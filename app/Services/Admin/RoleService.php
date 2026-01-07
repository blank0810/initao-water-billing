<?php

namespace App\Services\Admin;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Collection;

class RoleService
{
    /**
     * Get all roles with permission and user counts
     */
    public function getAllRolesWithCounts(): Collection
    {
        return Role::withCount(['permissions', 'users'])->get()->map(function ($role) {
            return [
                'role_id' => $role->role_id,
                'role_name' => $role->role_name,
                'description' => $role->description,
                'permissions_count' => $role->role_name === Role::SUPER_ADMIN
                    ? 'All (bypass)'
                    : $role->permissions_count,
                'users_count' => $role->users_count,
                'is_system_role' => in_array($role->role_name, Role::getAllRoleNames()),
                'can_delete' => $role->users_count === 0 && ! in_array($role->role_name, [
                    Role::SUPER_ADMIN, Role::ADMIN,
                ]),
            ];
        });
    }

    /**
     * Get role with full details
     */
    public function getRoleWithDetails(Role $role): array
    {
        $role->load(['permissions', 'users']);

        return [
            'role_id' => $role->role_id,
            'role_name' => $role->role_name,
            'description' => $role->description,
            'permissions' => $role->permissions->map(fn ($p) => [
                'permission_id' => $p->permission_id,
                'permission_name' => $p->permission_name,
                'description' => $p->description,
            ]),
            'users' => $role->users->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
            ]),
            'is_super_admin' => $role->role_name === Role::SUPER_ADMIN,
        ];
    }

    /**
     * Create new role with permissions
     */
    public function createRole(array $data): Role
    {
        $role = Role::create([
            'role_name' => $data['role_name'],
            'description' => $data['description'] ?? null,
        ]);

        if (! empty($data['permissions'])) {
            $permissionIds = Permission::whereIn('permission_name', $data['permissions'])
                ->pluck('permission_id')
                ->toArray();
            $role->permissions()->sync($permissionIds);
        }

        return $role->fresh(['permissions']);
    }

    /**
     * Update role and its permissions
     */
    public function updateRole(Role $role, array $data): Role
    {
        // Prevent renaming system roles
        if (in_array($role->role_name, Role::getAllRoleNames()) && isset($data['role_name'])) {
            unset($data['role_name']);
        }

        $role->update([
            'role_name' => $data['role_name'] ?? $role->role_name,
            'description' => $data['description'] ?? $role->description,
        ]);

        if (isset($data['permissions']) && $role->role_name !== Role::SUPER_ADMIN) {
            $permissionIds = Permission::whereIn('permission_name', $data['permissions'])
                ->pluck('permission_id')
                ->toArray();
            $role->permissions()->sync($permissionIds);
        }

        return $role->fresh(['permissions']);
    }

    /**
     * Delete role (with validation)
     */
    public function deleteRole(Role $role): array
    {
        // Cannot delete system roles
        if (in_array($role->role_name, [Role::SUPER_ADMIN, Role::ADMIN])) {
            return [
                'success' => false,
                'message' => 'Cannot delete system roles (super_admin, admin)',
            ];
        }

        // Cannot delete if users are assigned
        if ($role->users()->count() > 0) {
            return [
                'success' => false,
                'message' => 'Cannot delete role with assigned users. Reassign users first.',
            ];
        }

        $role->permissions()->detach();
        $role->delete();

        return ['success' => true];
    }
}
