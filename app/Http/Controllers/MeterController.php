<?php

namespace App\Http\Controllers;

use App\Services\Meter\MeterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeterController extends Controller
{
    public function __construct(
        private MeterService $meterService
    ) {}

    /**
     * Get all meters for inventory table.
     */
    public function index(): JsonResponse
    {
        $meters = $this->meterService->getAllMetersWithStatus();
        $stats = $this->meterService->getStats();

        return response()->json([
            'success' => true,
            'data' => $meters,
            'stats' => $stats,
        ]);
    }

    /**
     * Get meter details.
     */
    public function show(int $meterId): JsonResponse
    {
        $meter = $this->meterService->getMeterDetails($meterId);

        if (! $meter) {
            return response()->json([
                'success' => false,
                'message' => 'Meter not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $meter,
        ]);
    }

    /**
     * Create a new meter.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mtr_serial' => 'required|string|max:50',
            'mtr_brand' => 'required|string|max:100',
        ]);

        $result = $this->meterService->createMeter($validated);

        return response()->json($result, $result['success'] ? 201 : 422);
    }

    /**
     * Update a meter.
     */
    public function update(Request $request, int $meterId): JsonResponse
    {
        $validated = $request->validate([
            'mtr_serial' => 'sometimes|required|string|max:50',
            'mtr_brand' => 'sometimes|required|string|max:100',
        ]);

        $result = $this->meterService->updateMeter($meterId, $validated);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Delete a meter.
     */
    public function destroy(int $meterId): JsonResponse
    {
        $result = $this->meterService->deleteMeter($meterId);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Get inventory statistics.
     */
    public function stats(): JsonResponse
    {
        $stats = $this->meterService->getStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Mark a meter as faulty.
     */
    public function markFaulty(int $meterId): JsonResponse
    {
        $result = $this->meterService->markAsFaulty($meterId);

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
