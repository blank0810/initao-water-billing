<?php

namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Config\StoreAccountTypeRequest;
use App\Http\Requests\Admin\Config\UpdateAccountTypeRequest;
use App\Services\Admin\Config\AccountTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AccountTypeController extends Controller
{
    public function __construct(
        private AccountTypeService $accountTypeService
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

                $result = $this->accountTypeService->getAllAccountTypes($filters);

                return response()->json($result);

            } catch (\Exception $e) {
                Log::error('Failed to fetch account types', [
                    'error' => $e->getMessage(),
                    'filters' => $filters ?? [],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch account types',
                ], 500);
            }
        }

        // Otherwise return the view
        return view('pages.admin.config.account-types.index');
    }

    public function store(StoreAccountTypeRequest $request): JsonResponse
    {
        try {
            $accountType = $this->accountTypeService->createAccountType($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Account type created successfully',
                'data' => $accountType->load('status'),
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create account type', [
                'error' => $e->getMessage(),
                'data' => $request->validated(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create account type',
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $accountType = $this->accountTypeService->getAccountTypeDetails($id);

            return response()->json([
                'data' => $accountType,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Account type not found',
            ], 404);
        }
    }

    public function update(UpdateAccountTypeRequest $request, int $id): JsonResponse
    {
        try {
            $accountType = $this->accountTypeService->updateAccountType($id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Account type updated successfully',
                'data' => $accountType->load('status'),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update account type', [
                'account_type_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update account type',
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->accountTypeService->deleteAccountType($id);

            return response()->json([
                'success' => true,
                'message' => 'Account type deleted successfully',
            ]);

        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Failed to delete account type', [
                'account_type_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete account type',
            ], 500);
        }
    }
}
