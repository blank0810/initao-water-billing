<?php

namespace App\Services\Admin;

use App\Models\Permission;
use App\Models\Role;
use InvalidArgumentException;

class RolePermissionService
{
    /**
     * Update all permissions for a role (sync)
     */
    public function updateRolePermissions(Role $role, array $permissionNames): void
    {
        $this->guardSuperAdmin($role);

        $permissionIds = Permission::whereIn('permission_name', $permissionNames)
            ->pluck('permission_id')
            ->toArray();

        $role->permissions()->sync($permissionIds);
    }

    /**
     * Toggle a single permission for a role
     */
    public function togglePermission(Role $role, string $permissionName, bool $enabled): void
    {
        $this->guardSuperAdmin($role);

        $permission = Permission::where('permission_name', $permissionName)->first();

        if ($enabled) {
            $role->permissions()->syncWithoutDetaching([$permission->permission_id]);
        } else {
            $role->permissions()->detach($permission->permission_id);
        }
    }

    /**
     * Bulk add/remove permission across multiple roles
     */
    public function bulkUpdatePermission(string $permissionName, array $roleIds, string $action): void
    {
        $permission = Permission::where('permission_name', $permissionName)->first();
        $roles = Role::whereIn('role_id', $roleIds)
            ->where('role_name', '!=', Role::SUPER_ADMIN)
            ->get();

        foreach ($roles as $role) {
            if ($action === 'add') {
                $role->permissions()->syncWithoutDetaching([$permission->permission_id]);
            } else {
                $role->permissions()->detach($permission->permission_id);
            }
        }
    }

    /**
     * Guard against modifying super admin role
     */
    private function guardSuperAdmin(Role $role): void
    {
        if ($role->role_name === Role::SUPER_ADMIN) {
            throw new InvalidArgumentException('Cannot modify super admin permissions');
        }
    }
}
