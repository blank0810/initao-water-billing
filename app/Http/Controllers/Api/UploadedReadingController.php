<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UploadedReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UploadedReadingController extends Controller
{
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
            'readings.*.is_printed' => ['nullable', 'boolean'],
            'readings.*.is_scanned' => ['nullable', 'boolean'],
            'readings.*.user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $readings = $request->input('readings');
        $uploaded = [];
        $failed = [];

        DB::beginTransaction();
        try {
            foreach ($readings as $index => $readingData) {
                try {
                    // Use updateOrCreate to handle duplicates
                    $uploadedReading = UploadedReading::updateOrCreate(
                        [
                            'schedule_id' => $readingData['schedule_id'],
                            'connection_id' => $readingData['connection_id'],
                        ],
                        [
                            'account_no' => $readingData['account_no'] ?? null,
                            'customer_name' => $readingData['customer_name'] ?? null,
                            'address' => $readingData['address'] ?? null,
                            'area_desc' => $readingData['area_desc'] ?? null,
                            'account_type_desc' => $readingData['account_type_desc'] ?? null,
                            'connection_status' => $readingData['connection_status'] ?? null,
                            'meter_serial' => $readingData['meter_serial'] ?? null,
                            'previous_reading' => $readingData['previous_reading'] ?? null,
                            'arrear' => $readingData['arrear'] ?? 0,
                            'penalty' => $readingData['penalty'] ?? 0,
                            'sequence_order' => $readingData['sequence_order'] ?? 0,
                            'entry_status' => $readingData['entry_status'] ?? null,
                            'present_reading' => $readingData['present_reading'] ?? null,
                            'reading_date' => $readingData['reading_date'] ?? null,
                            'site_bill_amount' => $readingData['site_bill_amount'] ?? null,
                            'is_printed' => $readingData['is_printed'] ?? false,
                            'is_scanned' => $readingData['is_scanned'] ?? false,
                            'user_id' => $readingData['user_id'],
                        ]
                    );

                    $uploaded[] = [
                        'uploaded_reading_id' => $uploadedReading->uploaded_reading_id,
                        'connection_id' => $uploadedReading->connection_id,
                        'account_no' => $uploadedReading->account_no,
                    ];
                } catch (\Exception $e) {
                    $failed[] = [
                        'index' => $index,
                        'connection_id' => $readingData['connection_id'] ?? null,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Readings uploaded successfully.',
                'uploaded_count' => count($uploaded),
                'failed_count' => count($failed),
                'data' => [
                    'uploaded' => $uploaded,
                    'failed' => $failed,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to upload readings.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get uploaded readings for a specific schedule.
     */
    public function getBySchedule(int $scheduleId): JsonResponse
    {
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
     */
    public function getByUser(int $userId): JsonResponse
    {
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
}
