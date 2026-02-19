<?php

namespace App\Http\Controllers;

use App\Models\PenaltyConfiguration;
use App\Services\Billing\PenaltyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PenaltyController extends Controller
{
    public function __construct(
        private PenaltyService $penaltyService
    ) {}

    /**
     * Show the penalty configuration page.
     */
    public function index(): View
    {
        return view('pages.admin.config.penalty.index');
    }

    /**
     * Get penalty configuration change history.
     */
    public function getHistory(): JsonResponse
    {
        $history = PenaltyConfiguration::with('createdByUser')
            ->orderByDesc('effective_date')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($item) => [
                'penalty_config_id' => $item->penalty_config_id,
                'rate_percentage' => (float) $item->rate_percentage,
                'effective_date' => $item->effective_date->toDateString(),
                'is_active' => $item->is_active,
                'created_by_name' => $item->createdByUser?->name,
                'created_at' => $item->created_at->format('Y-m-d H:i'),
            ]);

        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }

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

    /**
     * Process a batch of overdue bills for progress tracking.
     */
    public function processBatch(Request $request): JsonResponse
    {
        $userId = Auth::id();

        if (! $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.',
            ], 401);
        }

        $limit = (int) $request->input('limit', 50);

        $result = $this->penaltyService->processBatch($userId, $limit);

        return response()->json($result);
    }

    /**
     * Get the current active penalty configuration.
     */
    public function getConfig(): JsonResponse
    {
        $config = PenaltyConfiguration::where('is_active', true)
            ->where('effective_date', '<=', now())
            ->orderByDesc('effective_date')
            ->first();

        if (! $config) {
            return response()->json([
                'success' => true,
                'data' => [
                    'rate_percentage' => PenaltyConfiguration::DEFAULT_RATE_PERCENTAGE,
                    'effective_date' => now()->toDateString(),
                    'updated_by_name' => null,
                ],
            ]);
        }

        $config->load('updatedByUser', 'createdByUser');

        return response()->json([
            'success' => true,
            'data' => [
                'rate_percentage' => (float) $config->rate_percentage,
                'effective_date' => $config->effective_date->toDateString(),
                'updated_by_name' => $config->updatedByUser?->name ?? $config->createdByUser?->name,
            ],
        ]);
    }

    /**
     * Update the penalty rate by deactivating old config and creating a new active row.
     */
    public function updateRate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rate_percentage' => 'required|numeric|min:0.01|max:100',
        ]);

        $userId = Auth::id();

        if (! $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.',
            ], 401);
        }

        try {
            DB::transaction(function () use ($validated, $userId) {
                // Deactivate all current active configs
                PenaltyConfiguration::where('is_active', true)
                    ->update(['is_active' => false, 'updated_by' => $userId, 'updated_at' => now()]);

                // Create new active config
                PenaltyConfiguration::create([
                    'rate_percentage' => $validated['rate_percentage'],
                    'is_active' => true,
                    'effective_date' => now()->toDateString(),
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Penalty rate updated successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update penalty rate', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update penalty rate.',
            ], 500);
        }
    }
}
