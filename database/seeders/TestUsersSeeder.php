<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Create test users for each role.
     */
    public function run(): void
    {
        $testUsers = [
            [
                'username' => 'admin_user',
                'email' => 'admin@test.com',
                'name' => 'Admin User',
                'role' => Role::ADMIN,
            ],
            [
                'username' => 'billing_officer',
                'email' => 'billing@test.com',
                'name' => 'Billing Officer',
                'role' => Role::BILLING_OFFICER,
            ],
            [
                'username' => 'meter_reader',
                'email' => 'meter@test.com',
                'name' => 'Meter Reader',
                'role' => Role::METER_READER,
            ],
            [
                'username' => 'cashier',
                'email' => 'cashier@test.com',
                'name' => 'Cashier User',
                'role' => Role::CASHIER,
            ],
            [
                'username' => 'viewer',
                'email' => 'viewer@test.com',
                'name' => 'Viewer User',
                'role' => Role::VIEWER,
            ],
        ];

        foreach ($testUsers as $userData) {
            $roleName = $userData['role'];
            unset($userData['role']);

            // Create user with required fields
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'password' => Hash::make('password'),
                    'u_type' => 3, // Default user type
                    'stat_id' => 1, // ACTIVE status
                ])
            );

            // Assign role
            $user->assignRole($roleName);

            $this->command->info("Created user: {$user->email} with role: {$roleName}");
        }

        $this->command->newLine();
        $this->command->info('Test Users Created:');
        $this->command->table(
            ['Email', 'Password', 'Role'],
            [
                ['admin@test.com', 'password', 'Admin'],
                ['billing@test.com', 'password', 'Billing Officer'],
                ['meter@test.com', 'password', 'Meter Reader'],
                ['cashier@test.com', 'password', 'Cashier'],
                ['viewer@test.com', 'password', 'Viewer'],
            ]
        );
    }
}
