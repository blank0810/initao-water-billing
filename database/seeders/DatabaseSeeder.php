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

        // Seed location data first (required by other seeders)
        $this->call([
            ProvinceSeeder::class,        // Province: Misamis Oriental
            TownSeeder::class,            // Town: Initao
            BarangaySeeder::class,        // Barangays: 16 barangays
            PurokSeeder::class,           // Puroks: 24 per barangay (1-A to 12-B)
        ]);

        // Seed service-related tables
        $this->call([
            StatusSeeder::class,          // Statuses (PENDING, ACTIVE, OVERDUE, etc.)
            UserTypeSeeder::class,        // User types (ADMIN, BILLING)
            AccountTypeSeeder::class,     // Account types (Individual, Corporation, etc.)
            WaterRateSeeder::class,       // Water rates (Residential, Commercial, etc.)
            ChargeItemSeeder::class,      // Charge items (Connection Fee, Deposits, etc.)
            BillAdjustmentTypeSeeder::class, // Bill adjustment types (Meter Error, Penalty Waiver, etc.)
            AreaSeeder::class,            // Areas: 16 areas (one per barangay)
            SystemSettingSeeder::class,   // System settings (automation flags)

            // RBAC seeders
            RoleSeeder::class,            // Roles (Super Admin, Admin, Billing Officer, etc.)
            PermissionSeeder::class,      // Permissions (18 permissions across 8 modules)
        ]);

        // Create default test user (skip if already exists)
        if (! User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'username' => 'testuser',
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);
        }

        // Seed tables that depend on users (must come after user creation)
        $this->call([
            RolePermissionSeeder::class,  // Assign permissions to roles
            TestUsersSeeder::class,       // Create test users for each role (includes super admin)
            PenaltyConfigurationSeeder::class, // Penalty rate configuration (10% default)
            PeriodSeeder::class,          // Billing periods (12 months)
            MiscReferenceSeeder::class,   // Misc references (penalties, discounts, surcharges)
            MeterSeeder::class,           // Sample meters (25 meters: Neptune, Sensus, Badger, Itron, Master Meter)
            ServiceConnectionSeeder::class, // Sample service connections (Residential & Commercial)
            ReadingScheduleSeeder::class, // Default reading schedules for areas with connections
        ]);

        $this->command->info('✅ All seeders completed successfully!');
        $this->command->newLine();
        $this->command->info('Summary:');
        $this->command->info('- Province: Misamis Oriental');
        $this->command->info('- Town: Initao');
        $this->command->info('- Barangays: 16 barangays');
        $this->command->info('- Puroks: 384 puroks (24 per barangay: 1-A to 12-B)');
        $this->command->info('- Statuses: 13 statuses (PENDING, ACTIVE, OVERDUE, etc.)');
        $this->command->info('- User Types: 2 types');
        $this->command->info('- Account Types: 6 types');
        $this->command->info('- Water Rates: 13 rate tiers');
        $this->command->info('- Charge Items: 10 charge items');
        $this->command->info('- Bill Adjustment Types: 5 types');
        $this->command->info('- Periods: 12 billing periods');
        $this->command->info('- Misc References: 3 reference types');
        $this->command->info('- Meters: 25 meters (5 each of Neptune, Sensus, Badger, Itron, Master Meter)');
        $this->command->info('- Roles: 6 roles (Super Admin, Admin, Billing Officer, Meter Reader, Cashier, Viewer)');
        $this->command->info('- Permissions: 18 permissions across 8 modules');
        $this->command->info('- Test Users: 7 users (1 per role + testuser) - Password: "password"');
        $this->command->info('- Areas: 16 areas (one per barangay)');
        $this->command->info('- Service Connections: 5 connections (3 Residential, 2 Commercial)');
        $this->command->info('- Reading Schedules: 1 per area with connections (pending status)');
    }
}
