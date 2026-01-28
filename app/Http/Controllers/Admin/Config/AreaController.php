<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Config\StoreAreaRequest;
use App\Http\Requests\Admin\Config\UpdateAreaRequest;
use App\Services\Admin\Config\AreaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AreaController extends Controller
{
    public function __construct(
        private AreaService $areaService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        // If JSON is requested, return data for AJAX requests
        if ($request->wantsJson()) {
            try {
                $filters = [
                    'search' => $request->input('search', ''),
                    'status' => $request->input('status', ''),
                    'per_page' => $request->input('per_page', 15),
                ];

                $result = $this->areaService->getAllAreas($filters);

                return response()->json($result);

            } catch (\Exception $e) {
                Log::error('Failed to fetch areas', [
                    'error' => $e->getMessage(),
                    'filters' => $filters ?? [],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch areas',
                ], 500);
            }
        }

        // Otherwise return the view
        return view('pages.admin.config.areas.index');
    }

    public function store(StoreAreaRequest $request): JsonResponse
    {
        try {
            $area = $this->areaService->createArea($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Area created successfully',
                'data' => $area,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create area', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create area',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $area = $this->areaService->getAreaDetails($id);

            return response()->json([
                'success' => true,
                'data' => $area,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Area not found',
            ], 404);

        } catch (\Exception $e) {
            Log::error('Failed to fetch area details', [
                'area_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
            ], 500);
        }
    }

    public function update(UpdateAreaRequest $request, int $id): JsonResponse
    {
        try {
            $area = $this->areaService->updateArea($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Area updated successfully',
                'data' => $area,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Area not found',
            ], 404);

        } catch (\Exception $e) {
            Log::error('Failed to update area', [
                'area_id' => $id,
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update area',
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->areaService->deleteArea($id);

            return response()->json([
                'success' => true,
                'message' => 'Area deleted successfully',
            ]);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Area not found',
            ], 404);

        } catch (\Exception $e) {
            Log::error('Failed to delete area', [
                'area_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
            ], 500);
        }
    }
}
