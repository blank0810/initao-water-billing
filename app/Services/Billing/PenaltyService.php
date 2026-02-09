<?php

namespace App\Services\Billing;

use App\Models\ChargeItem;
use App\Models\CustomerCharge;
use App\Models\Status;
use App\Models\WaterBillHistory;
use App\Services\Ledger\LedgerService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PenaltyService
{
    private const PENALTY_AMOUNT = 10.00;

    private const PENALTY_CODE = 'LATE_PENALTY';

    public function __construct(
        private LedgerService $ledgerService
    ) {}

    /**
     * Find all overdue bills that are past due_date and still ACTIVE
     */
    public function findOverdueBills(): Collection
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        return WaterBillHistory::with(['serviceConnection.customer', 'period'])
            ->where('stat_id', $activeStatusId)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereHas('period', function ($query) {
                $query->where('is_closed', false);
            })
            ->get();
    }

    /**
     * Check if a penalty already exists for a specific bill
     */
    public function hasExistingPenalty(WaterBillHistory $bill): bool
    {
        $penaltyChargeItem = ChargeItem::where('code', self::PENALTY_CODE)->first();

        if (! $penaltyChargeItem) {
            return false;
        }

        return CustomerCharge::where('connection_id', $bill->connection_id)
            ->where('charge_item_id', $penaltyChargeItem->charge_item_id)
            ->where('description', 'like', '%Bill #'.$bill->bill_id.'%')
            ->exists();
    }

    /**
     * Create a penalty charge for an overdue bill
     */
    public function createPenalty(WaterBillHistory $bill, int $userId): array
    {
        // Get the penalty charge item
        $penaltyChargeItem = ChargeItem::where('code', self::PENALTY_CODE)->first();

        if (! $penaltyChargeItem) {
            return [
                'success' => false,
                'message' => 'Penalty charge item (LATE_PENALTY) not found. Please seed charge items.',
            ];
        }

        // Check if penalty already exists
        if ($this->hasExistingPenalty($bill)) {
            return [
                'success' => false,
                'message' => 'Penalty already exists for this bill.',
            ];
        }

        // Get customer from connection
        $connection = $bill->serviceConnection;
        if (! $connection || ! $connection->customer_id) {
            return [
                'success' => false,
                'message' => 'Service connection or customer not found.',
            ];
        }

        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $overdueStatusId = Status::getIdByDescription(Status::OVERDUE);

        try {
            DB::beginTransaction();

            // Create the penalty charge
            $periodName = $bill->period ? $bill->period->per_name : 'Unknown Period';
            $charge = CustomerCharge::create([
                'customer_id' => $connection->customer_id,
                'application_id' => null,
                'connection_id' => $bill->connection_id,
                'charge_item_id' => $penaltyChargeItem->charge_item_id,
                'description' => "Late Payment Penalty - {$periodName} (Bill #{$bill->bill_id})",
                'quantity' => 1,
                'unit_amount' => self::PENALTY_AMOUNT,
                'due_date' => now()->addDays(7),
                'stat_id' => $activeStatusId,
            ]);

            // Create ledger entry via LedgerService
            $this->ledgerService->recordCharge($charge, $userId);

            // Update bill status to OVERDUE
            $bill->update(['stat_id' => $overdueStatusId]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Penalty created successfully.',
                'charge' => $charge,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Failed to create penalty: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Process all overdue bills and create penalties
     */
    public function processAllOverdueBills(int $userId): array
    {
        $overdueBills = $this->findOverdueBills();

        $processed = 0;
        $skipped = 0;
        $errors = [];

        foreach ($overdueBills as $bill) {
            $result = $this->createPenalty($bill, $userId);

            if ($result['success']) {
                $processed++;
            } elseif (str_contains($result['message'], 'already exists')) {
                $skipped++;
            } else {
                $errors[] = [
                    'bill_id' => $bill->bill_id,
                    'message' => $result['message'],
                ];
            }
        }

        return [
            'success' => true,
            'message' => $processed > 0
                ? "Successfully created {$processed} penalty/ies."
                : 'No new penalties created.',
            'total_overdue' => $overdueBills->count(),
            'processed' => $processed,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * Get overdue bills summary for display
     */
    public function getOverdueBillsSummary(): array
    {
        $overdueBills = $this->findOverdueBills();

        $withPenalty = 0;
        $withoutPenalty = 0;

        foreach ($overdueBills as $bill) {
            if ($this->hasExistingPenalty($bill)) {
                $withPenalty++;
            } else {
                $withoutPenalty++;
            }
        }

        return [
            'total_overdue' => $overdueBills->count(),
            'with_penalty' => $withPenalty,
            'without_penalty' => $withoutPenalty,
        ];
    }
}
