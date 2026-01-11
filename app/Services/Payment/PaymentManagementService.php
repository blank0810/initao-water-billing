<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\ServiceApplication;
use App\Models\Status;
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

        // Future: Get pending water bills
        // if (!$type || $type === self::TYPE_WATER_BILL) {
        //     $billPayments = $this->getPendingWaterBills($search);
        //     $payments = $payments->merge($billPayments);
        // }

        // Sort by date (newest first)
        return $payments->sortByDesc('date')->values();
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
     * Get payment queue statistics
     */
    public function getStatistics(): array
    {
        $verifiedStatusId = Status::getIdByDescription(Status::VERIFIED);
        $paidStatusId = Status::getIdByDescription(Status::PAID);

        // Pending application fees
        $pendingApplications = ServiceApplication::with('customerCharges')
            ->where('stat_id', $verifiedStatusId)
            ->whereNull('payment_id')
            ->get();

        $totalPending = $pendingApplications->sum(function ($app) {
            return $app->customerCharges->sum(fn ($c) => $c->total_amount);
        });

        $pendingCount = $pendingApplications->count();

        // Today's collections
        $todayPayments = Payment::whereDate('payment_date', today())->get();
        $todayCollection = $todayPayments->sum('amount_received');
        $todayCount = $todayPayments->count();

        // This month's collections
        $monthPayments = Payment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->get();
        $monthCollection = $monthPayments->sum('amount_received');

        return [
            'pending_amount' => $totalPending,
            'pending_amount_formatted' => '₱ '.number_format($totalPending, 2),
            'pending_count' => $pendingCount,
            'today_collection' => $todayCollection,
            'today_collection_formatted' => '₱ '.number_format($todayCollection, 2),
            'today_count' => $todayCount,
            'month_collection' => $monthCollection,
            'month_collection_formatted' => '₱ '.number_format($monthCollection, 2),
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
            // ['value' => self::TYPE_WATER_BILL, 'label' => 'Water Bill'],
            // ['value' => self::TYPE_OTHER_CHARGE, 'label' => 'Other Charges'],
        ];
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
