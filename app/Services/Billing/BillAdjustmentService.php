<?php

namespace App\Services\Billing;

use App\Models\BillAdjustment;
use App\Models\BillAdjustmentType;
use App\Models\CustomerLedger;
use App\Models\MeterReading;
use App\Models\Status;
use App\Models\WaterBillHistory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     * Create a consumption adjustment.
     *
     * This will:
     * 1. Update the current reading value
     * 2. Recalculate the bill amount based on new consumption
     * 3. Create a BillAdjustment record tracking old/new values
     * 4. Update the bill's water_amount and adjustment_total
     * 5. Create a CustomerLedger entry for the difference
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

            // Update the bill
            $bill->update([
                'consumption' => $newConsumption,
                'water_amount' => $newAmount,
            ]);

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
                'message' => 'Consumption adjustment applied successfully.',
                'data' => [
                    'adjustment_id' => $adjustment->bill_adjustment_id,
                    'bill_id' => $bill->bill_id,
                    'old_consumption' => $oldConsumption,
                    'new_consumption' => $newConsumption,
                    'old_amount' => $oldAmount,
                    'new_amount' => $newAmount,
                    'difference' => $amountDifference,
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

            // Determine the reversal amount
            $direction = $adjustment->billAdjustmentType?->direction ?? 'debit';
            $reversalAmount = (float) $adjustment->amount;

            if ($adjustment->adjustment_category === BillAdjustment::CATEGORY_CONSUMPTION) {
                // For consumption adjustments, we need to reverse the bill changes
                // This is complex - we would need to restore old consumption and amount
                // For now, we'll just reverse the financial impact
                $amountDiff = $adjustment->amount_difference ?? 0;
                $reversalAmount = -$amountDiff;
            } else {
                // For amount adjustments, reverse the direction
                $reversalAmount = $direction === 'credit' ? $adjustment->amount : -$adjustment->amount;
            }

            // Update bill adjustment_total
            if ($adjustment->adjustment_category === BillAdjustment::CATEGORY_AMOUNT) {
                $newAdjustmentTotal = (float) $bill->adjustment_total + $reversalAmount;
                $bill->update(['adjustment_total' => $newAdjustmentTotal]);
            }

            // Mark adjustment as voided
            $adjustment->update([
                'stat_id' => $voidedStatusId,
                'remarks' => $adjustment->remarks.' [VOIDED: '.($reason ?? 'No reason provided').']',
            ]);

            // Create reversal ledger entry
            $description = 'VOID: '.$adjustment->billAdjustmentType?->name.' - '.$bill->period?->per_name;
            if ($reason) {
                $description .= ' (Reason: '.$reason.')';
            }

            $this->createAdjustmentLedgerEntry($bill, $adjustment, $reversalAmount, $description);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Adjustment voided successfully.',
                'data' => [
                    'adjustment_id' => $adjustmentId,
                    'reversal_amount' => $reversalAmount,
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
}
