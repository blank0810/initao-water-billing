<?php

namespace App\Services\Billing;

use App\Models\BillAdjustment;
use App\Models\ChargeItem;
use App\Models\CustomerCharge;
use App\Models\CustomerLedger;
use App\Models\PenaltyConfiguration;
use App\Models\Status;
use App\Models\WaterBillHistory;
use App\Services\Ledger\LedgerService;
use App\Services\Notification\NotificationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenaltyService
{
    private const PENALTY_CODE = 'LATE_PENALTY';

    private ?ChargeItem $penaltyChargeItem = null;

    private ?float $penaltyRate = null;

    private ?int $activeStatusId = null;

    private ?int $overdueStatusId = null;

    public function __construct(
        private LedgerService $ledgerService,
        private NotificationService $notificationService
    ) {}

    /**
     * Get the penalty charge item (cached per request).
     */
    private function getPenaltyChargeItem(): ?ChargeItem
    {
        if ($this->penaltyChargeItem === null) {
            $this->penaltyChargeItem = ChargeItem::where('code', self::PENALTY_CODE)->first();
        }

        return $this->penaltyChargeItem;
    }

    /**
     * Get the active penalty rate as a decimal (cached per request).
     */
    private function getPenaltyRate(): float
    {
        if ($this->penaltyRate === null) {
            $this->penaltyRate = PenaltyConfiguration::getActiveRate();
        }

        return $this->penaltyRate;
    }

    /**
     * Get the ACTIVE status ID (cached per request).
     */
    private function getActiveStatusId(): int
    {
        if ($this->activeStatusId === null) {
            $this->activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        }

        return $this->activeStatusId;
    }

    /**
     * Get the OVERDUE status ID (cached per request).
     */
    private function getOverdueStatusId(): int
    {
        if ($this->overdueStatusId === null) {
            $this->overdueStatusId = Status::getIdByDescription(Status::OVERDUE);
        }

        return $this->overdueStatusId;
    }

    /**
     * Calculate the effective bill total including ledger-only consumption adjustments.
     *
     * Amount adjustments are already in adjustment_total. Consumption adjustments
     * (adjustConsumption) only post to CustomerLedger, so we must add their net delta.
     */
    private function getEffectiveBillTotal(WaterBillHistory $bill): float
    {
        $activeStatusId = $this->getActiveStatusId();
        $baseTotal = (float) $bill->water_amount + (float) $bill->adjustment_total;

        // Get consumption-only adjustment IDs (these are ledger-only, not in adjustment_total)
        $consumptionAdjustmentIds = $bill->billAdjustments
            ->filter(fn ($adj) => $adj->stat_id === $activeStatusId && $adj->adjustment_category === BillAdjustment::CATEGORY_CONSUMPTION)
            ->pluck('bill_adjustment_id');

        if ($consumptionAdjustmentIds->isEmpty()) {
            return $baseTotal;
        }

        $debit = (float) CustomerLedger::where('connection_id', $bill->connection_id)
            ->where('source_type', 'ADJUST')
            ->where('stat_id', $activeStatusId)
            ->whereIn('source_id', $consumptionAdjustmentIds)
            ->sum('debit');
        $credit = (float) CustomerLedger::where('connection_id', $bill->connection_id)
            ->where('source_type', 'ADJUST')
            ->where('stat_id', $activeStatusId)
            ->whereIn('source_id', $consumptionAdjustmentIds)
            ->sum('credit');

        return $baseTotal + ($debit - $credit);
    }

    /**
     * Find all overdue bills that are past due_date and still ACTIVE
     */
    public function findOverdueBills(): Collection
    {
        return WaterBillHistory::with(['serviceConnection.customer', 'period', 'billAdjustments'])
            ->where('stat_id', $this->getActiveStatusId())
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereHas('period', function ($query) {
                $query->where('is_closed', false);
            })
            ->get();
    }

    /**
     * Get bill IDs that already have penalties (bulk check).
     */
    private function getBillIdsWithPenalties(Collection $bills): array
    {
        $chargeItem = $this->getPenaltyChargeItem();
        if (! $chargeItem || $bills->isEmpty()) {
            return [];
        }

        $billIds = $bills->pluck('bill_id')->toArray();

        return CustomerCharge::where('charge_item_id', $chargeItem->charge_item_id)
            ->where(function ($query) use ($billIds) {
                foreach ($billIds as $billId) {
                    $query->orWhere('description', 'like', '%(Bill #'.$billId.')');
                }
            })
            ->get()
            ->flatMap(function ($charge) use ($billIds) {
                $matched = [];
                foreach ($billIds as $billId) {
                    if (str_contains($charge->description, '(Bill #'.$billId.')')) {
                        $matched[] = $billId;
                    }
                }

                return $matched;
            })
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Check if a penalty already exists for a specific bill
     */
    public function hasExistingPenalty(WaterBillHistory $bill): bool
    {
        $chargeItem = $this->getPenaltyChargeItem();

        if (! $chargeItem) {
            return false;
        }

        return CustomerCharge::where('connection_id', $bill->connection_id)
            ->where('charge_item_id', $chargeItem->charge_item_id)
            ->where('description', 'like', '%(Bill #'.$bill->bill_id.')')
            ->exists();
    }

    /**
     * Create a penalty charge for an overdue bill
     */
    public function createPenalty(WaterBillHistory $bill, int $userId): array
    {
        // Get the penalty charge item (cached)
        $penaltyChargeItem = $this->getPenaltyChargeItem();

        if (! $penaltyChargeItem) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Penalty charge item (LATE_PENALTY) not found. Please seed charge items.',
            ];
        }

        // Check if penalty already exists
        if ($this->hasExistingPenalty($bill)) {
            return [
                'success' => false,
                'status' => 'already_exists',
                'message' => 'Penalty already exists for this bill.',
            ];
        }

        // Get customer from connection
        $connection = $bill->serviceConnection;
        if (! $connection || ! $connection->customer_id) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Service connection or customer not found.',
            ];
        }

        // Calculate penalty as percentage of total bill amount (rate cached per request)
        $rate = $this->getPenaltyRate();
        $ratePercent = $rate * 100;
        $billTotal = $this->getEffectiveBillTotal($bill);
        $penaltyAmount = round($billTotal * $rate, 2);

        try {
            DB::beginTransaction();

            // Create the penalty charge
            $periodName = $bill->period ? $bill->period->per_name : 'Unknown Period';
            $charge = CustomerCharge::create([
                'customer_id' => $connection->customer_id,
                'application_id' => null,
                'connection_id' => $bill->connection_id,
                'charge_item_id' => $penaltyChargeItem->charge_item_id,
                'description' => "Late Payment Penalty ({$ratePercent}%) - {$periodName} (Bill #{$bill->bill_id})",
                'quantity' => 1,
                'unit_amount' => $penaltyAmount,
                'due_date' => now()->addDays(7),
                'stat_id' => $this->getActiveStatusId(),
            ]);

            // Create ledger entry via LedgerService (with period_id for proper tracking)
            $this->ledgerService->recordCharge($charge, $userId, $bill->period_id);

            // Update bill status to OVERDUE
            $bill->update(['stat_id' => $this->getOverdueStatusId()]);

            DB::commit();

            return [
                'success' => true,
                'status' => 'created',
                'message' => 'Penalty created successfully.',
                'charge' => $charge,
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Failed to create penalty', [
                'bill_id' => $bill->bill_id,
                'connection_id' => $bill->connection_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Failed to create penalty. Please try again or contact support.',
            ];
        }
    }

    /**
     * Process all overdue bills and create penalties
     */
    public function processAllOverdueBills(int $userId): array
    {
        $overdueBills = $this->findOverdueBills();
        $billIdsWithPenalties = $this->getBillIdsWithPenalties($overdueBills);

        $processed = 0;
        $skipped = 0;
        $errors = [];

        foreach ($overdueBills as $bill) {
            if (in_array($bill->bill_id, $billIdsWithPenalties)) {
                $skipped++;

                continue;
            }

            $result = $this->createPenalty($bill, $userId);

            if ($result['success']) {
                $processed++;
            } else {
                $errors[] = [
                    'bill_id' => $bill->bill_id,
                    'message' => $result['message'],
                ];
            }
        }

        if ($processed > 0) {
            $this->notificationService->notifyPenaltiesProcessed($processed, $userId);
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
     * Process a batch of overdue bills (for progress tracking).
     */
    public function processBatch(int $userId, int $limit = 50): array
    {
        $allOverdueBills = $this->findOverdueBills();
        $totalOverdue = $allOverdueBills->count();

        // Bulk check for existing penalties (single query instead of N queries)
        $billIdsWithPenalties = $this->getBillIdsWithPenalties($allOverdueBills);

        // Filter to only bills without existing penalty
        $pendingBills = $allOverdueBills->filter(fn ($bill) => ! in_array($bill->bill_id, $billIdsWithPenalties))->values();
        $totalPending = $pendingBills->count();

        // Processed bills are marked OVERDUE and removed from findOverdueBills(),
        // so each call must start from position 0 of the current pending list.
        $batch = $pendingBills->slice(0, $limit);

        $processed = 0;
        $skipped = 0;
        $errors = [];

        foreach ($batch as $bill) {
            $result = $this->createPenalty($bill, $userId);

            if ($result['success']) {
                $processed++;
            } elseif (($result['status'] ?? '') === 'already_exists') {
                $skipped++;
            } else {
                $errors[] = [
                    'bill_id' => $bill->bill_id,
                    'message' => $result['message'],
                ];
            }
        }

        $hasMore = $totalPending > $limit;

        return [
            'success' => true,
            'total_pending' => $totalPending,
            'total_overdue' => $totalOverdue,
            'batch_processed' => $processed,
            'batch_skipped' => $skipped,
            'batch_errors' => $errors,
            'has_more' => $hasMore,
        ];
    }

    /**
     * Get overdue bills summary for display
     */
    public function getOverdueBillsSummary(): array
    {
        $overdueBills = $this->findOverdueBills();
        $rate = $this->getPenaltyRate();

        // Bulk check for existing penalties (single query)
        $billIdsWithPenalties = $this->getBillIdsWithPenalties($overdueBills);

        $withPenalty = 0;
        $withoutPenalty = 0;
        $estimatedTotal = 0;

        foreach ($overdueBills as $bill) {
            if (in_array($bill->bill_id, $billIdsWithPenalties)) {
                $withPenalty++;
            } else {
                $withoutPenalty++;
                $estimatedTotal += round($this->getEffectiveBillTotal($bill) * $rate, 2);
            }
        }

        return [
            'total_overdue' => $overdueBills->count(),
            'with_penalty' => $withPenalty,
            'without_penalty' => $withoutPenalty,
            'penalty_rate' => $rate * 100,
            'estimated_total' => $estimatedTotal,
        ];
    }
}
