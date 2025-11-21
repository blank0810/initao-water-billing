<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Status;

class WaterRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $waterRates = [
            // Residential Rates
            [
                'rate_desc' => 'Residential - Minimum (0-10 cu.m)',
                'rate' => 150.00,
            ],
            [
                'rate_desc' => 'Residential - 11-20 cu.m',
                'rate' => 18.00,
            ],
            [
                'rate_desc' => 'Residential - 21-30 cu.m',
                'rate' => 20.00,
            ],
            [
                'rate_desc' => 'Residential - 31+ cu.m',
                'rate' => 25.00,
            ],

            // Commercial Rates
            [
                'rate_desc' => 'Commercial - Minimum (0-10 cu.m)',
                'rate' => 250.00,
            ],
            [
                'rate_desc' => 'Commercial - 11-20 cu.m',
                'rate' => 28.00,
            ],
            [
                'rate_desc' => 'Commercial - 21-30 cu.m',
                'rate' => 30.00,
            ],
            [
                'rate_desc' => 'Commercial - 31+ cu.m',
                'rate' => 35.00,
            ],

            // Industrial Rates
            [
                'rate_desc' => 'Industrial - Minimum (0-10 cu.m)',
                'rate' => 350.00,
            ],
            [
                'rate_desc' => 'Industrial - 11-20 cu.m',
                'rate' => 35.00,
            ],
            [
                'rate_desc' => 'Industrial - 21+ cu.m',
                'rate' => 40.00,
            ],

            // Government Rates
            [
                'rate_desc' => 'Government - Minimum (0-10 cu.m)',
                'rate' => 200.00,
            ],
            [
                'rate_desc' => 'Government - 11+ cu.m',
                'rate' => 22.00,
            ],
        ];

        foreach ($waterRates as $rate) {
            // Use updateOrInsert to avoid duplicate entries
            DB::table('water_rates')->updateOrInsert(
                ['rate_desc' => $rate['rate_desc']], // Check for existing record by description
                [
                    'rate_desc' => $rate['rate_desc'],
                    'rate' => $rate['rate'],
                    'stat_id' => $activeStatusId,
                    'updated_at' => now(),
                ]
            );
        }

        // If this is a new insert, set the created_at timestamp
        DB::table('water_rates')
            ->whereNull('created_at')
            ->update(['created_at' => now()]);

        $this->command->info('Water Rates seeded: ' . count($waterRates) . ' rate tiers');
    }
}
