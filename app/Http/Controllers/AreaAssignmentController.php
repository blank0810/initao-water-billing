<?php

namespace App\Http\Controllers;

use App\Services\Billing\AreaAssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AreaAssignmentController extends Controller
{
    public function __construct(
        private AreaAssignmentService $assignmentService
    ) {}

    /**
     * Get all area assignments.
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
     * Get available meter readers for assignment.
     */
    public function getMeterReaders(): JsonResponse
    {
        $meterReaders = $this->assignmentService->getAvailableMeterReaders();

        return response()->json([
            'success' => true,
            'data' => $meterReaders,
        ]);
    }

    /**
     * Get assignments by area.
     */
    public function byArea(int $areaId): JsonResponse
    {
        $assignments = $this->assignmentService->getAssignmentsByArea($areaId);

        return response()->json([
            'success' => true,
            'data' => $assignments,
        ]);
    }

    /**
     * Get assignments by user.
     */
    public function byUser(int $userId): JsonResponse
    {
        $assignments = $this->assignmentService->getAssignmentsByUser($userId);

        return response()->json([
            'success' => true,
            'data' => $assignments,
        ]);
    }

    /**
     * Create a new area assignment.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'area_id' => 'required|integer|exists:area,a_id',
            'user_id' => 'required|integer|exists:users,id',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
        ]);

        $result = $this->assignmentService->createAssignment($validated);

        return response()->json($result, $result['success'] ? 201 : 422);
    }

    /**
     * Update an existing assignment.
     */
    public function update(Request $request, int $assignmentId): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'sometimes|required|integer|exists:users,id',
            'effective_from' => 'sometimes|required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
        ]);

        $result = $this->assignmentService->updateAssignment($assignmentId, $validated);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * End an assignment.
     */
    public function end(Request $request, int $assignmentId): JsonResponse
    {
        $validated = $request->validate([
            'effective_to' => 'required|date',
        ]);

        $result = $this->assignmentService->endAssignment($assignmentId, $validated['effective_to']);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Delete an assignment.
     */
    public function destroy(int $assignmentId): JsonResponse
    {
        $result = $this->assignmentService->deleteAssignment($assignmentId);

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
