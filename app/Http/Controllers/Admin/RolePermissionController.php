<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Services\Admin\RolePermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function __construct(
        private RolePermissionService $rolePermissionService
    ) {}

    /**
     * Display role-permission matrix page
     */
    public function matrix()
    {
        session(['active_menu' => 'user-matrix']);

        $roles = Role::with('permissions')->orderBy('role_id')->get();
        $permissionsGrouped = Permission::getPermissionsByModule();
        $allPermissions = Permission::all()->keyBy('permission_name');

        return view('pages.admin.role-permissions.matrix', compact('roles', 'permissionsGrouped', 'allPermissions'));
    }

    /**
     * Update permissions for a single role
     */
    public function updateRolePermissions(Request $request, Role $role): JsonResponse
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,permission_name',
        ]);

        try {
            $this->rolePermissionService->updateRolePermissions($role, $request->permissions ?? []);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'message' => 'Role permissions updated successfully']);
    }

    /**
     * Toggle single permission for a role (AJAX)
     */
    public function togglePermission(Request $request): JsonResponse
    {
        $request->validate([
            'role_id' => 'required|exists:roles,role_id',
            'permission_name' => 'required|string|exists:permissions,permission_name',
            'enabled' => 'required|boolean',
        ]);

        $role = Role::find($request->role_id);

        try {
            $this->rolePermissionService->togglePermission($role, $request->permission_name, $request->enabled);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'message' => 'Permission updated successfully']);
    }

    /**
     * Bulk update: Toggle permission for multiple roles
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'permission_name' => 'required|string|exists:permissions,permission_name',
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,role_id',
            'action' => 'required|in:add,remove',
        ]);

        $this->rolePermissionService->bulkUpdatePermission(
            $request->permission_name,
            $request->role_ids,
            $request->action
        );

        return response()->json(['success' => true, 'message' => 'Permissions updated successfully']);
    }

    /**
     * API: Get matrix data
     */
    public function getMatrixData(): JsonResponse
    {
        $roles = Role::with('permissions')->orderBy('role_id')->get()->map(function ($role) {
            return [
                'role_id' => $role->role_id,
                'role_name' => $role->role_name,
                'description' => $role->description,
                'is_super_admin' => $role->role_name === Role::SUPER_ADMIN,
                'permissions' => $role->permissions->pluck('permission_name')->toArray(),
            ];
        });

        $permissionsGrouped = Permission::getPermissionsByModule();

        return response()->json([
            'roles' => $roles,
            'permissions_grouped' => $permissionsGrouped,
        ]);
    }
}
