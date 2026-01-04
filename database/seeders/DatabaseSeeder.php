<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Seed service-related tables
        $this->call([
            UserTypeSeeder::class,        // User types (ADMIN, BILLING)
            AccountTypeSeeder::class,     // Account types (Individual, Corporation, etc.)
            WaterRateSeeder::class,       // Water rates (Residential, Commercial, etc.)
            ChargeItemSeeder::class,      // Charge items (Connection Fee, Deposits, etc.)
            BillAdjustmentTypeSeeder::class, // Bill adjustment types (Meter Error, Penalty Waiver, etc.)

            // RBAC seeders
            RoleSeeder::class,            // Roles (Super Admin, Admin, Billing Officer, etc.)
            PermissionSeeder::class,      // Permissions (18 permissions across 8 modules)
        ]);

        // Create default test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed tables that depend on users (must come after user creation)
        $this->call([
            RolePermissionSeeder::class,  // Assign permissions to roles
            AdminUserRoleSeeder::class,   // Assign Super Admin role to admin user
            TestUsersSeeder::class,       // Create test users for each role
            PeriodSeeder::class,          // Billing periods (12 months)
            MiscReferenceSeeder::class,   // Misc references (penalties, discounts, surcharges)
        ]);

        $this->command->info('✅ All seeders completed successfully!');
        $this->command->newLine();
        $this->command->info('Summary:');
        $this->command->info('- Province: Misamis Oriental');
        $this->command->info('- Town: Initao');
        $this->command->info('- Barangays: 16 barangays');
        $this->command->info('- Puroks: 384 puroks (24 per barangay: 1-A to 12-B)');
        $this->command->info('- User Types: 2 types');
        $this->command->info('- Account Types: 6 types');
        $this->command->info('- Water Rates: 13 rate tiers');
        $this->command->info('- Charge Items: 10 charge items');
        $this->command->info('- Bill Adjustment Types: 5 types');
        $this->command->info('- Periods: 12 billing periods');
        $this->command->info('- Misc References: 3 reference types');
        $this->command->info('- Roles: 6 roles (Super Admin, Admin, Billing Officer, Meter Reader, Cashier, Viewer)');
        $this->command->info('- Permissions: 18 permissions across 8 modules');
        $this->command->info('- Test Users: 6 users (1 per role) - Password: "password"');
    }
}
