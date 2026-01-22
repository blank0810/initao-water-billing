<?php

namespace App\Http\Controllers;

use App\Models\UploadedReading;
use App\Services\Billing\WaterBillService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadedReadingController extends Controller
{
    public function __construct(
        private WaterBillService $billService
    ) {}

    /**
     * Get all uploaded readings with optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = UploadedReading::query();

        // Filter by period (through schedule relationship)
        if ($request->has('period_id') && $request->period_id) {
            $query->whereHas('schedule', function ($q) use ($request) {
                $q->where('period_id', $request->period_id);
            });
        }

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
                    'is_processed' => $reading->is_processed ?? false,
                    'processed_at' => $reading->processed_at?->format('Y-m-d H:i:s'),
                    'bill_id' => $reading->bill_id,
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

    /**
     * Process selected uploaded readings into bills.
     */
    public function processReadings(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reading_ids' => 'required|array|min:1',
            'reading_ids.*' => 'integer|exists:uploaded_readings,uploaded_reading_id',
        ]);

        $result = $this->billService->processUploadedReadings($validated['reading_ids']);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Get processing status summary for uploaded readings.
     */
    public function processingStats(Request $request): JsonResponse
    {
        $stats = $this->billService->getUploadedReadingsProcessingStats(
            $request->input('period_id'),
            $request->input('schedule_id')
        );

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }
}
