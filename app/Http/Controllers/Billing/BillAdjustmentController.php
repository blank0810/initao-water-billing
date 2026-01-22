<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Services\Billing\BillAdjustmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillAdjustmentController extends Controller
{
    public function __construct(
        private BillAdjustmentService $adjustmentService
    ) {}

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
}
