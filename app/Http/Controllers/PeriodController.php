<?php

namespace App\Http\Controllers;

use App\Services\Billing\PeriodService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PeriodController extends Controller
{
    protected PeriodService $periodService;

    public function __construct(PeriodService $periodService)
    {
        $this->periodService = $periodService;
    }

    /**
     * Get all periods for table display.
     */
    public function index(): JsonResponse
    {
        $periods = $this->periodService->getAllPeriodsWithStats();
        $stats = $this->periodService->getStatsSummary();

        return response()->json([
            'periods' => $periods,
            'stats' => $stats,
        ]);
    }

    /**
     * Get a single period's details.
     */
    public function show(int $periodId): JsonResponse
    {
        $period = $this->periodService->getPeriodDetails($periodId);

        if (! $period) {
            return response()->json([
                'message' => 'Period not found.',
            ], 404);
        }

        return response()->json([
            'period' => $period,
        ]);
    }

    /**
     * Create a new period.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'per_name' => 'required|string|max:100',
            'per_code' => 'required|string|max:20',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $result = $this->periodService->createPeriod($validated);

        if (! $result['success']) {
            return response()->json([
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'message' => $result['message'],
            'period' => $result['data'],
        ], 201);
    }

    /**
     * Update a period.
     */
    public function update(Request $request, int $periodId): JsonResponse
    {
        $validated = $request->validate([
            'per_name' => 'sometimes|string|max:100',
            'per_code' => 'sometimes|string|max:20',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
        ]);

        $result = $this->periodService->updatePeriod($periodId, $validated);

        if (! $result['success']) {
            return response()->json([
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'message' => $result['message'],
            'period' => $result['data'],
        ]);
    }

    /**
     * Delete a period.
     */
    public function destroy(int $periodId): JsonResponse
    {
        $result = $this->periodService->deletePeriod($periodId);

        if (! $result['success']) {
            return response()->json([
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'message' => $result['message'],
        ]);
    }

    /**
     * Close a period.
     */
    public function closePeriod(int $periodId): JsonResponse
    {
        $userId = auth()->id();

        $result = $this->periodService->closePeriod($periodId, $userId);

        if (! $result['success']) {
            return response()->json([
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'message' => $result['message'],
            'period' => $result['data'],
        ]);
    }

    /**
     * Reopen a period.
     */
    public function openPeriod(int $periodId): JsonResponse
    {
        $result = $this->periodService->openPeriod($periodId);

        if (! $result['success']) {
            return response()->json([
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'message' => $result['message'],
            'period' => $result['data'],
        ]);
    }

    /**
     * Generate period code and name from date.
     */
    public function generateFromDate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        return response()->json([
            'per_code' => $this->periodService->generatePeriodCode($validated['date']),
            'per_name' => $this->periodService->generatePeriodName($validated['date']),
        ]);
    }
}
