<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Role;
use App\Models\Status;
use App\Services\AreaAssignmentService;
use App\Services\Users\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected AreaAssignmentService $areaAssignmentService
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

        $statuses = [
            ['stat_id' => Status::getIdByDescription(Status::ACTIVE), 'stat_desc' => 'Active'],
            ['stat_id' => Status::getIdByDescription(Status::INACTIVE), 'stat_desc' => 'Inactive'],
        ];

        return view('user.add', compact('statuses'));
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
        DB::beginTransaction();

        try {
            $user = $this->userService->createUser($request->validated());

            // If user has meter_reader role, assign areas
            if ($this->isMeterReaderRole($request->input('role_id'))) {
                if ($request->has('meter_reader_areas')) {
                    $this->areaAssignmentService->assignAreasToUser(
                        $user->id,
                        $request->input('meter_reader_areas')
                    );
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $this->userService->formatUserForResponse($user),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get user details
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userService->getUserById($id);

        if (! $user) {
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
        $user = $this->userService->getUserById($id);

        if (! $user) {
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
        $user = $this->userService->getUserById($id);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $result = $this->userService->deleteUser($user);

        if (! $result['success']) {
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

    /**
     * Check if the given role ID is a meter reader role
     */
    private function isMeterReaderRole(int $roleId): bool
    {
        $role = Role::find($roleId);

        return $role && $role->role_name === Role::METER_READER;
    }
}
