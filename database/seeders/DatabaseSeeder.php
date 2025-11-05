<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed address hierarchy (must be in this order due to foreign keys)
        $this->call([
            ProvinceSeeder::class,       // 1. Province first (Misamis Oriental)
            TownSeeder::class,            // 2. Town (Initao)
            BarangaySeeder::class,        // 3. Barangays (16 barangays in Initao)
            PurokSeeder::class,           // 4. Puroks (1-A through 12-B per barangay)

            // Seed service-related tables
            AccountTypeSeeder::class,     // Account types (Individual, Corporation, etc.)
            WaterRateSeeder::class,       // Water rates (Residential, Commercial, etc.)
            ChargeItemSeeder::class,      // Charge items (Connection Fee, Deposits, etc.)
        ]);

        // Create default admin user (optional)
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@initao-water.gov.ph',
        ]);

        $this->command->info('✅ All seeders completed successfully!');
        $this->command->newLine();
        $this->command->info('Summary:');
        $this->command->info('- Province: Misamis Oriental');
        $this->command->info('- Town: Initao');
        $this->command->info('- Barangays: 16 barangays');
        $this->command->info('- Puroks: 384 puroks (24 per barangay: 1-A to 12-B)');
        $this->command->info('- Account Types: 6 types');
        $this->command->info('- Water Rates: 13 rate tiers');
        $this->command->info('- Charge Items: 10 charge items');
    }
}
