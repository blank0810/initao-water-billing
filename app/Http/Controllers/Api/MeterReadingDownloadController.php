<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Billing\MeterReadingDownloadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeterReadingDownloadController extends Controller
{
    protected MeterReadingDownloadService $downloadService;

    public function __construct(MeterReadingDownloadService $downloadService)
    {
        $this->downloadService = $downloadService;
    }

    /**
     * Get consumer information for active reading schedules assigned to the authenticated user.
     *
     * Returns consumer details for all connections in active schedules (pending/in_progress)
     * assigned to the authenticated user, including customer name, account number, address,
     * meter serial, and previous reading.
     */
    public function getConsumerInfo(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $result = $this->downloadService->getConsumerInfoByUser($userId);

        if (empty($result['schedules'])) {
            return response()->json([
                'success' => false,
                'message' => 'No active reading schedules found for this user.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Consumer information retrieved successfully.',
            'schedule_count' => count($result['schedules']),
            'consumer_count' => count($result['consumers']),
            'data' => $result,
        ]);
    }

    /**
     * Get water rates for the current active period.
     *
     * Returns water rate tiers including class_id, range_min, range_max,
     * rate_val, and rate_increment for the current billing period.
     */
    public function getCurrentPeriodRates(): JsonResponse
    {
        $result = $this->downloadService->getCurrentPeriodWaterRates();

        if ($result['period'] === null) {
            return response()->json([
                'success' => false,
                'message' => 'No active period found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Water rates retrieved successfully.',
            'data' => $result,
        ]);
    }

    /**
     * Get water rates for a specific period.
     */
    public function getRatesByPeriod(int $periodId): JsonResponse
    {
        $result = $this->downloadService->getWaterRatesByPeriod($periodId);

        if ($result['period'] === null) {
            return response()->json([
                'success' => false,
                'message' => 'Period not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Water rates retrieved successfully.',
            'data' => $result,
        ]);
    }
}
