<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Period;
use App\Models\Status;
use App\Services\Billing\BillAdjustmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillAdjustmentController extends Controller
{
    public function __construct(
        private BillAdjustmentService $adjustmentService
    ) {}

    /**
     * Get all billing periods for adjustments (includes closed periods).
     */
    public function periods(): JsonResponse
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $periods = Period::select('per_id', 'per_name', 'start_date', 'end_date', 'grace_period', 'is_closed', 'stat_id')
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($period) use ($activeStatusId) {
                return [
                    'per_id' => $period->per_id,
                    'per_name' => $period->per_name,
                    'start_date' => $period->start_date,
                    'end_date' => $period->end_date,
                    'grace_period' => $period->grace_period,
                    'is_closed' => (bool) $period->is_closed,
                    'is_active' => $period->stat_id === $activeStatusId,
                ];
            });

        // Default period: most recent closed period
        $defaultPeriod = $periods->first(fn ($p) => $p['is_closed']);
        $defaultPeriodId = $defaultPeriod ? $defaultPeriod['per_id'] : ($periods->first()['per_id'] ?? null);

        return response()->json([
            'success' => true,
            'data' => $periods,
            'defaultPeriodId' => $defaultPeriodId,
        ]);
    }

    /**
     * Get all adjustments with optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'period_id' => $request->input('period_id'),
            'area_id' => $request->input('area_id'),
            'type_id' => $request->input('type_id'),
            'search' => $request->input('search'),
        ];

        $adjustments = $this->adjustmentService->getAllAdjustments($filters);

        return response()->json([
            'success' => true,
            'data' => $adjustments,
            'count' => $adjustments->count(),
        ]);
    }

    /**
     * Lookup bill details for adjustment modal.
     */
    public function lookupBill(int $billId): JsonResponse
    {
        $bill = $this->adjustmentService->lookupBill($billId);

        if (! $bill) {
            return response()->json([
                'success' => false,
                'message' => 'Bill not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $bill,
        ]);
    }

    /**
     * Get available adjustment types.
     */
    public function types(): JsonResponse
    {
        $types = $this->adjustmentService->getAdjustmentTypes();

        return response()->json([
            'success' => true,
            'data' => $types,
        ]);
    }

    /**
     * Get adjustments for a specific bill.
     */
    public function forBill(int $billId): JsonResponse
    {
        $adjustments = $this->adjustmentService->getAdjustmentsForBill($billId);
        $summary = $this->adjustmentService->getAdjustmentSummary($billId);

        return response()->json([
            'success' => true,
            'data' => $adjustments,
            'summary' => $summary,
        ]);
    }

    /**
     * Apply a consumption adjustment to a bill.
     */
    public function adjustConsumption(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bill_id' => 'required|integer|exists:water_bill_history,bill_id',
            'new_prev_reading' => 'nullable|numeric|min:0',
            'new_curr_reading' => 'required|numeric|min:0',
            'bill_adjustment_type_id' => 'nullable|integer|exists:BillAdjustmentType,bill_adjustment_type_id',
            'remarks' => 'nullable|string|max:500',
        ]);

        $result = $this->adjustmentService->adjustConsumption($validated);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Apply an amount adjustment to a bill.
     */
    public function adjustAmount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bill_id' => 'required|integer|exists:water_bill_history,bill_id',
            'bill_adjustment_type_id' => 'required|integer|exists:BillAdjustmentType,bill_adjustment_type_id',
            'amount' => 'required|numeric|min:0.01',
            'remarks' => 'nullable|string|max:500',
        ]);

        $result = $this->adjustmentService->adjustAmount($validated);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Void/reverse an adjustment.
     */
    public function void(Request $request, int $adjustmentId): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $result = $this->adjustmentService->voidAdjustment($adjustmentId, $validated['reason'] ?? null);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Get adjustment summary for a bill.
     */
    public function summary(int $billId): JsonResponse
    {
        $summary = $this->adjustmentService->getAdjustmentSummary($billId);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Recompute a single bill.
     *
     * This modifies the actual bill record (water_amount, consumption)
     * based on the current meter readings. Only works on OPEN periods.
     *
     * Optionally accepts new reading values to update before recomputing.
     */
    public function recomputeBill(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bill_id' => 'required|integer|exists:water_bill_history,bill_id',
            'new_prev_reading' => 'nullable|numeric|min:0',
            'new_curr_reading' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        $result = $this->adjustmentService->recomputeBill(
            $validated['bill_id'],
            $validated['remarks'] ?? null,
            $validated['new_prev_reading'] ?? null,
            $validated['new_curr_reading'] ?? null
        );

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Recompute all bills in a period.
     *
     * This recalculates all bills based on their current meter readings.
     * Only works on OPEN periods.
     */
    public function recomputePeriod(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'period_id' => 'required|integer|exists:period,per_id',
        ]);

        $result = $this->adjustmentService->recomputePeriodBills($validated['period_id']);

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
