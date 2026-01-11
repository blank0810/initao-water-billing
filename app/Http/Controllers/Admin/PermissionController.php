<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Services\Admin\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(
        protected PermissionService $permissionService
    ) {}

    /**
     * Display permission list page
     */
    public function index()
    {
        session(['active_menu' => 'user-permissions']);

        return view('pages.admin.permissions.index');
    }

    /**
     * API: Get all permissions with role count
     */
    public function apiIndex(): JsonResponse
    {
        $permissions = $this->permissionService->getAllPermissionsWithRoleCounts();

        return response()->json(['data' => $permissions]);
    }

    /**
     * API: Get permissions grouped by module
     */
    public function getGroupedPermissions(): JsonResponse
    {
        $grouped = $this->permissionService->getPermissionsGroupedByModule();

        return response()->json(['data' => $grouped]);
    }

    /**
     * Get permission details
     */
    public function show(Permission $permission): JsonResponse
    {
        $data = $this->permissionService->getPermissionWithRoles($permission);

        return response()->json(['data' => $data]);
    }

    /**
     * Update permission description
     */
    public function update(Request $request, Permission $permission): JsonResponse
    {
        $validated = $request->validate([
            'description' => 'nullable|string|max:255',
        ]);

        $permission->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully',
            'data' => $permission,
        ]);
    }
}
