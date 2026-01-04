<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define role-permission mappings
        $rolePermissions = [
            // Super Admin gets all permissions via bypass in hasPermission() - no explicit assignments needed
            Role::SUPER_ADMIN => [],

            Role::ADMIN => [
                Permission::USERS_VIEW, Permission::USERS_MANAGE,
                Permission::ROLES_VIEW, Permission::ROLES_MANAGE,
                Permission::CUSTOMERS_VIEW, Permission::CUSTOMERS_MANAGE,
                Permission::BILLING_VIEW, Permission::BILLING_GENERATE, Permission::BILLING_ADJUST,
                Permission::PAYMENTS_VIEW, Permission::PAYMENTS_PROCESS, Permission::PAYMENTS_VOID,
                Permission::METERS_VIEW, Permission::METERS_READ, Permission::METERS_MANAGE,
                Permission::REPORTS_VIEW, Permission::REPORTS_EXPORT,
                Permission::SETTINGS_MANAGE,
            ],

            Role::BILLING_OFFICER => [
                Permission::CUSTOMERS_VIEW,
                Permission::BILLING_VIEW, Permission::BILLING_GENERATE, Permission::BILLING_ADJUST,
                Permission::PAYMENTS_VIEW, Permission::PAYMENTS_PROCESS,
                Permission::METERS_VIEW, Permission::METERS_READ,
                Permission::REPORTS_VIEW,
            ],

            Role::METER_READER => [
                Permission::CUSTOMERS_VIEW,
                Permission::METERS_VIEW, Permission::METERS_READ,
            ],

            Role::CASHIER => [
                Permission::CUSTOMERS_VIEW,
                Permission::BILLING_VIEW,
                Permission::PAYMENTS_VIEW, Permission::PAYMENTS_PROCESS,
            ],

            Role::VIEWER => [
                Permission::USERS_VIEW,
                Permission::ROLES_VIEW,
                Permission::CUSTOMERS_VIEW,
                Permission::BILLING_VIEW,
                Permission::PAYMENTS_VIEW,
                Permission::METERS_VIEW,
                Permission::REPORTS_VIEW,
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = Role::findByName($roleName);
            if (! $role) {
                $this->command->warn("Role not found: {$roleName}");

                continue;
            }

            if (empty($permissionNames)) {
                $this->command->info("Skipping {$role->role_name} (super admin - bypasses permission checks)");

                continue;
            }

            $permissionIds = Permission::whereIn('permission_name', $permissionNames)
                ->pluck('permission_id')
                ->toArray();

            $role->permissions()->syncWithoutDetaching($permissionIds);
            $this->command->info('Synced '.count($permissionIds)." permissions to {$role->role_name} (existing preserved)");
        }
    }
}
