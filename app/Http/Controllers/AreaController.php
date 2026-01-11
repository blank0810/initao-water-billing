<?php

namespace App\Http\Controllers;

use App\Services\Billing\AreaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function __construct(
        private AreaService $areaService
    ) {}

    /**
     * Get all areas.
     */
    public function index(Request $request): JsonResponse
    {
        $showAll = $request->boolean('all', true);

        $areas = $showAll
            ? $this->areaService->getAllAreas()
            : $this->areaService->getActiveAreas();

        $stats = $this->areaService->getStats();

        return response()->json([
            'success' => true,
            'data' => $areas,
            'stats' => $stats,
        ]);
    }

    /**
     * Get area details.
     */
    public function show(int $areaId): JsonResponse
    {
        $area = $this->areaService->getAreaById($areaId);

        if (! $area) {
            return response()->json([
                'success' => false,
                'message' => 'Area not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $area,
        ]);
    }

    /**
     * Create a new area.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'a_desc' => 'required|string|max:255',
            'stat_id' => 'nullable|integer|exists:status,stat_id',
        ]);

        $result = $this->areaService->createArea($validated);

        return response()->json($result, $result['success'] ? 201 : 422);
    }

    /**
     * Update an existing area.
     */
    public function update(Request $request, int $areaId): JsonResponse
    {
        $validated = $request->validate([
            'a_desc' => 'sometimes|required|string|max:255',
            'stat_id' => 'nullable|integer|exists:status,stat_id',
        ]);

        $result = $this->areaService->updateArea($areaId, $validated);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Delete an area.
     */
    public function destroy(int $areaId): JsonResponse
    {
        $result = $this->areaService->deleteArea($areaId);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Get area statistics.
     */
    public function stats(): JsonResponse
    {
        $stats = $this->areaService->getStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
