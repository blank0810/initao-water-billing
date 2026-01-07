<?php

namespace App\Services\Admin;

use App\Models\Permission;
use Illuminate\Support\Collection;

class PermissionService
{
    /**
     * Get all permissions with role counts
     */
    public function getAllPermissionsWithRoleCounts(): Collection
    {
        return Permission::withCount('roles')->get()->map(function ($permission) {
            $module = explode('.', $permission->permission_name)[0] ?? 'other';

            return [
                'permission_id' => $permission->permission_id,
                'permission_name' => $permission->permission_name,
                'description' => $permission->description,
                'module' => ucfirst($module),
                'roles_count' => $permission->roles_count,
            ];
        });
    }

    /**
     * Get permissions grouped by module
     */
    public function getPermissionsGroupedByModule(): array
    {
        $permissions = Permission::withCount('roles')->get();
        $grouped = [];

        foreach ($permissions as $permission) {
            $module = explode('.', $permission->permission_name)[0] ?? 'other';
            $moduleKey = ucfirst($module);

            if (! isset($grouped[$moduleKey])) {
                $grouped[$moduleKey] = [];
            }

            $grouped[$moduleKey][] = [
                'permission_id' => $permission->permission_id,
                'permission_name' => $permission->permission_name,
                'description' => $permission->description,
                'roles_count' => $permission->roles_count,
            ];
        }

        return $grouped;
    }

    /**
     * Get permission with assigned roles
     */
    public function getPermissionWithRoles(Permission $permission): array
    {
        $permission->load('roles');

        return [
            'permission_id' => $permission->permission_id,
            'permission_name' => $permission->permission_name,
            'description' => $permission->description,
            'module' => ucfirst(explode('.', $permission->permission_name)[0] ?? 'other'),
            'roles' => $permission->roles->map(fn ($r) => [
                'role_id' => $r->role_id,
                'role_name' => $r->role_name,
                'description' => $r->description,
            ]),
        ];
    }
}
