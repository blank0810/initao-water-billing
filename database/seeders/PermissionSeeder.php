<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Users Module
            ['permission_name' => Permission::USERS_VIEW, 'description' => 'View user list and details'],
            ['permission_name' => Permission::USERS_MANAGE, 'description' => 'Create, edit, and delete users'],

            // Roles Module
            ['permission_name' => Permission::ROLES_VIEW, 'description' => 'View roles and permissions'],
            ['permission_name' => Permission::ROLES_MANAGE, 'description' => 'Create, edit, and delete roles'],

            // Customers Module
            ['permission_name' => Permission::CUSTOMERS_VIEW, 'description' => 'View customer list and details'],
            ['permission_name' => Permission::CUSTOMERS_MANAGE, 'description' => 'Create, edit, and delete customers'],

            // Billing Module
            ['permission_name' => Permission::BILLING_VIEW, 'description' => 'View billing records and history'],
            ['permission_name' => Permission::BILLING_GENERATE, 'description' => 'Generate water bills for periods'],
            ['permission_name' => Permission::BILLING_ADJUST, 'description' => 'Create bill adjustments and credits'],

            // Payments Module
            ['permission_name' => Permission::PAYMENTS_VIEW, 'description' => 'View payment records and history'],
            ['permission_name' => Permission::PAYMENTS_PROCESS, 'description' => 'Accept and process customer payments'],
            ['permission_name' => Permission::PAYMENTS_VOID, 'description' => 'Void or cancel payments'],

            // Meters Module
            ['permission_name' => Permission::METERS_VIEW, 'description' => 'View meter inventory and assignments'],
            ['permission_name' => Permission::METERS_READ, 'description' => 'Enter meter readings'],
            ['permission_name' => Permission::METERS_MANAGE, 'description' => 'Add, edit, and assign meters'],

            // Reports Module
            ['permission_name' => Permission::REPORTS_VIEW, 'description' => 'Access and view reports'],
            ['permission_name' => Permission::REPORTS_EXPORT, 'description' => 'Export reports to Excel/PDF'],

            // Settings Module
            ['permission_name' => Permission::SETTINGS_MANAGE, 'description' => 'Manage system settings and configuration'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['permission_name' => $permission['permission_name']],
                $permission
            );
        }

        $this->command->info('Permissions seeded: '.count($permissions).' permissions');
    }
}
