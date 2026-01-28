<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Config\StorePurokRequest;
use App\Http\Requests\Admin\Config\UpdatePurokRequest;
use App\Services\Admin\Config\PurokService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PurokController extends Controller
{
    public function __construct(
        private PurokService $purokService
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

                $result = $this->purokService->getAllPuroks($filters);

                return response()->json($result);

            } catch (\Exception $e) {
                Log::error('Failed to fetch puroks', [
                    'error' => $e->getMessage(),
                    'filters' => $filters ?? [],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch puroks',
                ], 500);
            }
        }

        // Otherwise return the view
        return view('pages.admin.config.puroks.index');
    }

    public function store(StorePurokRequest $request): JsonResponse
    {
        try {
            $purok = $this->purokService->createPurok($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Purok created successfully',
                'data' => $purok->load('status'),
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create purok', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create purok',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $purok = $this->purokService->getPurokDetails($id);

            return response()->json([
                'data' => $purok,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Purok not found',
            ], 404);
        }
    }

    public function update(UpdatePurokRequest $request, int $id): JsonResponse
    {
        try {
            $purok = $this->purokService->updatePurok($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Purok updated successfully',
                'data' => $purok->load('status'),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update purok', [
                'purok_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update purok',
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->purokService->deletePurok($id);

            return response()->json([
                'success' => true,
                'message' => 'Purok deleted successfully',
            ]);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Failed to delete purok', [
                'purok_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete purok',
            ], 500);
        }
    }
}
