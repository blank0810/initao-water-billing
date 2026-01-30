<?php

namespace App\Services\Billing;

use App\Models\BillAdjustment;
use App\Models\BillAdjustmentType;
use App\Models\CustomerLedger;
use App\Models\MeterReading;
use App\Models\Period;
use App\Models\Status;
use App\Models\WaterBillHistory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling bill adjustments.
 *
 * NOTE: Bill adjustments are intentionally allowed on closed periods.
 * This is a business decision to support customer dispute resolution
 * and corrections that may be raised after a billing period is closed.
 * The period closure rule applies to routine billing operations (generating
 * new bills, standard processing) but not to adjustments/corrections.
 */
class BillAdjustmentService
{
    public function __construct(
        private WaterBillService $waterBillService
    ) {}

    /**
     * Get adjustment types for dropdown.
     */
    public function getAdjustmentTypes(): Collection
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE) ?? 2;

        return BillAdjustmentType::where('stat_id', $activeStatusId)
            ->get()
            ->map(function ($type) {
                return [
                    'bill_adjustment_type_id' => $type->bill_adjustment_type_id,
                    'name' => $type->name,
                    'direction' => $type->direction, // 'debit' or 'credit'
                ];
            });
    }

    /**
     * Create a consumption adjustment (ledger-only approach).
     *
     * This will:
     * 1. Update the MeterReading records (to correct source data)
     * 2. Calculate the bill amount difference based on new consumption
     * 3. Create a BillAdjustment record tracking old/new values for audit
     * 4. Create a CustomerLedger entry (ADJUST) for the difference
     *
     * NOTE: This method does NOT modify water_bill_history.water_amount or consumption.
     * The original bill amount is preserved as a historical record.
     * Use recomputeBill() if actual bill modification is needed before period closure.
     */
    public function adjustConsumption(array $data): array
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE) ?? 2;
        $userId = Auth::id() ?? 1;

        // Validate bill exists
        $bill = WaterBillHistory::with(['currentReading', 'previousReading', 'serviceConnection.accountType'])
            ->find($data['bill_id']);

        if (! $bill) {
            return ['success' => false, 'message' => 'Bill not found.'];
        }

        $connection = $bill->serviceConnection;
        if (! $connection) {
            return ['success' => false, 'message' => 'Service connection not found.'];
        }

        // Calculate old values
        $oldPrevReading = (float) $bill->previousReading?->reading_value ?? 0;
        $oldCurrReading = (float) $bill->currentReading?->reading_value ?? 0;
        $oldConsumption = (float) $bill->consumption;
        $oldAmount = (float) $bill->water_amount;

        // Get new readings from data
        $newPrevReading = isset($data['new_prev_reading']) ? (float) $data['new_prev_reading'] : $oldPrevReading;
        $newCurrReading = isset($data['new_curr_reading']) ? (float) $data['new_curr_reading'] : $oldCurrReading;

        // Validate readings
        if ($newCurrReading < $newPrevReading) {
            return ['success' => false, 'message' => 'Current reading cannot be less than previous reading.'];
        }

        $newConsumption = $newCurrReading - $newPrevReading;

        // If no change in consumption, return early
        if (abs($newConsumption - $oldConsumption) < 0.001) {
            return ['success' => false, 'message' => 'No change in consumption detected.'];
        }

        // Calculate new bill amount
        $calculation = $this->waterBillService->calculateBillAmount(
            $newConsumption,
            $connection->account_type_id,
            $bill->period_id
        );

        if (! $calculation['success']) {
            return $calculation;
        }

        $newAmount = $calculation['amount'];
        $amountDifference = $newAmount - $oldAmount;

        // Get the adjustment type (required)
        $adjustmentTypeId = $data['bill_adjustment_type_id'] ?? $this->getConsumptionAdjustmentTypeId($amountDifference);
        $adjustmentType = BillAdjustmentType::find($adjustmentTypeId);

        if (! $adjustmentType) {
            return ['success' => false, 'message' => 'Adjustment type not found.'];
        }

        try {
            DB::beginTransaction();

            // Update meter readings if changed
            if ($bill->currentReading && abs($newCurrReading - $oldCurrReading) > 0.001) {
                $bill->currentReading->update(['reading_value' => $newCurrReading]);
            }

            if ($bill->previousReading && abs($newPrevReading - $oldPrevReading) > 0.001) {
                $bill->previousReading->update(['reading_value' => $newPrevReading]);
            }

            // NOTE: Bill record (water_bill_history) is NOT modified.
            // The ledger entry handles the financial adjustment.

            // Create BillAdjustment record
            $adjustment = BillAdjustment::create([
                'bill_id' => $bill->bill_id,
                'bill_adjustment_type_id' => $adjustmentTypeId,
                'amount' => abs($amountDifference),
                'old_consumption' => $oldConsumption,
                'new_consumption' => $newConsumption,
                'old_amount' => $oldAmount,
                'new_amount' => $newAmount,
                'adjustment_category' => BillAdjustment::CATEGORY_CONSUMPTION,
                'remarks' => $data['remarks'] ?? 'Consumption adjustment',
                'created_at' => now(),
                'user_id' => $userId,
                'stat_id' => $activeStatusId,
            ]);

            // Create CustomerLedger entry
            // For consumption adjustments: direction is determined by actual change
            // Bill increased (amountDifference > 0) = customer owes more = debit
            // Bill decreased (amountDifference < 0) = customer owes less = credit
            if (abs($amountDifference) > 0.01) {
                $ledgerDirection = $amountDifference >= 0 ? 'debit' : 'credit';
                $this->createAdjustmentLedgerEntry(
                    $bill,
                    $adjustment,
                    abs($amountDifference),
                    'Consumption Adjustment - '.$bill->period?->per_name.' (Old: '.$oldConsumption.' cu.m, New: '.$newConsumption.' cu.m)',
                    $ledgerDirection
                );
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Consumption adjustment recorded. A ledger entry has been created for the difference of ₱'.number_format(abs($amountDifference), 2).'.',
                'data' => [
                    'adjustment_id' => $adjustment->bill_adjustment_id,
                    'bill_id' => $bill->bill_id,
                    'old_consumption' => $oldConsumption,
                    'new_consumption' => $newConsumption,
                    'old_amount' => $oldAmount,
                    'new_amount' => $newAmount,
                    'difference' => $amountDifference,
                    'ledger_only' => true,
                ],
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Consumption adjustment failed', [
                'bill_id' => $data['bill_id'],
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Adjustment failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Create an amount adjustment (without changing consumption).
     *
     * This will:
     * 1. Create a BillAdjustment record
     * 2. Update the bill's adjustment_total field
     * 3. Create a CustomerLedger entry
     */
    public function adjustAmount(array $data): array
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE) ?? 2;
        $userId = Auth::id() ?? 1;

        // Validate bill exists
        $bill = WaterBillHistory::with(['serviceConnection', 'period'])
            ->find($data['bill_id']);

        if (! $bill) {
            return ['success' => false, 'message' => 'Bill not found.'];
        }

        // Validate adjustment type
        $adjustmentType = BillAdjustmentType::find($data['bill_adjustment_type_id']);
        if (! $adjustmentType) {
            return ['success' => false, 'message' => 'Adjustment type not found.'];
        }

        $amount = abs((float) $data['amount']);
        if ($amount <= 0) {
            return ['success' => false, 'message' => 'Adjustment amount must be greater than zero.'];
        }

        // Determine the actual amount based on direction
        // 'debit' = adds to bill (increases what customer owes)
        // 'credit' = subtracts from bill (reduces what customer owes)
        $actualAmount = $adjustmentType->direction === 'credit' ? -$amount : $amount;

        // Get old values
        $oldAmount = (float) $bill->water_amount + (float) $bill->adjustment_total;
        $newAmount = $oldAmount + $actualAmount;

        try {
            DB::beginTransaction();

            // Update bill adjustment_total
            $newAdjustmentTotal = (float) $bill->adjustment_total + $actualAmount;
            $bill->update(['adjustment_total' => $newAdjustmentTotal]);

            // Create BillAdjustment record
            $adjustment = BillAdjustment::create([
                'bill_id' => $bill->bill_id,
                'bill_adjustment_type_id' => $data['bill_adjustment_type_id'],
                'amount' => $amount,
                'old_consumption' => null,
                'new_consumption' => null,
                'old_amount' => $oldAmount,
                'new_amount' => $newAmount,
                'adjustment_category' => BillAdjustment::CATEGORY_AMOUNT,
                'remarks' => $data['remarks'] ?? $adjustmentType->name,
                'created_at' => now(),
                'user_id' => $userId,
                'stat_id' => $activeStatusId,
            ]);

            // Create CustomerLedger entry using adjustment type's direction
            $description = $adjustmentType->name.' - '.$bill->period?->per_name;
            if (! empty($data['remarks'])) {
                $description .= ' ('.$data['remarks'].')';
            }

            $this->createAdjustmentLedgerEntry($bill, $adjustment, $amount, $description, $adjustmentType->direction);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Amount adjustment applied successfully.',
                'data' => [
                    'adjustment_id' => $adjustment->bill_adjustment_id,
                    'bill_id' => $bill->bill_id,
                    'adjustment_type' => $adjustmentType->name,
                    'direction' => $adjustmentType->direction,
                    'amount' => $amount,
                    'old_total' => $oldAmount,
                    'new_total' => $newAmount,
                ],
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Amount adjustment failed', [
                'bill_id' => $data['bill_id'],
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Adjustment failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Get adjustments for a specific bill.
     */
    public function getAdjustmentsForBill(int $billId): Collection
    {
        return BillAdjustment::with(['billAdjustmentType', 'user', 'status'])
            ->where('bill_id', $billId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($adjustment) {
                return [
                    'bill_adjustment_id' => $adjustment->bill_adjustment_id,
                    'type' => $adjustment->billAdjustmentType?->name ?? 'Unknown',
                    'direction' => $adjustment->billAdjustmentType?->direction ?? 'debit',
                    'amount' => (float) $adjustment->amount,
                    'old_consumption' => $adjustment->old_consumption,
                    'new_consumption' => $adjustment->new_consumption,
                    'old_amount' => $adjustment->old_amount,
                    'new_amount' => $adjustment->new_amount,
                    'adjustment_category' => $adjustment->adjustment_category,
                    'consumption_difference' => $adjustment->consumption_difference,
                    'amount_difference' => $adjustment->amount_difference,
                    'remarks' => $adjustment->remarks,
                    'user' => $adjustment->user?->name ?? 'System',
                    'created_at' => $adjustment->created_at?->format('Y-m-d H:i:s'),
                ];
            });
    }

    /**
     * Void/reverse an adjustment.
     */
    public function voidAdjustment(int $adjustmentId, ?string $reason = null): array
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE) ?? 2;
        $voidedStatusId = Status::getIdByDescription('VOIDED') ?? Status::getIdByDescription('Voided') ?? 5;
        $userId = Auth::id() ?? 1;

        $adjustment = BillAdjustment::with(['waterBillHistory', 'billAdjustmentType'])
            ->find($adjustmentId);

        if (! $adjustment) {
            return ['success' => false, 'message' => 'Adjustment not found.'];
        }

        // Check if already voided
        if ($adjustment->stat_id === $voidedStatusId) {
            return ['success' => false, 'message' => 'Adjustment is already voided.'];
        }

        $bill = $adjustment->waterBillHistory;
        if (! $bill) {
            return ['success' => false, 'message' => 'Associated bill not found.'];
        }

        try {
            DB::beginTransaction();

            // Determine the reversal amount and direction
            $originalDirection = $adjustment->billAdjustmentType?->direction ?? 'debit';
            $reversalAmount = (float) $adjustment->amount;
            $reversalDirection = 'debit'; // Will be set correctly below

            if ($adjustment->adjustment_category === BillAdjustment::CATEGORY_CONSUMPTION) {
                // For consumption adjustments, reverse the financial impact
                // If original adjustment increased bill (debit), void should decrease (credit)
                // If original adjustment decreased bill (credit), void should increase (debit)
                $amountDiff = (float) ($adjustment->amount_difference ?? 0);
                $reversalAmount = abs($amountDiff);

                // Original was debit (bill increased) -> void is credit (reduce customer debt)
                // Original was credit (bill decreased) -> void is debit (increase customer debt)
                $originalWasDebit = $amountDiff > 0;
                $reversalDirection = $originalWasDebit ? 'credit' : 'debit';
            } else {
                // For amount adjustments, reverse the original direction
                // Original debit -> void is credit
                // Original credit -> void is debit
                $reversalDirection = $originalDirection === 'debit' ? 'credit' : 'debit';
            }

            // Update bill adjustment_total
            if ($adjustment->adjustment_category === BillAdjustment::CATEGORY_AMOUNT) {
                // Apply reversal: credit reduces total, debit increases total
                $reversalImpact = $reversalDirection === 'credit' ? -$reversalAmount : $reversalAmount;
                $newAdjustmentTotal = (float) $bill->adjustment_total + $reversalImpact;
                $bill->update(['adjustment_total' => $newAdjustmentTotal]);
            }

            // Mark adjustment as voided
            $adjustment->update([
                'stat_id' => $voidedStatusId,
                'remarks' => $adjustment->remarks.' [VOIDED: '.($reason ?? 'No reason provided').']',
            ]);

            // Create reversal ledger entry with correct direction
            $description = 'VOID: '.$adjustment->billAdjustmentType?->name.' - '.$bill->period?->per_name;
            if ($reason) {
                $description .= ' (Reason: '.$reason.')';
            }

            $this->createAdjustmentLedgerEntry($bill, $adjustment, $reversalAmount, $description, $reversalDirection);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Adjustment voided successfully.',
                'data' => [
                    'adjustment_id' => $adjustmentId,
                    'reversal_amount' => $reversalAmount,
                    'reversal_direction' => $reversalDirection,
                ],
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Void adjustment failed', [
                'adjustment_id' => $adjustmentId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Void failed: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Get adjustment summary for a bill.
     */
    public function getAdjustmentSummary(int $billId): array
    {
        $adjustments = BillAdjustment::with('billAdjustmentType')
            ->where('bill_id', $billId)
            ->active()
            ->get();

        $additionsTotal = 0;
        $subtractionsTotal = 0;

        foreach ($adjustments as $adjustment) {
            $direction = $adjustment->billAdjustmentType?->direction ?? 'debit';
            if ($direction === 'debit') {
                $additionsTotal += (float) $adjustment->amount;
            } else {
                $subtractionsTotal += (float) $adjustment->amount;
            }
        }

        return [
            'total_adjustments' => $adjustments->count(),
            'additions_total' => round($additionsTotal, 2),
            'subtractions_total' => round($subtractionsTotal, 2),
            'net_adjustment' => round($additionsTotal - $subtractionsTotal, 2),
        ];
    }

    /**
     * Create a CustomerLedger entry for an adjustment.
     *
     * @param  string  $direction  The adjustment type direction: 'debit' or 'credit'
     */
    private function createAdjustmentLedgerEntry(
        WaterBillHistory $bill,
        BillAdjustment $adjustment,
        float $amount,
        string $description,
        string $direction = 'debit'
    ): void {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE) ?? 2;
        $userId = Auth::id() ?? 1;

        $connection = $bill->serviceConnection;
        if (! $connection) {
            return;
        }

        $absAmount = abs($amount);

        // Direction determines debit/credit:
        // 'debit' = increases what customer owes (accounts receivable increases)
        // 'credit' = reduces what customer owes (accounts receivable decreases)
        $debit = $direction === 'debit' ? $absAmount : 0;
        $credit = $direction === 'credit' ? $absAmount : 0;

        CustomerLedger::create([
            'customer_id' => $connection->customer_id,
            'connection_id' => $connection->connection_id,
            'period_id' => $bill->period_id,
            'txn_date' => now()->format('Y-m-d'),
            'post_ts' => now(),
            'source_type' => 'ADJUST',
            'source_id' => $adjustment->bill_adjustment_id,
            'source_line_no' => 1,
            'description' => $description,
            'debit' => $debit,
            'credit' => $credit,
            'user_id' => $userId,
            'stat_id' => $activeStatusId,
        ]);
    }

    /**
     * Get all adjustments with optional filters.
     */
    public function getAllAdjustments(array $filters = []): Collection
    {
        $query = BillAdjustment::with([
            'billAdjustmentType',
            'user',
            'waterBillHistory.serviceConnection.customer',
            'waterBillHistory.serviceConnection.area',
            'waterBillHistory.period',
        ])->active();

        // Filter by period
        if (! empty($filters['period_id'])) {
            $query->whereHas('waterBillHistory', function ($q) use ($filters) {
                $q->where('period_id', $filters['period_id']);
            });
        }

        // Filter by area
        if (! empty($filters['area_id'])) {
            $query->whereHas('waterBillHistory.serviceConnection', function ($q) use ($filters) {
                $q->where('area_id', $filters['area_id']);
            });
        }

        // Filter by adjustment type
        if (! empty($filters['type_id'])) {
            $query->where('bill_adjustment_type_id', $filters['type_id']);
        }

        // Search filter
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('bill_id', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%")
                    ->orWhereHas('waterBillHistory.serviceConnection.customer', function ($cq) use ($search) {
                        $cq->where('cust_first_name', 'like', "%{$search}%")
                            ->orWhere('cust_last_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('waterBillHistory.serviceConnection', function ($cq) use ($search) {
                        $cq->where('account_no', 'like', "%{$search}%");
                    });
            });
        }

        return $query->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($adjustment) {
                $bill = $adjustment->waterBillHistory;
                $connection = $bill?->serviceConnection;
                $customer = $connection?->customer;
                $customerName = $customer
                    ? trim($customer->cust_first_name.' '.$customer->cust_last_name)
                    : 'Unknown';

                // Determine display direction based on adjustment category
                // For consumption adjustments: based on actual bill change
                // For amount adjustments: based on adjustment type direction
                $direction = $adjustment->billAdjustmentType?->direction ?? 'debit';
                if ($adjustment->adjustment_category === 'consumption' && $adjustment->old_amount !== null && $adjustment->new_amount !== null) {
                    // Bill increased = debit (customer owes more), Bill decreased = credit (customer owes less)
                    $direction = ((float) $adjustment->new_amount >= (float) $adjustment->old_amount) ? 'debit' : 'credit';
                }

                return [
                    'bill_adjustment_id' => $adjustment->bill_adjustment_id,
                    'bill_id' => $adjustment->bill_id,
                    'consumer_name' => $customerName,
                    'account_no' => $connection?->account_no ?? 'N/A',
                    'period_name' => $bill?->period?->per_name ?? 'N/A',
                    'area_desc' => $connection?->area?->a_desc ?? 'N/A',
                    'type_name' => $adjustment->billAdjustmentType?->name ?? 'Unknown',
                    'direction' => $direction,
                    'amount' => (float) $adjustment->amount,
                    'old_amount' => $adjustment->old_amount,
                    'new_amount' => $adjustment->new_amount,
                    'adjustment_category' => $adjustment->adjustment_category,
                    'remarks' => $adjustment->remarks,
                    'user' => $adjustment->user?->name ?? 'System',
                    'created_at' => $adjustment->created_at?->format('Y-m-d H:i:s'),
                ];
            });
    }

    /**
     * Lookup bill details for adjustment modal.
     */
    public function lookupBill(int $billId): ?array
    {
        $bill = WaterBillHistory::with([
            'serviceConnection.customer',
            'serviceConnection.area',
            'period',
            'currentReading',
            'previousReading',
        ])->find($billId);

        if (! $bill) {
            return null;
        }

        $connection = $bill->serviceConnection;
        $customer = $connection?->customer;
        $customerName = $customer
            ? trim($customer->cust_first_name.' '.$customer->cust_last_name)
            : 'Unknown';

        // Get status description
        $statusMap = [
            1 => 'Pending',
            2 => 'Active',
            3 => 'Inactive',
            4 => 'Overdue',
        ];

        return [
            'bill_id' => $bill->bill_id,
            'connection_id' => $bill->connection_id,
            'consumer_name' => $customerName,
            'account_no' => $connection?->account_no ?? 'N/A',
            'period_name' => $bill->period?->per_name ?? 'N/A',
            'area_desc' => $connection?->area?->a_desc ?? 'N/A',
            'water_amount' => (float) $bill->water_amount,
            'adjustment_total' => (float) $bill->adjustment_total,
            'total_amount' => (float) $bill->water_amount + (float) $bill->adjustment_total,
            'due_date' => $bill->due_date?->format('Y-m-d'),
            'status' => $statusMap[$bill->stat_id] ?? 'Unknown',
            'stat_id' => $bill->stat_id,
            // Reading values for consumption adjustment
            'prev_reading' => (float) ($bill->previousReading?->reading_value ?? 0),
            'curr_reading' => (float) ($bill->currentReading?->reading_value ?? 0),
            'consumption' => (float) $bill->consumption,
        ];
    }

    /**
     * Get the default adjustment type ID for consumption adjustments.
     */
    private function getConsumptionAdjustmentTypeId(float $amountDifference): ?int
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE) ?? 2;
        $direction = $amountDifference >= 0 ? 'debit' : 'credit';

        $type = BillAdjustmentType::where('stat_id', $activeStatusId)
            ->where('direction', $direction)
            ->first();

        return $type?->bill_adjustment_type_id;
    }

    /**
     * Recompute a bill's water_amount based on current readings.
     *
     * This is a RECALCULATION (not an adjustment) and should only be used:
     * - Before period closure (ONLY works on OPEN periods)
     * - When the bill itself needs to be corrected
     * - To update readings and recalculate the bill amount
     *
     * This method directly modifies:
     * - MeterReading records (if new reading values provided)
     * - water_bill_history (consumption, water_amount)
     * - CustomerLedger BILL entry (debit amount, description)
     *
     * Changes are logged to activity_log for audit purposes.
     *
     * @param  int  $billId  The bill to recompute
     * @param  string|null  $remarks  Optional remarks for audit log
     * @param  float|null  $newPrevReading  Optional new previous reading value to set
     * @param  float|null  $newCurrReading  Optional new current reading value to set
     * @return array Result with success status and data
     */
    public function recomputeBill(int $billId, ?string $remarks = null, ?float $newPrevReading = null, ?float $newCurrReading = null): array
    {

        // Validate bill exists with relationships
        $bill = WaterBillHistory::with(['currentReading', 'previousReading', 'period', 'serviceConnection.accountType'])
            ->find($billId);

        if (! $bill) {
            return ['success' => false, 'message' => 'Bill not found.'];
        }

        // CRITICAL: Check period is OPEN
        if ($bill->period?->is_closed) {
            return ['success' => false, 'message' => 'Cannot recompute bill for a closed period. Use adjustments instead.'];
        }

        $connection = $bill->serviceConnection;
        if (! $connection) {
            return ['success' => false, 'message' => 'Service connection not found.'];
        }

        // Capture original reading values for audit trail
        $originalPrevReading = (float) ($bill->previousReading?->reading_value ?? 0);
        $originalCurrReading = (float) ($bill->currentReading?->reading_value ?? 0);
        $readingsUpdated = false;

        // Determine which reading values to use
        $prevReading = $newPrevReading ?? $originalPrevReading;
        $currReading = $newCurrReading ?? $originalCurrReading;
        $newConsumption = $currReading - $prevReading;

        if ($newConsumption < 0) {
            return ['success' => false, 'message' => 'Invalid reading values: current reading is less than previous.'];
        }

        // Check if readings will be updated
        $readingsUpdated = ($newPrevReading !== null && abs($newPrevReading - $originalPrevReading) > 0.001) ||
                          ($newCurrReading !== null && abs($newCurrReading - $originalCurrReading) > 0.001);

        // Check if there's actually a difference in consumption
        $oldConsumption = (float) $bill->consumption;
        $oldAmount = (float) $bill->water_amount;

        if (! $readingsUpdated && abs($newConsumption - $oldConsumption) < 0.001) {
            return ['success' => false, 'message' => 'No change in consumption detected. Bill is already up to date.'];
        }

        // Calculate new bill amount
        $calculation = $this->waterBillService->calculateBillAmount(
            $newConsumption,
            $connection->account_type_id,
            $bill->period_id
        );

        if (! $calculation['success']) {
            return $calculation;
        }

        $newAmount = $calculation['amount'];
        $amountDifference = $newAmount - $oldAmount;

        try {
            DB::beginTransaction();

            // Update MeterReading records if new values are provided (inside transaction)
            if ($newPrevReading !== null && $bill->previousReading) {
                $bill->previousReading->update(['reading_value' => $newPrevReading]);
            }

            if ($newCurrReading !== null && $bill->currentReading) {
                $bill->currentReading->update(['reading_value' => $newCurrReading]);
            }

            // Update the bill record
            $bill->update([
                'consumption' => $newConsumption,
                'water_amount' => $newAmount,
            ]);

            // Update the associated CustomerLedger entry (BILL entry)
            $ledgerEntry = CustomerLedger::where('source_type', 'BILL')
                ->where('source_id', $bill->bill_id)
                ->first();

            if ($ledgerEntry) {
                $ledgerEntry->update([
                    'debit' => $newAmount,
                    'description' => 'Water Bill - '.$bill->period?->per_name.' (Consumption: '.$newConsumption.' cu.m)',
                ]);
            }

            // Log to activity_log for audit trail
            activity('billing')
                ->performedOn($bill)
                ->withProperties([
                    'action' => 'bill_recomputation',
                    'bill_id' => $bill->bill_id,
                    'ledger_id' => $ledgerEntry?->ledger_id,
                    'period' => $bill->period?->per_name,
                    'old_prev_reading' => $originalPrevReading,
                    'new_prev_reading' => $prevReading,
                    'old_curr_reading' => $originalCurrReading,
                    'new_curr_reading' => $currReading,
                    'old_consumption' => $oldConsumption,
                    'new_consumption' => $newConsumption,
                    'old_amount' => $oldAmount,
                    'new_amount' => $newAmount,
                    'difference' => $amountDifference,
                    'readings_updated' => $readingsUpdated,
                    'ledger_updated' => $ledgerEntry !== null,
                    'remarks' => $remarks,
                ])
                ->log('Bill recomputed: consumption '.$oldConsumption.' → '.$newConsumption.', amount ₱'.number_format($oldAmount, 2).' → ₱'.number_format($newAmount, 2));

            DB::commit();

            $message = 'Bill recomputed successfully. Amount changed from ₱'.number_format($oldAmount, 2).' to ₱'.number_format($newAmount, 2).'.';
            if ($readingsUpdated) {
                $message .= ' Readings updated.';
            }
            if ($ledgerEntry) {
                $message .= ' Ledger updated.';
            }

            return [
                'success' => true,
                'message' => $message,
                'data' => [
                    'bill_id' => $bill->bill_id,
                    'old_consumption' => $oldConsumption,
                    'new_consumption' => $newConsumption,
                    'old_amount' => $oldAmount,
                    'new_amount' => $newAmount,
                    'difference' => $amountDifference,
                    'readings_updated' => $readingsUpdated,
                    'ledger_updated' => $ledgerEntry !== null,
                    'original_prev_reading' => $originalPrevReading,
                    'original_curr_reading' => $originalCurrReading,
                    'final_prev_reading' => $prevReading,
                    'final_curr_reading' => $currReading,
                ],
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bill recomputation failed', [
                'bill_id' => $billId,
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'message' => 'Recomputation failed: '.$e->getMessage()];
        }
    }

    /**
     * Recompute all bills in a period.
     *
     * This will recalculate all bills based on their current meter readings.
     * Only works on OPEN periods. Changes are logged to activity_log.
     *
     * @param  int  $periodId  The period to recompute
     * @return array Result with success count and errors
     */
    public function recomputePeriodBills(int $periodId): array
    {
        $period = Period::find($periodId);

        if (! $period) {
            return ['success' => false, 'message' => 'Period not found.'];
        }

        if ($period->is_closed) {
            return ['success' => false, 'message' => 'Cannot recompute bills for a closed period.'];
        }

        $bills = WaterBillHistory::where('period_id', $periodId)->get();

        if ($bills->isEmpty()) {
            return ['success' => false, 'message' => 'No bills found for this period.'];
        }

        $processed = 0;
        $skipped = 0;
        $errors = [];

        foreach ($bills as $bill) {
            $result = $this->recomputeBill($bill->bill_id, 'Batch recomputation - '.$period->per_name);

            if ($result['success']) {
                $processed++;
            } else {
                if (str_contains($result['message'], 'No change')) {
                    $skipped++;
                } else {
                    $errors[] = [
                        'bill_id' => $bill->bill_id,
                        'message' => $result['message'],
                    ];
                }
            }
        }

        $totalBills = $bills->count();
        $errorCount = count($errors);

        return [
            'success' => true,
            'message' => "Recomputation complete: {$processed} bill(s) updated, {$skipped} skipped (no changes), {$errorCount} error(s).",
            'data' => [
                'total_bills' => $totalBills,
                'processed' => $processed,
                'skipped' => $skipped,
                'error_count' => $errorCount,
                'errors' => $errors,
            ],
        ];
    }
}
