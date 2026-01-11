<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\ServiceApplication;
use App\Models\Status;
use App\Services\Charge\ApplicationChargeService;
use App\Services\Ledger\LedgerService;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        protected ApplicationChargeService $chargeService,
        protected LedgerService $ledgerService
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

        return DB::transaction(function () use ($application, $chargesData, $amountReceived, $totalDue, $change, $userId) {
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
}
