<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'role_name' => Role::SUPER_ADMIN,
                'description' => 'Full system access with all permissions',
            ],
            [
                'role_name' => Role::ADMIN,
                'description' => 'Administrative functions including user management',
            ],
            [
                'role_name' => Role::BILLING_OFFICER,
                'description' => 'Billing generation, adjustments, and payment processing',
            ],
            [
                'role_name' => Role::METER_READER,
                'description' => 'Meter reading entry and management',
            ],
            [
                'role_name' => Role::CASHIER,
                'description' => 'Payment processing only',
            ],
            [
                'role_name' => Role::VIEWER,
                'description' => 'Read-only access to system data',
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['role_name' => $role['role_name']],
                $role
            );
        }

        $this->command->info('Roles seeded: ' . count($roles) . ' roles');
    }
}
