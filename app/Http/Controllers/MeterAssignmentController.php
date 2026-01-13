<?php

namespace App\Http\Controllers;

use App\Services\Meter\MeterAssignmentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeterAssignmentController extends Controller
{
    public function __construct(
        private MeterAssignmentService $assignmentService
    ) {}

    /**
     * Get all meter assignments.
     */
    public function index(Request $request): JsonResponse
    {
        $showAll = $request->boolean('all', false);

        $assignments = $showAll
            ? $this->assignmentService->getAllAssignments()
            : $this->assignmentService->getActiveAssignments();

        $stats = $this->assignmentService->getStats();

        return response()->json([
            'success' => true,
            'data' => $assignments,
            'stats' => $stats,
        ]);
    }

    /**
     * Get assignment details.
     */
    public function show(int $assignmentId): JsonResponse
    {
        $assignment = $this->assignmentService->getAssignmentDetails($assignmentId);

        if (! $assignment) {
            return response()->json([
                'success' => false,
                'message' => 'Assignment not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $assignment,
        ]);
    }

    /**
     * Get available meters for assignment.
     */
    public function getAvailableMeters(): JsonResponse
    {
        $meters = $this->assignmentService->getAvailableMeters();

        return response()->json([
            'success' => true,
            'data' => $meters,
        ]);
    }

    /**
     * Get unassigned service connections.
     */
    public function getUnassignedConnections(): JsonResponse
    {
        $connections = $this->assignmentService->getUnassignedConnections();

        return response()->json([
            'success' => true,
            'data' => $connections,
        ]);
    }

    /**
     * Assign a meter to a service connection.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'connection_id' => 'required|integer|exists:ServiceConnection,connection_id',
            'meter_id' => 'required|integer|exists:meter,mtr_id',
            'installed_at' => 'nullable|date',
            'install_read' => 'nullable|numeric|min:0',
        ]);

        try {
            $assignment = $this->assignmentService->assignMeter(
                (int) $validated['connection_id'],
                (int) $validated['meter_id'],
                (float) ($validated['install_read'] ?? 0),
                Carbon::parse($validated['installed_at'] ?? now())
            );

            return response()->json([
                'success' => true,
                'message' => 'Meter assigned successfully.',
                'data' => $assignment,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Remove a meter from a service connection.
     */
    public function remove(Request $request, int $assignmentId): JsonResponse
    {
        $validated = $request->validate([
            'removed_at' => 'nullable|date',
            'removal_read' => 'nullable|numeric|min:0',
        ]);

        $result = $this->assignmentService->removeMeter($assignmentId, $validated);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Get assignment statistics.
     */
    public function stats(): JsonResponse
    {
        $stats = $this->assignmentService->getStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
