<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Config\StoreWaterRateRequest;
use App\Http\Requests\Admin\Config\UpdateWaterRateRequest;
use App\Services\Admin\Config\WaterRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class WaterRateController extends Controller
{
    public function __construct(
        private WaterRateService $waterRateService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        if ($request->expectsJson()) {
            $periodId = $request->query('period');
            $rates = $this->waterRateService->getAllRates($periodId);

            return response()->json($rates);
        }

        return view('pages.admin.config.water-rates.index');
    }

    public function getAccountTypes(): JsonResponse
    {
        $accountTypes = $this->waterRateService->getAccountTypes();

        return response()->json([
            'data' => $accountTypes,
        ]);
    }

    public function store(StoreWaterRateRequest $request): JsonResponse
    {
        try {
            // Validate no range overlap
            $this->waterRateService->validateNoRangeOverlap(
                $request->validated()['class_id'],
                $request->validated()['period_id'] ?? null,
                $request->validated()
            );

            $tier = $this->waterRateService->createOrUpdateRateTier($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Rate tier created successfully',
                'data' => $tier,
            ], 201);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Failed to create water rate tier', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create rate tier',
            ], 500);
        }
    }

    public function update(UpdateWaterRateRequest $request, int $id): JsonResponse
    {
        try {
            // For update, we pass the data with the tier ID
            $data = $request->validated();
            $tier = $this->waterRateService->createOrUpdateRateTier($data);

            return response()->json([
                'success' => true,
                'message' => 'Rate tier updated successfully',
                'data' => $tier,
            ]);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Failed to update water rate tier', [
                'tier_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update rate tier',
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->waterRateService->deleteRateTier($id);

            return response()->json([
                'success' => true,
                'message' => 'Rate tier deleted successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete water rate tier', [
                'tier_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete rate tier',
            ], 500);
        }
    }
}
