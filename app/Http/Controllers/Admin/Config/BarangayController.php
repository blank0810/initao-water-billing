<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Config\StoreBarangayRequest;
use App\Http\Requests\Admin\Config\UpdateBarangayRequest;
use App\Services\Admin\Config\BarangayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class BarangayController extends Controller
{
    public function __construct(
        private BarangayService $barangayService
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

                $result = $this->barangayService->getAllBarangays($filters);

                return response()->json($result);

            } catch (\Exception $e) {
                Log::error('Failed to fetch barangays', [
                    'error' => $e->getMessage(),
                    'filters' => $filters ?? [],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch barangays',
                ], 500);
            }
        }

        // Otherwise return the view
        return view('pages.admin.config.barangays.index');
    }

    public function store(StoreBarangayRequest $request): JsonResponse
    {
        try {
            $barangay = $this->barangayService->createBarangay($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Barangay created successfully',
                'data' => $barangay,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create barangay', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create barangay',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $barangay = $this->barangayService->getBarangayDetails($id);

            return response()->json([
                'data' => $barangay,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Barangay not found',
            ], 404);
        }
    }

    public function update(UpdateBarangayRequest $request, int $id): JsonResponse
    {
        try {
            $barangay = $this->barangayService->updateBarangay($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Barangay updated successfully',
                'data' => $barangay,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update barangay', [
                'barangay_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update barangay',
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->barangayService->deleteBarangay($id);

            return response()->json([
                'success' => true,
                'message' => 'Barangay deleted successfully',
            ]);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Failed to delete barangay', [
                'barangay_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred',
            ], 500);
        }
    }
}
