<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Models\Role;
use App\Services\Admin\RoleService;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function __construct(
        protected RoleService $roleService
    ) {}

    /**
     * Display role list page
     */
    public function index()
    {
        session(['active_menu' => 'user-roles']);

        return view('pages.admin.roles.index');
    }

    /**
     * API: Get all roles with permission count and user count
     */
    public function apiIndex(): JsonResponse
    {
        $roles = $this->roleService->getAllRolesWithCounts();

        return response()->json(['data' => $roles]);
    }

    /**
     * API: Get available roles for user assignment dropdown
     */
    public function getAvailableRoles(): JsonResponse
    {
        $roles = Role::select('role_id', 'role_name', 'description')->get();

        return response()->json(['data' => $roles]);
    }

    /**
     * Store new role
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->createRole($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'data' => $role,
        ], 201);
    }

    /**
     * Get role details with permissions
     */
    public function show(Role $role): JsonResponse
    {
        $roleData = $this->roleService->getRoleWithDetails($role);

        return response()->json(['data' => $roleData]);
    }

    /**
     * Update role
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $updatedRole = $this->roleService->updateRole($role, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
            'data' => $updatedRole,
        ]);
    }

    /**
     * Delete role (with validation)
     */
    public function destroy(Role $role): JsonResponse
    {
        $result = $this->roleService->deleteRole($role);

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully',
        ]);
    }

    /**
     * Get users assigned to a role
     */
    public function getRoleUsers(Role $role): JsonResponse
    {
        $users = $role->users()->select('users.id', 'users.name', 'users.email')->get();

        return response()->json(['data' => $users]);
    }
}
