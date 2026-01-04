<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\Users\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * Display user list page
     */
    public function index()
    {
        session(['active_menu' => 'user-list']);

        return view('pages.user.user-list');
    }

    /**
     * Display add user page
     */
    public function create()
    {
        session(['active_menu' => 'user-add']);
        return view('pages.user.add-user');
    }

    /**
     * API: Get all users for DataTable
     */
    public function apiIndex(): JsonResponse
    {
        $users = $this->userService->getAllUsersForApi();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * Store a new user
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->createUser($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $this->userService->formatUserForResponse($user),
        ], 201);
    }

    /**
     * Get user details
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->userService->formatUserForResponse($user),
        ]);
    }

    /**
     * Update an existing user
     */
    public function update(UpdateUserRequest $request, int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $updatedUser = $this->userService->updateUser($user, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $this->userService->formatUserForResponse($updatedUser),
        ]);
    }

    /**
     * Delete a user
     */
    public function destroy(int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $result = $this->userService->deleteUser($user);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }

    /**
     * Get user statistics
     */
    public function stats(): JsonResponse
    {
        $stats = $this->userService->getStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
