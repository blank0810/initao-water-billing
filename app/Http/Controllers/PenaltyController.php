<?php

namespace App\Http\Controllers;

use App\Services\Billing\PenaltyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PenaltyController extends Controller
{
    public function __construct(
        private PenaltyService $penaltyService
    ) {}

    /**
     * Get summary of overdue bills and penalty status.
     */
    public function summary(): JsonResponse
    {
        $summary = $this->penaltyService->getOverdueBillsSummary();

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    /**
     * Process all overdue bills and create penalties.
     */
    public function process(): JsonResponse
    {
        $userId = Auth::id();

        if (! $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.',
            ], 401);
        }

        $result = $this->penaltyService->processAllOverdueBills($userId);

        return response()->json($result);
    }
}
