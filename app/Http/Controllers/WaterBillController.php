<?php

namespace App\Http\Controllers;

use App\Services\Billing\WaterBillService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WaterBillController extends Controller
{
    public function __construct(
        private WaterBillService $billService
    ) {}

    /**
     * Get billable connections (service connections with active meters).
     */
    public function getBillableConnections(): JsonResponse
    {
        $connections = $this->billService->getBillableConnections();

        return response()->json([
            'success' => true,
            'data' => $connections,
        ]);
    }

    /**
     * Get billing periods.
     */
    public function getBillingPeriods(): JsonResponse
    {
        $periods = $this->billService->getBillingPeriods();

        return response()->json([
            'success' => true,
            'data' => $periods['periods'],
            'activePeriodId' => $periods['activePeriodId'],
        ]);
    }

    /**
     * Get last reading for a connection.
     */
    public function getLastReading(int $connectionId): JsonResponse
    {
        $lastReading = $this->billService->getLastReading($connectionId);

        if (! $lastReading) {
            return response()->json([
                'success' => false,
                'message' => 'Connection not found or no meter assigned.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $lastReading,
        ]);
    }

    /**
     * Calculate bill preview.
     */
    public function previewBill(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'connection_id' => 'required|integer|exists:ServiceConnection,connection_id',
            'period_id' => 'nullable|integer|exists:Period,per_id',
            'prev_reading' => 'required|numeric|min:0',
            'curr_reading' => 'required|numeric|min:0',
        ]);

        $result = $this->billService->previewBill($validated);

        return response()->json($result);
    }

    /**
     * Generate water bill.
     */
    public function generateBill(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'connection_id' => 'required|integer|exists:ServiceConnection,connection_id',
            'period_id' => 'required|integer|exists:Period,per_id',
            'prev_reading' => 'required|numeric|min:0',
            'curr_reading' => 'required|numeric|min:0',
            'reading_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'meter_reader_id' => 'nullable|integer',
        ]);

        $result = $this->billService->generateBill($validated);

        return response()->json($result, $result['success'] ? 201 : 422);
    }

    /**
     * Get billing details for a connection.
     */
    public function getConnectionBillingDetails(int $connectionId): JsonResponse
    {
        try {
            $details = $this->billService->getConnectionBillingDetails($connectionId);

            if (! $details) {
                return response()->json([
                    'success' => false,
                    'message' => 'Connection not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $details,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading billing details: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all connections with billing summary for consumer billing list.
     */
    public function getConsumerBillingList(): JsonResponse
    {
        try {
            $consumers = $this->billService->getConsumerBillingList();

            return response()->json([
                'success' => true,
                'data' => $consumers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading consumer list: '.$e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    /**
     * Get billed consumers for a specific period.
     */
    public function getBilledConsumersByPeriod(Request $request): JsonResponse
    {
        try {
            $periodId = $request->input('period_id') ? (int) $request->input('period_id') : null;
            $result = $this->billService->getBilledConsumersByPeriod($periodId);

            return response()->json([
                'success' => true,
                'data' => $result['consumers'],
                'statistics' => $result['statistics'],
                'period_id' => $result['period_id'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading billed consumers: '.$e->getMessage(),
                'data' => [],
                'statistics' => [],
            ], 500);
        }
    }

    /**
     * Get billing summary statistics (for summary cards).
     */
    public function getBillingSummary(): JsonResponse
    {
        try {
            $summary = $this->billService->getBillingSummary();

            return response()->json([
                'success' => true,
                'data' => $summary,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading billing summary: '.$e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
