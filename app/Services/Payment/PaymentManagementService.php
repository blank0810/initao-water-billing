<?php

namespace App\Services\Payment;

use App\Models\CustomerCharge;
use App\Models\CustomerLedger;
use App\Models\Payment;
use App\Models\ServiceApplication;
use App\Models\Status;
use App\Models\WaterBillHistory;
use Illuminate\Support\Collection;

class PaymentManagementService
{
    /**
     * Payment type constants
     */
    public const TYPE_APPLICATION_FEE = 'APPLICATION_FEE';

    public const TYPE_WATER_BILL = 'WATER_BILL';

    public const TYPE_OTHER_CHARGE = 'OTHER_CHARGE';

    /**
     * Get all pending payments across different sources
     *
     * Returns a unified list of items awaiting payment
     */
    public function getPendingPayments(?string $type = null, ?string $search = null): Collection
    {
        $payments = collect();

        // Get pending application fees (VERIFIED status, not yet paid)
        if (! $type || $type === self::TYPE_APPLICATION_FEE) {
            $applicationPayments = $this->getPendingApplicationFees($search);
            $payments = $payments->merge($applicationPayments);
        }

        // Get pending water bills (ACTIVE or OVERDUE status)
        if (! $type || $type === self::TYPE_WATER_BILL) {
            $billPayments = $this->getPendingWaterBills($search);
            $payments = $payments->merge($billPayments);
        }

        // Sort by date (oldest first)
        return $payments->sortBy('date')->values();
    }

    /**
     * Get pending application fees (VERIFIED, awaiting payment)
     */
    protected function getPendingApplicationFees(?string $search = null): Collection
    {
        $verifiedStatusId = Status::getIdByDescription(Status::VERIFIED);

        $query = ServiceApplication::with([
            'customer',
            'address.purok',
            'address.barangay',
            'customerCharges',
        ])
            ->where('stat_id', $verifiedStatusId)
            ->whereNull('payment_id'); // Not yet paid

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('application_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('cust_first_name', 'like', "%{$search}%")
                            ->orWhere('cust_last_name', 'like', "%{$search}%")
                            ->orWhere('resolution_no', 'like', "%{$search}%");
                    });
            });
        }

        return $query->get()->map(function ($application) {
            $totalAmount = $application->customerCharges->sum(fn ($c) => $c->total_amount);

            return [
                'id' => $application->application_id,
                'type' => self::TYPE_APPLICATION_FEE,
                'type_label' => 'Application Fee',
                'reference_number' => $application->application_number,
                'customer_id' => $application->customer_id,
                'customer_name' => $this->formatCustomerName($application->customer),
                'customer_code' => $application->customer->resolution_no ?? '-',
                'address' => $this->formatAddress($application->address),
                'amount' => $totalAmount,
                'amount_formatted' => '₱ '.number_format($totalAmount, 2),
                'date' => $application->verified_at ?? $application->submitted_at,
                'date_formatted' => ($application->verified_at ?? $application->submitted_at)?->format('M d, Y'),
                'status' => 'Pending Payment',
                'status_color' => 'yellow',
                'action_url' => route('payment.process.application', $application->application_id),
                'process_url' => route('payment.process.application', $application->application_id),
                'print_url' => route('service.application.order-of-payment', $application->application_id),
            ];
        });
    }

    /**
     * Get pending water bills (ACTIVE or OVERDUE status)
     * Includes unpaid charges count/total per connection for queue display
     */
    protected function getPendingWaterBills(?string $search = null): Collection
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $overdueStatusId = Status::getIdByDescription(Status::OVERDUE);

        $query = WaterBillHistory::with([
            'serviceConnection.customer',
            'serviceConnection.address.purok',
            'serviceConnection.address.barangay',
            'period',
            'status',
        ])
            ->whereIn('stat_id', array_filter([$activeStatusId, $overdueStatusId]));

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('period', function ($pq) use ($search) {
                    $pq->where('per_name', 'like', "%{$search}%");
                })
                    ->orWhereHas('serviceConnection', function ($cq) use ($search) {
                        $cq->where('account_no', 'like', "%{$search}%");
                    })
                    ->orWhereHas('serviceConnection.customer', function ($custQ) use ($search) {
                        $custQ->where('cust_first_name', 'like', "%{$search}%")
                            ->orWhere('cust_last_name', 'like', "%{$search}%")
                            ->orWhere('resolution_no', 'like', "%{$search}%");
                    });
            });
        }

        $overdueId = $overdueStatusId;

        // Pre-fetch unpaid charges grouped by (connection_id, period_id) to avoid N+1
        // JOIN CustomerLedger to get period_id for each charge — associates charges with specific bills
        $bills = $query->orderBy('due_date', 'asc')->get();
        $connectionIds = $bills->pluck('connection_id')->unique()->values();

        $chargesByBill = CustomerCharge::select('CustomerCharge.*', 'CustomerLedger.period_id as ledger_period_id')
            ->leftJoin('CustomerLedger', function ($join) {
                $join->on('CustomerCharge.charge_id', '=', 'CustomerLedger.source_id')
                    ->where('CustomerLedger.source_type', '=', 'CHARGE');
            })
            ->where('CustomerCharge.stat_id', $activeStatusId)
            ->whereNull('CustomerCharge.application_id')
            ->whereIn('CustomerCharge.connection_id', $connectionIds)
            ->get()
            ->unique('charge_id')
            ->groupBy(function ($charge) {
                $periodKey = $charge->ledger_period_id ?? 'null';

                return "{$charge->connection_id}-{$periodKey}";
            })
            ->map(function ($charges) {
                $unpaid = $charges->filter(fn ($c) => $c->remaining_amount > 0);

                return [
                    'count' => $unpaid->count(),
                    'total' => $unpaid->sum(fn ($c) => $c->remaining_amount),
                ];
            });

        // Find the oldest bill per connection to attach null-period charges
        $oldestBillPerConnection = $bills->groupBy('connection_id')
            ->map(fn ($connBills) => $connBills->sortBy('due_date')->first());

        return $bills->map(function ($bill) use ($overdueId, $chargesByBill, $oldestBillPerConnection) {
            $connection = $bill->serviceConnection;
            $customer = $connection?->customer;
            $totalAmount = $bill->remaining_amount;
            $originalAmount = $bill->total_amount;
            $isPartiallyPaid = $bill->isPartiallyPaid();
            $isOverdue = $bill->stat_id === $overdueId;

            // Get charges for this specific bill (by connection_id + period_id)
            $billKey = "{$bill->connection_id}-{$bill->period_id}";
            $connCharges = $chargesByBill->get($billKey, ['count' => 0, 'total' => 0]);

            // Attach null-period charges to the oldest bill for this connection
            $oldestBill = $oldestBillPerConnection->get($bill->connection_id);
            if ($oldestBill && $oldestBill->bill_id === $bill->bill_id) {
                $nullKey = "{$bill->connection_id}-null";
                $nullCharges = $chargesByBill->get($nullKey, ['count' => 0, 'total' => 0]);
                $connCharges = [
                    'count' => $connCharges['count'] + $nullCharges['count'],
                    'total' => $connCharges['total'] + $nullCharges['total'],
                ];
            }

            return [
                'id' => $bill->bill_id,
                'type' => self::TYPE_WATER_BILL,
                'type_label' => 'Water Bill',
                'reference_number' => $bill->period?->per_name ?? 'Unknown Period',
                'customer_id' => $customer?->cust_id,
                'customer_name' => $this->formatCustomerName($customer),
                'customer_code' => $customer?->resolution_no ?? '-',
                'address' => $this->formatAddress($connection?->address),
                'amount' => $totalAmount,
                'amount_formatted' => '₱ '.number_format($totalAmount, 2),
                'original_amount' => $originalAmount,
                'original_amount_formatted' => '₱ '.number_format($originalAmount, 2),
                'is_partially_paid' => $isPartiallyPaid,
                'charges_count' => $connCharges['count'],
                'charges_total' => $connCharges['total'],
                'charges_total_formatted' => $connCharges['total'] > 0 ? '₱ '.number_format($connCharges['total'], 2) : null,
                'date' => $bill->due_date,
                'date_formatted' => $bill->due_date?->format('M d, Y'),
                'status' => $isPartiallyPaid ? 'Partially Paid' : ($isOverdue ? 'Overdue' : 'Pending Payment'),
                'status_color' => $isPartiallyPaid ? 'blue' : ($isOverdue ? 'red' : 'yellow'),
                'action_url' => route('payment.process.bill', $bill->bill_id),
                'process_url' => route('payment.process.bill', $bill->bill_id),
                'print_url' => null,
            ];
        });
    }

    /**
     * Get payment queue statistics
     */
    public function getStatistics(): array
    {
        $verifiedStatusId = Status::getIdByDescription(Status::VERIFIED);
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $overdueStatusId = Status::getIdByDescription(Status::OVERDUE);

        // Pending application fees
        $pendingApplications = ServiceApplication::with('customerCharges')
            ->where('stat_id', $verifiedStatusId)
            ->whereNull('payment_id')
            ->get();

        $totalPendingApps = $pendingApplications->sum(function ($app) {
            return $app->customerCharges->sum(fn ($c) => $c->total_amount);
        });
        $pendingAppCount = $pendingApplications->count();

        // Pending water bills
        $pendingBills = WaterBillHistory::whereIn('stat_id', array_filter([$activeStatusId, $overdueStatusId]))->get();
        $totalPendingBills = $pendingBills->sum(fn ($b) => $b->remaining_amount);
        $pendingBillCount = $pendingBills->count();

        // Combined totals
        $totalPending = $totalPendingApps + $totalPendingBills;
        $pendingCount = $pendingAppCount + $pendingBillCount;

        // Today's collections (exclude cancelled)
        $cancelledStatusId = Status::getIdByDescription(Status::CANCELLED);
        $todayPayments = Payment::whereDate('payment_date', today())
            ->where('stat_id', '!=', $cancelledStatusId)
            ->get();
        $todayCollection = $todayPayments->sum('amount_received');
        $todayCount = $todayPayments->count();

        // This month's collections (exclude cancelled)
        $monthPayments = Payment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->where('stat_id', '!=', $cancelledStatusId)
            ->get();
        $monthCollection = $monthPayments->sum('amount_received');

        // Total transactions (all time, exclude cancelled)
        $totalTransactions = Payment::where('stat_id', '!=', $cancelledStatusId)->count();

        // Average payment (all time, exclude cancelled)
        $averagePayment = $totalTransactions > 0
            ? Payment::where('stat_id', '!=', $cancelledStatusId)->avg('amount_received')
            : 0;

        return [
            'pending_amount' => $totalPending,
            'pending_amount_formatted' => '₱ '.number_format($totalPending, 2),
            'pending_count' => $pendingCount,
            'today_collection' => $todayCollection,
            'today_collection_formatted' => '₱ '.number_format($todayCollection, 2),
            'today_count' => $todayCount,
            'month_collection' => $monthCollection,
            'month_collection_formatted' => '₱ '.number_format($monthCollection, 2),
            'total_transactions' => $totalTransactions,
            'average_payment' => round($averagePayment, 2),
            'average_payment_formatted' => '₱ '.number_format($averagePayment, 2),
        ];
    }

    /**
     * Get collections data for server-side DataTables
     *
     * Handles search, sorting, pagination, and custom filters.
     * Returns DataTables-compatible JSON response array.
     */
    public function getCollections(array $params): array
    {
        $draw = (int) ($params['draw'] ?? 1);
        $start = (int) ($params['start'] ?? 0);
        $length = (int) ($params['length'] ?? 10);
        $searchValue = $params['search']['value'] ?? '';
        $orderColumnIndex = (int) ($params['order'][0]['column'] ?? 0);
        $orderDir = ($params['order'][0]['dir'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        // Custom filters
        $statusFilter = $params['status'] ?? 'all';
        $dateFrom = $params['date_from'] ?? null;
        $dateTo = $params['date_to'] ?? null;

        // Column mapping for sorting
        $columns = [
            0 => 'receipt_no',
            1 => 'payment_date',
            2 => 'payer_id',
            3 => 'amount_received',
            4 => 'user_id',
            5 => 'stat_id',
        ];
        $orderColumn = $columns[$orderColumnIndex] ?? 'payment_date';

        // Base query with eager loading
        $query = Payment::with(['payer', 'user', 'status']);

        // Status filter
        if ($statusFilter !== 'all') {
            $statusId = Status::getIdByDescription(strtoupper($statusFilter));
            if ($statusId) {
                $query->where('stat_id', $statusId);
            }
        }

        // Date range filter
        if ($dateFrom) {
            $query->whereDate('payment_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('payment_date', '<=', $dateTo);
        }

        // Search filter (receipt no, customer name)
        if ($searchValue) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('receipt_no', 'like', "%{$searchValue}%")
                    ->orWhere('amount_received', 'like', "%{$searchValue}%")
                    ->orWhereHas('payer', function ($cq) use ($searchValue) {
                        $cq->where('cust_first_name', 'like', "%{$searchValue}%")
                            ->orWhere('cust_last_name', 'like', "%{$searchValue}%")
                            ->orWhere('resolution_no', 'like', "%{$searchValue}%");
                    })
                    ->orWhereHas('user', function ($uq) use ($searchValue) {
                        $uq->where('name', 'like', "%{$searchValue}%");
                    });
            });
        }

        // Total records (unfiltered)
        $recordsTotal = Payment::count();

        // Filtered records count
        $recordsFiltered = $query->count();

        // Sorting — for customer/cashier columns, sort by related table via subquery
        if ($orderColumn === 'payer_id') {
            $query->orderBy(
                \App\Models\Customer::select('cust_last_name')
                    ->whereColumn('customer.cust_id', 'Payment.payer_id')
                    ->limit(1),
                $orderDir
            );
        } elseif ($orderColumn === 'user_id') {
            $query->orderBy(
                \App\Models\User::select('name')
                    ->whereColumn('users.id', 'Payment.user_id')
                    ->limit(1),
                $orderDir
            );
        } else {
            $query->orderBy($orderColumn, $orderDir);
        }

        // Pagination
        $payments = $query->skip($start)->take($length)->get();

        // Format data for DataTables
        $data = $payments->map(function ($payment) {
            $isCancelled = $payment->status?->stat_desc === 'CANCELLED';

            return [
                'payment_id' => $payment->payment_id,
                'receipt_no' => $payment->receipt_no,
                'payment_date' => $payment->payment_date?->format('M d, Y'),
                'payment_date_raw' => $payment->payment_date?->format('Y-m-d'),
                'consumer_name' => $this->formatCustomerName($payment->payer),
                'amount_received' => (float) $payment->amount_received,
                'amount_formatted' => '₱ '.number_format($payment->amount_received, 2),
                'cashier' => $payment->user?->name ?? '-',
                'status' => $isCancelled ? 'Cancelled' : 'Active',
                'status_raw' => $payment->status?->stat_desc ?? 'UNKNOWN',
                'is_cancelled' => $isCancelled,
                'receipt_url' => route('payment.receipt', $payment->payment_id),
                'cancelled_at' => $payment->cancelled_at?->format('M d, Y g:i A'),
                'cancelled_by_name' => $payment->cancelledBy?->name ?? null,
                'cancellation_reason' => $payment->cancellation_reason,
            ];
        });

        return [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data->values()->toArray(),
        ];
    }

    /**
     * Get payment type options for filter dropdown
     */
    public function getPaymentTypes(): array
    {
        return [
            ['value' => '', 'label' => 'All Types'],
            ['value' => self::TYPE_APPLICATION_FEE, 'label' => 'Application Fee'],
            ['value' => self::TYPE_WATER_BILL, 'label' => 'Water Bill'],
        ];
    }

    /**
     * Get transactions processed by a specific cashier for a given date
     * Includes cancelled payments with visual distinction
     */
    public function getCashierTransactions(int $userId, ?string $date = null): array
    {
        $targetDate = $date ? \Carbon\Carbon::parse($date) : today();
        $cancelledStatusId = Status::getIdByDescription(Status::CANCELLED);

        $payments = Payment::with(['payer', 'status', 'cancelledBy'])
            ->where('user_id', $userId)
            ->whereDate('payment_date', $targetDate)
            ->orderBy('created_at', 'desc')
            ->get();

        // Pre-fetch all application payment IDs in one query to avoid N+1
        $applicationPaymentIds = ServiceApplication::whereIn(
            'payment_id',
            $payments->pluck('payment_id')
        )->pluck('payment_id')->flip();

        // Split active and cancelled payments for summary
        $activePayments = $payments->filter(fn ($p) => $p->stat_id !== $cancelledStatusId);
        $cancelledPayments = $payments->filter(fn ($p) => $p->stat_id === $cancelledStatusId);

        // Calculate summary statistics (exclude cancelled from total)
        $totalCollected = $activePayments->sum('amount_received');
        $transactionCount = $payments->count();
        $cancelledAmount = $cancelledPayments->sum('amount_received');
        $cancelledCount = $cancelledPayments->count();

        // Group by payment type (active only)
        $byType = $this->groupPaymentsByType($activePayments, $applicationPaymentIds);

        // Format transactions for display
        $transactions = $payments->map(function ($payment) use ($applicationPaymentIds, $cancelledStatusId) {
            $paymentType = $this->getPaymentType($payment, $applicationPaymentIds);
            $isCancelled = $payment->stat_id === $cancelledStatusId;

            $tx = [
                'payment_id' => $payment->payment_id,
                'receipt_no' => $payment->receipt_no,
                'customer_name' => $this->formatCustomerName($payment->payer),
                'customer_code' => $payment->payer->resolution_no ?? '-',
                'payment_type' => $paymentType,
                'payment_type_label' => $this->getPaymentTypeLabelFromType($paymentType),
                'amount' => $payment->amount_received,
                'amount_formatted' => '₱ '.number_format($payment->amount_received, 2),
                'time' => $payment->created_at->format('g:i A'),
                'receipt_url' => route('payment.receipt', $payment->payment_id),
                'is_cancelled' => $isCancelled,
                'status' => $isCancelled ? 'CANCELLED' : 'ACTIVE',
            ];

            if ($isCancelled) {
                $tx['cancelled_at'] = $payment->cancelled_at?->format('M d, Y g:i A');
                $tx['cancelled_by_name'] = $payment->cancelledBy?->name ?? 'Unknown';
                $tx['cancellation_reason'] = $payment->cancellation_reason;
            }

            return $tx;
        });

        return [
            'date' => $targetDate->format('Y-m-d'),
            'date_display' => $targetDate->format('F j, Y'),
            'summary' => [
                'total_collected' => $totalCollected,
                'total_collected_formatted' => '₱ '.number_format($totalCollected, 2),
                'transaction_count' => $transactionCount,
                'cancelled_amount' => $cancelledAmount,
                'cancelled_amount_formatted' => '₱ '.number_format($cancelledAmount, 2),
                'cancelled_count' => $cancelledCount,
                'by_type' => $byType,
            ],
            'transactions' => $transactions,
        ];
    }

    /**
     * Group payments by type for summary breakdown
     */
    protected function groupPaymentsByType($payments, $applicationPaymentIds): array
    {
        $grouped = [];

        foreach ($payments as $payment) {
            $paymentType = $this->getPaymentType($payment, $applicationPaymentIds);
            $typeLabel = $this->getPaymentTypeLabelFromType($paymentType);
            if (! isset($grouped[$typeLabel])) {
                $grouped[$typeLabel] = 0;
            }
            $grouped[$typeLabel] += $payment->amount_received;
        }

        // Format for display
        $result = [];
        foreach ($grouped as $type => $amount) {
            $result[] = [
                'type' => $type,
                'amount' => $amount,
                'amount_formatted' => '₱ '.number_format($amount, 2),
            ];
        }

        return $result;
    }

    /**
     * Determine payment type from payment record using pre-fetched lookup
     */
    protected function getPaymentType(Payment $payment, $applicationPaymentIds): string
    {
        // Check if payment is linked to a service application (using in-memory lookup)
        if (isset($applicationPaymentIds[$payment->payment_id])) {
            return self::TYPE_APPLICATION_FEE;
        }

        // Check if payment has allocations to water bills
        $hasBillAllocation = $payment->paymentAllocations()
            ->where('target_type', 'BILL')
            ->exists();

        if ($hasBillAllocation) {
            return self::TYPE_WATER_BILL;
        }

        return self::TYPE_OTHER_CHARGE;
    }

    /**
     * Get human-readable payment type label from type constant
     */
    protected function getPaymentTypeLabelFromType(string $type): string
    {
        return match ($type) {
            self::TYPE_APPLICATION_FEE => 'Application Fee',
            self::TYPE_WATER_BILL => 'Water Bill',
            self::TYPE_OTHER_CHARGE => 'Other Charges',
            default => 'Other',
        };
    }

    /**
     * Format customer full name
     */
    protected function formatCustomerName($customer): string
    {
        if (! $customer) {
            return '-';
        }

        $parts = array_filter([
            $customer->cust_first_name ?? '',
            $customer->cust_middle_name ? substr($customer->cust_middle_name, 0, 1).'.' : '',
            $customer->cust_last_name ?? '',
        ]);

        return implode(' ', $parts) ?: '-';
    }

    /**
     * Format address
     */
    protected function formatAddress($address): string
    {
        if (! $address) {
            return '-';
        }

        $parts = array_filter([
            $address->purok->purok_name ?? '',
            $address->barangay->b_name ?? '',
            'Initao',
        ]);

        return implode(', ', $parts) ?: '-';
    }
}
