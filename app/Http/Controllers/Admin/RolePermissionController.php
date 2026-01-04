<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
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
        // Prevent modifying super_admin
        if ($role->role_name === Role::SUPER_ADMIN) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot modify super admin permissions'
            ], 422);
        }

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,permission_name',
        ]);

        $permissionIds = Permission::whereIn('permission_name', $request->permissions ?? [])
            ->pluck('permission_id')
            ->toArray();

        $role->permissions()->sync($permissionIds);

        return response()->json([
            'success' => true,
            'message' => 'Role permissions updated successfully'
        ]);
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

        // Prevent modifying super_admin
        if ($role->role_name === Role::SUPER_ADMIN) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot modify super admin permissions'
            ], 422);
        }

        $permission = Permission::where('permission_name', $request->permission_name)->first();

        if ($request->enabled) {
            $role->permissions()->syncWithoutDetaching([$permission->permission_id]);
        } else {
            $role->permissions()->detach($permission->permission_id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully'
        ]);
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
            'action' => 'required|in:add,remove'
        ]);

        $permission = Permission::where('permission_name', $request->permission_name)->first();
        $roles = Role::whereIn('role_id', $request->role_ids)
            ->where('role_name', '!=', Role::SUPER_ADMIN)
            ->get();

        foreach ($roles as $role) {
            if ($request->action === 'add') {
                $role->permissions()->syncWithoutDetaching([$permission->permission_id]);
            } else {
                $role->permissions()->detach($permission->permission_id);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Permissions updated successfully'
        ]);
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
