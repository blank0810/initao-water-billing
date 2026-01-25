<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Config\StoreChargeItemRequest;
use App\Http\Requests\Admin\Config\UpdateChargeItemRequest;
use App\Services\Admin\Config\ChargeItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ChargeItemController extends Controller
{
    public function __construct(
        private ChargeItemService $chargeItemService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        // If JSON is requested, return data for AJAX requests
        if ($request->wantsJson()) {
            try {
                $filters = [
                    'search' => $request->input('search', ''),
                    'status' => $request->input('status', ''),
                    'charge_type' => $request->input('charge_type', ''),
                    'per_page' => $request->input('per_page', 15),
                ];

                $result = $this->chargeItemService->getAllChargeItems($filters);

                return response()->json($result);

            } catch (\Exception $e) {
                Log::error('Failed to fetch charge items', [
                    'error' => $e->getMessage(),
                    'filters' => $filters ?? [],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch charge items',
                ], 500);
            }
        }

        // Otherwise return the view
        return view('pages.admin.config.charge-items.index');
    }

    public function store(StoreChargeItemRequest $request): JsonResponse
    {
        try {
            $chargeItem = $this->chargeItemService->createChargeItem($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Charge item created successfully',
                'data' => $chargeItem->load('status'),
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create charge item', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create charge item',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $chargeItem = $this->chargeItemService->getChargeItemDetails($id);

            return response()->json([
                'data' => $chargeItem,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Charge item not found',
            ], 404);
        }
    }

    public function update(UpdateChargeItemRequest $request, int $id): JsonResponse
    {
        try {
            $chargeItem = $this->chargeItemService->updateChargeItem($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Charge item updated successfully',
                'data' => $chargeItem->load('status'),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update charge item', [
                'charge_item_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update charge item',
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->chargeItemService->deleteChargeItem($id);

            return response()->json([
                'success' => true,
                'message' => 'Charge item deleted successfully',
            ]);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Failed to delete charge item', [
                'charge_item_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete charge item',
            ], 500);
        }
    }
}
