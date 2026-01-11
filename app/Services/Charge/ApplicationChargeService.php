<?php

namespace App\Services\Charge;

use App\Models\ChargeItem;
use App\Models\CustomerCharge;
use App\Models\ServiceApplication;
use App\Models\Status;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ApplicationChargeService
{
    /**
     * Generate charges when application is verified
     *
     * Creates CustomerCharge records for all application fees
     */
    public function generateApplicationCharges(ServiceApplication $application): Collection
    {
        $chargeItems = ChargeItem::applicationFees()->get();

        if ($chargeItems->isEmpty()) {
            throw new \Exception('No application fee charge items found. Please seed ChargeItem table.');
        }

        $pendingStatusId = Status::getIdByDescription(Status::PENDING);
        $charges = collect();

        DB::transaction(function () use ($application, $chargeItems, $pendingStatusId, &$charges) {
            foreach ($chargeItems as $chargeItem) {
                $charge = CustomerCharge::create([
                    'customer_id' => $application->customer_id,
                    'application_id' => $application->application_id,
                    'connection_id' => null, // Will be set when connection is created
                    'charge_item_id' => $chargeItem->charge_item_id,
                    'description' => $chargeItem->name,
                    'quantity' => 1,
                    'unit_amount' => $chargeItem->default_amount,
                    'due_date' => now()->addDays(7),
                    'stat_id' => $pendingStatusId,
                ]);

                $charges->push($charge);
            }
        });

        return $charges;
    }

    /**
     * Get charges summary for an application
     */
    public function getApplicationCharges(int $applicationId): Collection
    {
        return CustomerCharge::with(['chargeItem', 'status'])
            ->where('application_id', $applicationId)
            ->get();
    }

    /**
     * Get total charges for an application
     */
    public function getApplicationChargesTotal(int $applicationId): array
    {
        $charges = $this->getApplicationCharges($applicationId);

        $totalAmount = $charges->sum(fn ($charge) => $charge->total_amount);
        $paidAmount = $charges->sum(fn ($charge) => $charge->paid_amount);
        $remainingAmount = $charges->sum(fn ($charge) => $charge->remaining_amount);

        return [
            'charges' => $charges,
            'total_amount' => (float) $totalAmount,
            'paid_amount' => (float) $paidAmount,
            'remaining_amount' => (float) $remainingAmount,
            'is_fully_paid' => $remainingAmount <= 0,
        ];
    }

    /**
     * Transfer charges to connection when application is completed
     *
     * Updates CustomerCharge.connection_id for all charges
     */
    public function transferChargesToConnection(int $applicationId, int $connectionId): void
    {
        CustomerCharge::where('application_id', $applicationId)
            ->update(['connection_id' => $connectionId]);
    }

    /**
     * Mark charges as paid
     */
    public function markChargesAsPaid(int $applicationId): void
    {
        $paidStatusId = Status::getIdByDescription(Status::PAID);

        CustomerCharge::where('application_id', $applicationId)
            ->update(['stat_id' => $paidStatusId]);
    }

    /**
     * Check if all charges are paid for an application
     */
    public function areAllChargesPaid(int $applicationId): bool
    {
        $charges = $this->getApplicationCharges($applicationId);

        if ($charges->isEmpty()) {
            return false;
        }

        return $charges->every(fn ($charge) => $charge->isPaid());
    }
}
