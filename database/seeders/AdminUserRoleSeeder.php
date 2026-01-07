<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the existing admin user
        $adminUser = User::where('email', 'admin@initao-water.gov.ph')->first();

        if (! $adminUser) {
            // Try finding by username
            $adminUser = User::where('username', 'admin')->first();
        }

        if ($adminUser) {
            $adminUser->assignRole(Role::SUPER_ADMIN);
            $identifier = $adminUser->email ?: $adminUser->username;
            $this->command->info("Assigned Super Admin role to: {$identifier}");
        } else {
            $this->command->warn('Admin user not found. Creating default admin user...');

            // Create a default admin user if none exists
            $adminUser = User::create([
                'username' => 'admin',
                'email' => 'admin@initao-water.gov.ph',
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'u_type' => 3, // ADMIN type
                'stat_id' => 1, // ACTIVE status
            ]);

            $adminUser->assignRole(Role::SUPER_ADMIN);
            $this->command->info('Created admin user and assigned Super Admin role');
        }
    }
}
