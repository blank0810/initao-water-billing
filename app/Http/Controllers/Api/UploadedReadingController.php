<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReadingSchedule;
use App\Models\UploadedReading;
use App\Services\Billing\UploadedReadingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UploadedReadingController extends Controller
{
    public function __construct(
        private UploadedReadingService $uploadedReadingService
    ) {}

    /**
     * Upload readings from mobile device.
     * Accepts an array of readings with consumer info and device data.
     */
    public function upload(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'readings' => ['required', 'array', 'min:1'],
            'readings.*.schedule_id' => ['required', 'integer', 'exists:reading_schedule,schedule_id'],
            'readings.*.connection_id' => ['required', 'integer', 'exists:ServiceConnection,connection_id'],
            'readings.*.account_no' => ['nullable', 'string', 'max:50'],
            'readings.*.customer_name' => ['nullable', 'string', 'max:255'],
            'readings.*.address' => ['nullable', 'string', 'max:500'],
            'readings.*.area_desc' => ['nullable', 'string', 'max:100'],
            'readings.*.account_type_desc' => ['nullable', 'string', 'max:100'],
            'readings.*.connection_status' => ['nullable', 'string', 'max:50'],
            'readings.*.meter_serial' => ['nullable', 'string', 'max:50'],
            'readings.*.previous_reading' => ['nullable', 'numeric'],
            'readings.*.arrear' => ['nullable', 'numeric'],
            'readings.*.penalty' => ['nullable', 'numeric'],
            'readings.*.sequence_order' => ['nullable', 'integer'],
            'readings.*.entry_status' => ['nullable', 'string', 'max:50'],
            'readings.*.present_reading' => ['nullable', 'numeric'],
            'readings.*.reading_date' => ['nullable', 'date'],
            'readings.*.site_bill_amount' => ['nullable', 'numeric'],
            'readings.*.computed_amount' => ['nullable', 'numeric'],
            'readings.*.is_printed' => ['nullable', 'boolean'],
            'readings.*.is_scanned' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Use authenticated user's ID instead of client-supplied value
        $authUserId = $request->user()->id;

        $result = $this->uploadedReadingService->processUploadedReadings(
            $request->input('readings'),
            $authUserId
        );

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Get uploaded readings for a specific schedule.
     * Only the assigned reader can access their schedule's readings.
     */
    public function getBySchedule(Request $request, int $scheduleId): JsonResponse
    {
        $authUserId = $request->user()->id;

        // Verify user is assigned to this schedule
        $schedule = ReadingSchedule::find($scheduleId);
        if (! $schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found.',
            ], 404);
        }

        if ($schedule->reader_id !== $authUserId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You are not assigned to this schedule.',
            ], 403);
        }

        $readings = UploadedReading::where('schedule_id', $scheduleId)
            ->orderBy('sequence_order')
            ->get();

        if ($readings->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No uploaded readings found for this schedule.',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Uploaded readings retrieved successfully.',
            'count' => $readings->count(),
            'data' => $readings->toArray(),
        ]);
    }

    /**
     * Get uploaded readings for a specific user.
     * Users can only access their own readings.
     */
    public function getByUser(Request $request, int $userId): JsonResponse
    {
        $authUserId = $request->user()->id;

        // Users can only access their own readings
        if ($userId !== $authUserId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only access your own readings.',
            ], 403);
        }

        $readings = UploadedReading::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($readings->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No uploaded readings found for this user.',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Uploaded readings retrieved successfully.',
            'count' => $readings->count(),
            'data' => $readings->toArray(),
        ]);
    }

    /**
     * Get uploaded readings for the authenticated user.
     * Convenience endpoint that uses the authenticated user's ID.
     */
    public function getMyReadings(Request $request): JsonResponse
    {
        $authUserId = $request->user()->id;

        $readings = UploadedReading::where('user_id', $authUserId)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($readings->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No uploaded readings found.',
                'data' => [],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Uploaded readings retrieved successfully.',
            'count' => $readings->count(),
            'data' => $readings->toArray(),
        ]);
    }
}
