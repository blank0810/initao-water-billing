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

    // ========================================================================
    // Service Connection Area Assignment Endpoints
    // ========================================================================

    /**
     * Get service connections without area assignment.
     */
    public function getConnectionsWithoutArea(Request $request): JsonResponse
    {
        $search = $request->input('search', '');
        $barangayId = $request->has('barangay_id') ? (int) $request->input('barangay_id') : null;
        $limit = (int) $request->input('limit', 100);

        $connections = $this->areaService->getConnectionsWithoutArea($search, $barangayId, $limit);

        return response()->json([
            'success' => true,
            'data' => $connections,
        ]);
    }

    /**
     * Get service connections by area.
     */
    public function getConnectionsByArea(Request $request): JsonResponse
    {
        $areaId = $request->input('area_id') !== null ? (int) $request->input('area_id') : null;
        $search = $request->input('search', '');
        $barangayId = $request->has('barangay_id') ? (int) $request->input('barangay_id') : null;
        $limit = (int) $request->input('limit', 100);

        $connections = $this->areaService->getConnectionsByArea($areaId, $search, $barangayId, $limit);

        return response()->json([
            'success' => true,
            'data' => $connections,
        ]);
    }

    /**
     * Assign area to service connections.
     */
    public function assignAreaToConnections(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'area_id' => 'required|integer|exists:area,a_id',
            'connection_ids' => 'required|array|min:1',
            'connection_ids.*' => 'integer|exists:ServiceConnection,connection_id',
        ]);

        $result = $this->areaService->assignAreaToConnections(
            $validated['area_id'],
            $validated['connection_ids']
        );

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Remove area assignment from service connections.
     */
    public function removeAreaFromConnections(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'connection_ids' => 'required|array|min:1',
            'connection_ids.*' => 'integer|exists:ServiceConnection,connection_id',
        ]);

        $result = $this->areaService->removeAreaFromConnections($validated['connection_ids']);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Get connection area assignment statistics.
     */
    public function connectionAreaStats(): JsonResponse
    {
        $stats = $this->areaService->getConnectionAreaStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Auto-assign unassigned connections to areas based on barangay.
     */
    public function autoAssignConnections(): JsonResponse
    {
        $result = $this->areaService->autoAssignByBarangay();

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
