<?php

namespace App\Services\Payment;

use App\Models\CustomerCharge;
use App\Models\CustomerLedger;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\ServiceApplication;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Models\User;
use App\Models\WaterBillHistory;
use App\Services\Charge\ApplicationChargeService;
use App\Services\Ledger\LedgerService;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        protected ApplicationChargeService $chargeService,
        protected LedgerService $ledgerService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Generate receipt number in format: OR-YYYY-NNNNN
     */
    public function generateReceiptNumber(): string
    {
        $year = date('Y');
        $prefix = "OR-{$year}-";

        // Get the last receipt number for this year
        $lastReceipt = Payment::where('receipt_no', 'like', "{$prefix}%")
            ->orderBy('receipt_no', 'desc')
            ->value('receipt_no');

        if ($lastReceipt) {
            $lastNumber = (int) substr($lastReceipt, -5);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix.str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Process full payment for application charges
     *
     * Creates Payment, PaymentAllocations, CustomerLedger entries
     * Auto-updates application status to PAID
     *
     * @param  int  $userId  The cashier processing the payment
     * @return array Contains 'payment', 'allocations', 'change'
     *
     * @throws \Exception
     */
    public function processApplicationPayment(int $applicationId, float $amountReceived, int $userId): array
    {
        $application = ServiceApplication::with('customer')->findOrFail($applicationId);

        // Validate application is in VERIFIED status
        if ($application->stat_id !== Status::getIdByDescription(Status::VERIFIED)) {
            throw new \Exception('Payment can only be processed for VERIFIED applications.');
        }

        // Get charges total
        $chargesData = $this->chargeService->getApplicationChargesTotal($applicationId);

        if ($chargesData['charges']->isEmpty()) {
            throw new \Exception('No charges found for this application.');
        }

        $totalDue = $chargesData['remaining_amount'];

        // Validate payment amount (full payment required)
        if ($amountReceived < $totalDue) {
            throw new \Exception('Full payment required. Amount due: ₱'.number_format($totalDue, 2).'. Received: ₱'.number_format($amountReceived, 2));
        }

        $change = $amountReceived - $totalDue;

        $result = DB::transaction(function () use ($application, $chargesData, $amountReceived, $totalDue, $change, $userId) {
            $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
            $paidStatusId = Status::getIdByDescription(Status::PAID);

            // Create Payment record
            $payment = Payment::create([
                'receipt_no' => $this->generateReceiptNumber(),
                'payer_id' => $application->customer_id,
                'payment_date' => now()->toDateString(),
                'amount_received' => $amountReceived,
                'created_at' => now(),
                'user_id' => $userId,
                'stat_id' => $activeStatusId,
            ]);

            $allocations = collect();

            // Create PaymentAllocation for each charge
            foreach ($chargesData['charges'] as $charge) {
                $remainingOnCharge = $charge->remaining_amount;

                if ($remainingOnCharge <= 0) {
                    continue; // Already paid
                }

                $allocation = PaymentAllocation::create([
                    'payment_id' => $payment->payment_id,
                    'target_type' => 'CHARGE',
                    'target_id' => $charge->charge_id,
                    'amount_applied' => $remainingOnCharge,
                    'period_id' => null,
                    'connection_id' => $charge->connection_id,
                ]);

                $allocations->push($allocation);

                // Create CREDIT entry in ledger
                $this->ledgerService->recordPaymentAllocation(
                    $allocation,
                    $payment,
                    "Payment for: {$charge->description}",
                    $userId
                );
            }

            // Mark charges as PAID
            $this->chargeService->markChargesAsPaid($application->application_id);

            // Mark CHARGE ledger entries as PAID
            $chargeIds = $chargesData['charges']->pluck('charge_id');
            CustomerLedger::where('source_type', 'CHARGE')
                ->whereIn('source_id', $chargeIds)
                ->update(['stat_id' => $paidStatusId]);

            // Update application status to PAID
            $application->update([
                'stat_id' => $paidStatusId,
                'paid_at' => now(),
                'payment_id' => $payment->payment_id,
            ]);

            return [
                'payment' => $payment->fresh(),
                'allocations' => $allocations,
                'total_paid' => $totalDue,
                'amount_received' => $amountReceived,
                'change' => $change,
            ];
        });

        $application->load('customer');
        $totalPaidFormatted = '₱'.number_format($result['total_paid'], 2);
        $this->notificationService->notifyApplicationPaid($application, $totalPaidFormatted, $userId);

        return $result;
    }

    /**
     * Get payment details with allocations
     */
    public function getPaymentDetails(int $paymentId): ?Payment
    {
        return Payment::with([
            'payer',
            'user',
            'status',
            'paymentAllocations',
        ])->find($paymentId);
    }

    /**
     * Get payments for a customer
     */
    public function getCustomerPayments(int $customerId)
    {
        return Payment::with(['status', 'user', 'paymentAllocations'])
            ->where('payer_id', $customerId)
            ->orderBy('payment_date', 'desc')
            ->get();
    }

    /**
     * Get water bill details for payment processing
     */
    public function getWaterBillDetails(int $billId): ?WaterBillHistory
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $overdueStatusId = Status::getIdByDescription(Status::OVERDUE);

        return WaterBillHistory::with([
            'serviceConnection.customer',
            'serviceConnection.address.purok',
            'serviceConnection.address.barangay',
            'serviceConnection.accountType',
            'period',
            'previousReading',
            'currentReading',
            'status',
        ])
            ->where('bill_id', $billId)
            ->whereIn('stat_id', array_filter([$activeStatusId, $overdueStatusId]))
            ->first();
    }

    /**
     * Get outstanding items for a single bill + its period-matched charges
     *
     * Used by the single-bill payment page when a cashier clicks a specific bill.
     * Returns only that bill and charges sharing the same period_id.
     */
    public function getBillOutstandingItems(int $billId): array
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $overdueStatusId = Status::getIdByDescription(Status::OVERDUE);

        $bill = WaterBillHistory::with(['period', 'status'])
            ->where('bill_id', $billId)
            ->whereIn('stat_id', array_filter([$activeStatusId, $overdueStatusId]))
            ->first();

        if (! $bill) {
            return [
                'bills' => collect(),
                'charges' => collect(),
                'bills_total' => 0,
                'charges_total' => 0,
                'grand_total' => 0,
            ];
        }

        $billMapped = collect([[
            'id' => $bill->bill_id,
            'type' => 'BILL',
            'description' => 'Water Bill - '.($bill->period?->per_name ?? 'Unknown Period'),
            'period_name' => $bill->period?->per_name ?? 'Unknown',
            'amount' => (float) $bill->remaining_amount,
            'original_amount' => (float) $bill->total_amount,
            'paid_amount' => (float) $bill->paid_amount,
            'due_date' => $bill->due_date?->format('M d, Y'),
            'is_overdue' => $bill->stat_id === $overdueStatusId,
            'is_partially_paid' => $bill->isPartiallyPaid(),
            'period_id' => $bill->period_id,
        ]]);

        // Get charges for the same connection AND same period
        $charges = CustomerCharge::select('CustomerCharge.*', 'CustomerLedger.period_id as ledger_period_id')
            ->leftJoin('CustomerLedger', function ($join) {
                $join->on('CustomerCharge.charge_id', '=', 'CustomerLedger.source_id')
                    ->where('CustomerLedger.source_type', '=', 'CHARGE');
            })
            ->where('CustomerCharge.connection_id', $bill->connection_id)
            ->whereNull('CustomerCharge.application_id')
            ->where('CustomerCharge.stat_id', $activeStatusId)
            ->where('CustomerLedger.period_id', $bill->period_id)
            ->orderBy('CustomerCharge.due_date', 'asc')
            ->get()
            ->unique('charge_id')
            ->filter(fn ($charge) => $charge->remaining_amount > 0)
            ->map(function ($charge) {
                return [
                    'id' => $charge->charge_id,
                    'type' => 'CHARGE',
                    'description' => $charge->description,
                    'period_name' => null,
                    'amount' => (float) $charge->remaining_amount,
                    'due_date' => $charge->due_date?->format('M d, Y'),
                    'is_overdue' => $charge->due_date?->isPast() ?? false,
                    'period_id' => $charge->ledger_period_id,
                ];
            })
            ->values();

        return [
            'bills' => $billMapped,
            'charges' => $charges,
            'bills_total' => $billMapped->sum('amount'),
            'charges_total' => $charges->sum('amount'),
            'grand_total' => $billMapped->sum('amount') + $charges->sum('amount'),
        ];
    }

    /**
     * Get all outstanding items for a connection (unpaid bills + unpaid charges)
     *
     * Used by the bulk payment page to show everything a customer owes.
     * Application fees are excluded (they have their own payment flow).
     */
    public function getConnectionOutstandingItems(int $connectionId): array
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $overdueStatusId = Status::getIdByDescription(Status::OVERDUE);

        // Unpaid water bills for this connection
        $bills = WaterBillHistory::with(['period', 'status'])
            ->where('connection_id', $connectionId)
            ->whereIn('stat_id', array_filter([$activeStatusId, $overdueStatusId]))
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($bill) use ($overdueStatusId) {
                return [
                    'id' => $bill->bill_id,
                    'type' => 'BILL',
                    'description' => 'Water Bill - '.($bill->period?->per_name ?? 'Unknown Period'),
                    'period_name' => $bill->period?->per_name ?? 'Unknown',
                    'amount' => (float) ($bill->water_amount + $bill->adjustment_total),
                    'due_date' => $bill->due_date?->format('M d, Y'),
                    'is_overdue' => $bill->stat_id === $overdueStatusId,
                    'period_id' => $bill->period_id,
                ];
            });

        // Unpaid charges for this connection (penalties, misc — NOT application fees)
        // LEFT JOIN CustomerLedger to get period_id for bill association
        $charges = CustomerCharge::select('CustomerCharge.*', 'CustomerLedger.period_id as ledger_period_id')
            ->leftJoin('CustomerLedger', function ($join) {
                $join->on('CustomerCharge.charge_id', '=', 'CustomerLedger.source_id')
                    ->where('CustomerLedger.source_type', '=', 'CHARGE');
            })
            ->where('CustomerCharge.connection_id', $connectionId)
            ->whereNull('CustomerCharge.application_id')
            ->where('CustomerCharge.stat_id', $activeStatusId)
            ->orderBy('CustomerCharge.due_date', 'asc')
            ->get()
            ->unique('charge_id')
            ->filter(fn ($charge) => $charge->remaining_amount > 0)
            ->map(function ($charge) {
                return [
                    'id' => $charge->charge_id,
                    'type' => 'CHARGE',
                    'description' => $charge->description,
                    'period_name' => null,
                    'amount' => (float) $charge->remaining_amount,
                    'due_date' => $charge->due_date?->format('M d, Y'),
                    'is_overdue' => $charge->due_date?->isPast() ?? false,
                    'period_id' => $charge->ledger_period_id,
                ];
            })
            ->values();

        return [
            'bills' => $bills,
            'charges' => $charges,
            'bills_total' => $bills->sum('amount'),
            'charges_total' => $charges->sum('amount'),
            'grand_total' => $bills->sum('amount') + $charges->sum('amount'),
        ];
    }

    /**
     * Process payment for a single water bill + its period-matched charges
     *
     * Supports partial payments. Allocation precedence: charges (penalties) first, then bill.
     * Items are only marked PAID when fully covered. Multiple partial payments allowed.
     *
     * @param  int  $billId  The water bill to pay
     * @param  float  $amountReceived  Amount received from customer
     * @param  int  $userId  The cashier processing the payment
     * @return array Contains 'payment', 'allocations', 'total_paid', 'total_due', 'remaining_balance', 'amount_received', 'change'
     *
     * @throws \Exception
     */
    public function processWaterBillPayment(int $billId, float $amountReceived, int $userId): array
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $overdueStatusId = Status::getIdByDescription(Status::OVERDUE);
        $paidStatusId = Status::getIdByDescription(Status::PAID);

        $customerForNotification = null;

        $result = DB::transaction(function () use (
            $billId, $amountReceived, $userId, $activeStatusId, $overdueStatusId, $paidStatusId, &$customerForNotification
        ) {
            $allocations = collect();

            // Find the bill with lock to prevent concurrent payments
            $bill = WaterBillHistory::with(['serviceConnection.customer', 'period'])
                ->where('bill_id', $billId)
                ->whereIn('stat_id', array_filter([$activeStatusId, $overdueStatusId]))
                ->lockForUpdate()
                ->first();

            if (! $bill) {
                throw new \Exception('Bill not found or already paid.');
            }

            if ($bill->period && $bill->period->is_closed) {
                throw new \Exception(
                    'Cannot process payment: The billing period "'
                    .($bill->period->per_name ?? 'Unknown')
                    .'" has been closed.'
                );
            }

            $connection = $bill->serviceConnection;
            $customer = $connection->customer;
            $customerForNotification = $customer;

            if (! $customer) {
                throw new \Exception('No customer associated with this connection.');
            }

            // Lock period-matched charges for this bill
            $charges = CustomerCharge::select('CustomerCharge.*', 'CustomerLedger.period_id as ledger_period_id')
                ->leftJoin('CustomerLedger', function ($join) {
                    $join->on('CustomerCharge.charge_id', '=', 'CustomerLedger.source_id')
                        ->where('CustomerLedger.source_type', '=', 'CHARGE');
                })
                ->where('CustomerCharge.connection_id', $connection->connection_id)
                ->whereNull('CustomerCharge.application_id')
                ->where('CustomerCharge.stat_id', $activeStatusId)
                ->where('CustomerLedger.period_id', $bill->period_id)
                ->lockForUpdate()
                ->get()
                ->unique('charge_id')
                ->filter(fn ($c) => $c->remaining_amount > 0);

            $billRemaining = $bill->remaining_amount;
            $chargesAmount = $charges->sum(fn ($c) => $c->remaining_amount);
            $totalDue = $billRemaining + $chargesAmount;

            // Track how much of the payment is left to allocate
            $remainingPayment = min($amountReceived, $totalDue);
            $change = max(0, $amountReceived - $totalDue);

            // Create Payment record
            $payment = Payment::create([
                'receipt_no' => $this->generateReceiptNumber(),
                'payer_id' => $customer->cust_id,
                'payment_date' => now()->toDateString(),
                'amount_received' => $amountReceived,
                'user_id' => $userId,
                'stat_id' => $activeStatusId,
            ]);

            // === ALLOCATION PHASE: Charges (penalties) FIRST, then bill ===

            // 1. Allocate to period-matched charges first
            foreach ($charges as $charge) {
                if ($remainingPayment <= 0) {
                    break;
                }

                $chargeRemaining = $charge->remaining_amount;
                $applyAmount = min($remainingPayment, $chargeRemaining);

                $chargeAllocation = PaymentAllocation::create([
                    'payment_id' => $payment->payment_id,
                    'target_type' => 'CHARGE',
                    'target_id' => $charge->charge_id,
                    'amount_applied' => $applyAmount,
                    'period_id' => $bill->period_id,
                    'connection_id' => $connection->connection_id,
                ]);
                $allocations->push($chargeAllocation);

                $this->ledgerService->recordPaymentAllocation(
                    $chargeAllocation,
                    $payment,
                    'Payment for: '.$charge->description,
                    $userId
                );

                // Mark charge as PAID only if fully covered
                if ($applyAmount >= $chargeRemaining) {
                    $charge->update(['stat_id' => $paidStatusId]);

                    CustomerLedger::where('source_type', 'CHARGE')
                        ->where('source_id', $charge->charge_id)
                        ->update(['stat_id' => $paidStatusId]);
                }

                $remainingPayment -= $applyAmount;
            }

            // 2. Allocate remainder to the bill
            if ($remainingPayment > 0) {
                $applyToBill = min($remainingPayment, $billRemaining);

                $billAllocation = PaymentAllocation::create([
                    'payment_id' => $payment->payment_id,
                    'target_type' => 'BILL',
                    'target_id' => $bill->bill_id,
                    'amount_applied' => $applyToBill,
                    'period_id' => $bill->period_id,
                    'connection_id' => $connection->connection_id,
                ]);
                $allocations->push($billAllocation);

                $this->ledgerService->recordPaymentAllocation(
                    $billAllocation,
                    $payment,
                    'Payment for Water Bill - '.($bill->period?->per_name ?? 'Unknown'),
                    $userId
                );

                // Mark bill as PAID only if fully covered
                if ($applyToBill >= $billRemaining) {
                    $bill->update(['stat_id' => $paidStatusId]);

                    CustomerLedger::where('source_type', 'BILL')
                        ->where('source_id', $bill->bill_id)
                        ->update(['stat_id' => $paidStatusId]);
                }

                $remainingPayment -= $applyToBill;
            }

            // Post-allocation check: mark bill as PAID if fully covered
            // Handles edge cases where bill remaining was already 0 (e.g. zero-amount bills,
            // or bill fully covered by prior partial payments with only charges outstanding)
            $bill->refresh();
            if ($bill->stat_id !== $paidStatusId && $bill->remaining_amount <= 0) {
                $bill->update(['stat_id' => $paidStatusId]);

                CustomerLedger::where('source_type', 'BILL')
                    ->where('source_id', $bill->bill_id)
                    ->update(['stat_id' => $paidStatusId]);
            }

            $totalApplied = $allocations->sum('amount_applied');

            return [
                'payment' => $payment->fresh(),
                'allocations' => $allocations,
                'total_paid' => $totalApplied,
                'total_due' => $totalDue,
                'remaining_balance' => $totalDue - $totalApplied,
                'amount_received' => $amountReceived,
                'change' => $change,
            ];
        });

        $customerName = $customerForNotification?->fullName ?? 'Unknown Customer';
        $amountFormatted = '₱'.number_format($result['total_paid'], 2);
        $this->notificationService->notifyPaymentProcessed($customerName, $amountFormatted, $result['payment']->receipt_no, $userId);

        return $result;
    }

    /**
     * Process bulk payment for a connection — multiple bills and/or charges
     *
     * Creates one Payment, multiple PaymentAllocations, multiple ledger CREDIT entries.
     * Marks each paid bill as PAID and each paid charge as PAID.
     *
     * Municipal ordinance requires full payment — ALL outstanding bills
     * and charges for the connection must be paid in a single transaction.
     *
     * @param  int  $connectionId  The service connection
     * @param  float  $amountReceived  Amount received from customer
     * @param  int  $userId  The cashier processing the payment
     * @return array Contains 'payment', 'allocations', 'total_paid', 'amount_received', 'change'
     *
     * @throws \Exception
     */
    public function processConnectionPayment(
        int $connectionId,
        float $amountReceived,
        int $userId,
    ): array {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $overdueStatusId = Status::getIdByDescription(Status::OVERDUE);
        $paidStatusId = Status::getIdByDescription(Status::PAID);

        $customerForNotification = null;

        $result = DB::transaction(function () use (
            $connectionId, $amountReceived, $userId,
            $activeStatusId, $overdueStatusId, $paidStatusId, &$customerForNotification
        ) {
            $totalDue = 0;
            $allocations = collect();

            // --- Lock ALL outstanding bills for this connection ---
            $bills = WaterBillHistory::with(['serviceConnection.customer', 'period'])
                ->where('connection_id', $connectionId)
                ->whereIn('stat_id', array_filter([$activeStatusId, $overdueStatusId]))
                ->lockForUpdate()
                ->get();

            // Check no closed periods
            foreach ($bills as $bill) {
                if ($bill->period && $bill->period->is_closed) {
                    throw new \Exception(
                        'Cannot process payment: The billing period "'
                        .($bill->period->per_name ?? 'Unknown')
                        .'" has been closed.'
                    );
                }
            }

            $totalDue += $bills->sum(fn ($b) => $b->water_amount + $b->adjustment_total);

            // --- Lock ALL outstanding charges for this connection ---
            $charges = CustomerCharge::where('connection_id', $connectionId)
                ->whereNull('application_id')
                ->where('stat_id', $activeStatusId)
                ->lockForUpdate()
                ->get()
                ->filter(fn ($c) => $c->remaining_amount > 0);

            $totalDue += $charges->sum(fn ($c) => $c->remaining_amount);

            // Pre-fetch period_ids from ledger for charge allocations
            $chargePeriodMap = CustomerLedger::where('source_type', 'CHARGE')
                ->whereIn('source_id', $charges->pluck('charge_id'))
                ->pluck('period_id', 'source_id');

            if ($bills->isEmpty() && $charges->isEmpty()) {
                throw new \Exception('No outstanding bills or charges found for this connection.');
            }

            // --- Validate payment amount ---
            if ($amountReceived < $totalDue) {
                throw new \Exception(
                    'Insufficient payment. Total due: ₱'.number_format($totalDue, 2)
                    .'. Received: ₱'.number_format($amountReceived, 2)
                );
            }

            $change = $amountReceived - $totalDue;

            // Get customer from connection
            $connection = $bills->first()?->serviceConnection
                ?? ServiceConnection::with('customer')->findOrFail($connectionId);
            $customer = $connection->customer;
            $customerForNotification = $customer;

            if (! $customer) {
                throw new \Exception('No customer associated with this connection.');
            }

            // --- Create Payment record ---
            $payment = Payment::create([
                'receipt_no' => $this->generateReceiptNumber(),
                'payer_id' => $customer->cust_id,
                'payment_date' => now()->toDateString(),
                'amount_received' => $amountReceived,
                'user_id' => $userId,
                'stat_id' => $activeStatusId,
            ]);

            // --- Create allocations for bills ---
            foreach ($bills as $bill) {
                $billAmount = $bill->water_amount + $bill->adjustment_total;

                $allocation = PaymentAllocation::create([
                    'payment_id' => $payment->payment_id,
                    'target_type' => 'BILL',
                    'target_id' => $bill->bill_id,
                    'amount_applied' => $billAmount,
                    'period_id' => $bill->period_id,
                    'connection_id' => $connectionId,
                ]);

                $allocations->push($allocation);

                $this->ledgerService->recordPaymentAllocation(
                    $allocation,
                    $payment,
                    'Payment for Water Bill - '.($bill->period?->per_name ?? 'Unknown'),
                    $userId
                );

                $bill->update(['stat_id' => $paidStatusId]);

                // Mark BILL ledger entry as PAID
                CustomerLedger::where('source_type', 'BILL')
                    ->where('source_id', $bill->bill_id)
                    ->update(['stat_id' => $paidStatusId]);
            }

            // --- Create allocations for charges ---
            foreach ($charges as $charge) {
                $chargeAmount = $charge->remaining_amount;

                $allocation = PaymentAllocation::create([
                    'payment_id' => $payment->payment_id,
                    'target_type' => 'CHARGE',
                    'target_id' => $charge->charge_id,
                    'amount_applied' => $chargeAmount,
                    'period_id' => $chargePeriodMap->get($charge->charge_id),
                    'connection_id' => $connectionId,
                ]);

                $allocations->push($allocation);

                $this->ledgerService->recordPaymentAllocation(
                    $allocation,
                    $payment,
                    'Payment for: '.$charge->description,
                    $userId
                );

                $charge->update(['stat_id' => $paidStatusId]);

                // Mark CHARGE ledger entry as PAID
                CustomerLedger::where('source_type', 'CHARGE')
                    ->where('source_id', $charge->charge_id)
                    ->update(['stat_id' => $paidStatusId]);
            }

            return [
                'payment' => $payment->fresh(),
                'allocations' => $allocations,
                'total_paid' => $totalDue,
                'amount_received' => $amountReceived,
                'change' => $change,
            ];
        });

        $customerName = $customerForNotification?->fullName ?? 'Unknown Customer';
        $amountFormatted = '₱'.number_format($result['total_paid'], 2);
        $this->notificationService->notifyPaymentProcessed($customerName, $amountFormatted, $result['payment']->receipt_no, $userId);

        return $result;
    }

    /**
     * Cancel a payment and reverse all ledger entries
     *
     * - Marks Payment as CANCELLED with metadata
     * - Marks all PaymentAllocations as CANCELLED
     * - Marks original PAYMENT ledger entries as CANCELLED
     * - Creates REVERSAL DEBIT entries in CustomerLedger
     * - Reverts bill/charge statuses to ACTIVE or OVERDUE
     *
     * @param  int  $paymentId  The payment to cancel
     * @param  string  $reason  Required cancellation reason
     * @param  int  $userId  The admin/staff performing the cancellation
     * @return array Contains 'success', 'message', 'data'
     *
     * @throws \Exception
     */
    public function cancelPayment(int $paymentId, string $reason, int $userId): array
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $cancelledStatusId = Status::getIdByDescription(Status::CANCELLED);
        $overdueStatusId = Status::getIdByDescription(Status::OVERDUE);

        $result = DB::transaction(function () use (
            $paymentId, $reason, $userId,
            $activeStatusId, $cancelledStatusId, $overdueStatusId
        ) {
            // Lock the payment to prevent concurrent operations
            $payment = Payment::with('paymentAllocations.period')
                ->where('payment_id', $paymentId)
                ->lockForUpdate()
                ->first();

            if (! $payment) {
                throw new \Exception('Payment not found.');
            }

            if ($payment->stat_id === $cancelledStatusId) {
                throw new \Exception('Payment is already cancelled.');
            }

            if ($payment->stat_id !== $activeStatusId) {
                throw new \Exception('Only active payments can be cancelled.');
            }

            // Guard: reject cancellation if any allocation belongs to a closed period
            foreach ($payment->paymentAllocations as $allocation) {
                if ($allocation->period && $allocation->period->is_closed) {
                    throw new \Exception(
                        'Cannot cancel payment: The billing period "'
                        .($allocation->period->per_name ?? 'Unknown')
                        .'" has been closed.'
                    );
                }
            }

            // 1. Mark Payment as CANCELLED
            $payment->update([
                'stat_id' => $cancelledStatusId,
                'cancelled_at' => now(),
                'cancelled_by' => $userId,
                'cancellation_reason' => $reason,
            ]);

            // 2. Process each allocation
            foreach ($payment->paymentAllocations as $allocation) {
                // Mark allocation as CANCELLED
                $allocation->update(['stat_id' => $cancelledStatusId]);

                // Find and mark original PAYMENT ledger entry as CANCELLED
                CustomerLedger::where('source_type', 'PAYMENT')
                    ->where('source_id', $payment->payment_id)
                    ->where('source_line_no', $allocation->payment_allocation_id)
                    ->update(['stat_id' => $cancelledStatusId]);

                // Create REVERSAL DEBIT entry
                $description = 'CANCELLED: '.$this->getReversalDescription($allocation);
                if ($reason) {
                    $description .= ' (Reason: '.$reason.')';
                }

                $this->ledgerService->recordPaymentReversal(
                    $allocation,
                    $payment,
                    $description,
                    $userId
                );

                // Revert target bill/charge status
                $this->revertTargetStatus($allocation, $activeStatusId, $overdueStatusId);

                // Revert the original BILL/CHARGE ledger entry back to ACTIVE
                CustomerLedger::where('source_type', $allocation->target_type)
                    ->where('source_id', $allocation->target_id)
                    ->update(['stat_id' => $activeStatusId]);
            }

            // 3. Handle application payment link (if this was an application payment)
            $this->revertApplicationPayment($payment, $activeStatusId);

            return [
                'success' => true,
                'message' => 'Payment cancelled successfully.',
                'data' => [
                    'payment_id' => $paymentId,
                    'receipt_no' => $payment->receipt_no,
                    'amount' => $payment->amount_received,
                    'allocations_reversed' => $payment->paymentAllocations->count(),
                    'cancelled_by' => $userId,
                    'cancelled_at' => $payment->cancelled_at,
                ],
            ];
        });

        $cancelledByName = User::find($userId)?->name ?? 'Unknown';
        $amountFormatted = '₱'.number_format($result['data']['amount'], 2);
        $this->notificationService->notifyPaymentCancelled($result['data']['receipt_no'], $amountFormatted, $cancelledByName, $userId);

        return $result;
    }

    /**
     * Build reversal description from allocation target
     */
    protected function getReversalDescription(PaymentAllocation $allocation): string
    {
        if ($allocation->target_type === 'BILL') {
            $bill = WaterBillHistory::with('period')->find($allocation->target_id);

            return 'Water Bill - '.($bill?->period?->per_name ?? 'Unknown Period');
        }

        if ($allocation->target_type === 'CHARGE') {
            $charge = CustomerCharge::find($allocation->target_id);

            return $charge?->description ?? 'Charge';
        }

        return 'Payment';
    }

    /**
     * Revert bill or charge status after cancellation
     */
    protected function revertTargetStatus(
        PaymentAllocation $allocation,
        int $activeStatusId,
        int $overdueStatusId
    ): void {
        if ($allocation->target_type === 'BILL') {
            $bill = WaterBillHistory::find($allocation->target_id);
            if ($bill) {
                // Determine if bill should be OVERDUE based on due_date
                $newStatusId = ($bill->due_date && $bill->due_date->isPast())
                    ? $overdueStatusId
                    : $activeStatusId;
                $bill->update(['stat_id' => $newStatusId]);
            }
        } elseif ($allocation->target_type === 'CHARGE') {
            CustomerCharge::where('charge_id', $allocation->target_id)
                ->update(['stat_id' => $activeStatusId]);
        }
    }

    /**
     * If this payment was linked to a ServiceApplication, revert the application status
     */
    protected function revertApplicationPayment(
        Payment $payment,
        int $activeStatusId
    ): void {
        $application = ServiceApplication::where('payment_id', $payment->payment_id)->first();

        if ($application) {
            // Revert application from PAID back to VERIFIED
            $verifiedStatusId = Status::getIdByDescription(Status::VERIFIED);
            $application->update([
                'stat_id' => $verifiedStatusId,
                'paid_at' => null,
                'payment_id' => null,
            ]);

            // Revert charges back to ACTIVE
            $chargeIds = CustomerCharge::where('application_id', $application->application_id)
                ->where('stat_id', Status::getIdByDescription(Status::PAID))
                ->pluck('charge_id');

            CustomerCharge::whereIn('charge_id', $chargeIds)
                ->update(['stat_id' => $activeStatusId]);

            // Revert corresponding CHARGE ledger entries back to ACTIVE
            CustomerLedger::where('source_type', 'CHARGE')
                ->whereIn('source_id', $chargeIds)
                ->update(['stat_id' => $activeStatusId]);
        }
    }
}
