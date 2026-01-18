<?php

namespace App\Http\Controllers;

use App\Models\UploadedReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadedReadingController extends Controller
{
    /**
     * Get all uploaded readings with optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = UploadedReading::query();

        // Filter by schedule
        if ($request->has('schedule_id') && $request->schedule_id) {
            $query->where('schedule_id', $request->schedule_id);
        }

        // Filter by user (reader)
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Order by most recent first
        $readings = $query->orderBy('created_at', 'desc')->get();

        // Calculate stats
        $stats = [
            'total' => $readings->count(),
            'printed' => $readings->where('is_printed', true)->count(),
            'scanned' => $readings->where('is_scanned', true)->count(),
            'total_amount' => $readings->sum('computed_amount') ?: $readings->sum('site_bill_amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => $readings->map(function ($reading) {
                return [
                    'uploaded_reading_id' => $reading->uploaded_reading_id,
                    'schedule_id' => $reading->schedule_id,
                    'connection_id' => $reading->connection_id,
                    'account_no' => $reading->account_no,
                    'customer_name' => $reading->customer_name,
                    'address' => $reading->address,
                    'area_desc' => $reading->area_desc,
                    'account_type_desc' => $reading->account_type_desc,
                    'connection_status' => $reading->connection_status,
                    'meter_serial' => $reading->meter_serial,
                    'previous_reading' => $reading->previous_reading,
                    'present_reading' => $reading->present_reading,
                    'arrear' => $reading->arrear,
                    'penalty' => $reading->penalty,
                    'sequence_order' => $reading->sequence_order,
                    'entry_status' => $reading->entry_status,
                    'reading_date' => $reading->reading_date?->format('Y-m-d'),
                    'site_bill_amount' => $reading->site_bill_amount,
                    'computed_amount' => $reading->computed_amount,
                    'is_printed' => $reading->is_printed,
                    'is_scanned' => $reading->is_scanned,
                    'user_id' => $reading->user_id,
                    'created_at' => $reading->created_at?->format('Y-m-d H:i:s'),
                ];
            }),
            'stats' => $stats,
        ]);
    }

    /**
     * Get uploaded readings by schedule.
     */
    public function bySchedule(int $scheduleId): JsonResponse
    {
        $readings = UploadedReading::where('schedule_id', $scheduleId)
            ->orderBy('sequence_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $readings,
            'count' => $readings->count(),
        ]);
    }

    /**
     * Get uploaded readings by user.
     */
    public function byUser(int $userId): JsonResponse
    {
        $readings = UploadedReading::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $readings,
            'count' => $readings->count(),
        ]);
    }
}
