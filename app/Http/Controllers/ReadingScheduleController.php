<?php

namespace App\Http\Controllers;

use App\Services\Billing\ReadingScheduleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReadingScheduleController extends Controller
{
    public function __construct(
        private ReadingScheduleService $scheduleService
    ) {}

    /**
     * Get all reading schedules.
     */
    public function index(Request $request): JsonResponse
    {
        $status = $request->get('status');

        $schedules = $status
            ? $this->scheduleService->getSchedulesByStatus($status)
            : $this->scheduleService->getAllSchedules();

        $stats = $this->scheduleService->getStats();

        return response()->json([
            'success' => true,
            'data' => $schedules,
            'stats' => $stats,
        ]);
    }

    /**
     * Get schedule details.
     */
    public function show(int $scheduleId): JsonResponse
    {
        $schedule = $this->scheduleService->getScheduleById($scheduleId);

        if (! $schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $schedule,
        ]);
    }

    /**
     * Get schedules by period.
     */
    public function byPeriod(int $periodId): JsonResponse
    {
        $schedules = $this->scheduleService->getSchedulesByPeriod($periodId);

        return response()->json([
            'success' => true,
            'data' => $schedules,
        ]);
    }

    /**
     * Get schedules by area.
     */
    public function byArea(int $areaId): JsonResponse
    {
        $schedules = $this->scheduleService->getSchedulesByArea($areaId);

        return response()->json([
            'success' => true,
            'data' => $schedules,
        ]);
    }

    /**
     * Get schedules by reader.
     */
    public function byReader(int $readerId): JsonResponse
    {
        $schedules = $this->scheduleService->getSchedulesByReader($readerId);

        return response()->json([
            'success' => true,
            'data' => $schedules,
        ]);
    }

    /**
     * Get available meter readers for scheduling.
     */
    public function getMeterReaders(): JsonResponse
    {
        $meterReaders = $this->scheduleService->getAvailableMeterReaders();

        return response()->json([
            'success' => true,
            'data' => $meterReaders,
        ]);
    }

    /**
     * Get available areas for scheduling.
     */
    public function getAreas(): JsonResponse
    {
        $areas = $this->scheduleService->getAvailableAreas();

        return response()->json([
            'success' => true,
            'data' => $areas,
        ]);
    }

    /**
     * Get available periods for scheduling.
     */
    public function getPeriods(): JsonResponse
    {
        $periods = $this->scheduleService->getAvailablePeriods();

        return response()->json([
            'success' => true,
            'data' => $periods,
        ]);
    }

    /**
     * Create a new reading schedule.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'period_id' => 'required|integer|exists:period,per_id',
            'area_id' => 'required|integer|exists:area,a_id',
            'reader_id' => 'required|integer|exists:users,id',
            'scheduled_start_date' => 'required|date',
            'scheduled_end_date' => 'required|date|after_or_equal:scheduled_start_date',
            'notes' => 'nullable|string|max:1000',
            'total_meters' => 'nullable|integer|min:0',
        ]);

        $result = $this->scheduleService->createSchedule($validated);

        return response()->json($result, $result['success'] ? 201 : 422);
    }

    /**
     * Update an existing schedule.
     */
    public function update(Request $request, int $scheduleId): JsonResponse
    {
        $validated = $request->validate([
            'reader_id' => 'sometimes|required|integer|exists:users,id',
            'scheduled_start_date' => 'sometimes|required|date',
            'scheduled_end_date' => 'sometimes|required|date|after_or_equal:scheduled_start_date',
            'actual_start_date' => 'nullable|date',
            'actual_end_date' => 'nullable|date|after_or_equal:actual_start_date',
            'status' => 'sometimes|required|in:pending,in_progress,completed,delayed',
            'notes' => 'nullable|string|max:1000',
            'total_meters' => 'nullable|integer|min:0',
            'meters_read' => 'nullable|integer|min:0',
            'meters_missed' => 'nullable|integer|min:0',
        ]);

        $result = $this->scheduleService->updateSchedule($scheduleId, $validated);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Start a schedule.
     */
    public function start(int $scheduleId): JsonResponse
    {
        $result = $this->scheduleService->startSchedule($scheduleId);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Complete a schedule.
     */
    public function complete(Request $request, int $scheduleId): JsonResponse
    {
        $validated = $request->validate([
            'meters_read' => 'nullable|integer|min:0',
            'meters_missed' => 'nullable|integer|min:0',
        ]);

        $result = $this->scheduleService->completeSchedule($scheduleId, $validated);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Mark schedule as delayed.
     */
    public function delay(Request $request, int $scheduleId): JsonResponse
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $result = $this->scheduleService->markAsDelayed($scheduleId, $validated['notes'] ?? null);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Delete a schedule.
     */
    public function destroy(int $scheduleId): JsonResponse
    {
        $result = $this->scheduleService->deleteSchedule($scheduleId);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Get schedule statistics.
     */
    public function stats(): JsonResponse
    {
        $stats = $this->scheduleService->getStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}