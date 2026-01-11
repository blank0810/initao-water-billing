<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WaterRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds tiered water rates with the structure:
     * - period_id: NULL for default rates
     * - class_id: Links to account_type (1=Individual, 2=Corporation, etc.)
     * - range_id: Tier level (1, 2, 3, 4)
     * - range_min/max: Consumption range in cu.m
     * - rate_val: Base rate value
     * - rate_inc: Rate increment per cu.m above minimum
     */
    public function run(): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        // Get account type IDs
        $accountTypes = DB::table('account_type')->pluck('at_id', 'at_desc');

        // Define rate tiers for each class (using class_id from account_type)
        // Format: [class_id => [[range_id, range_min, range_max, rate_val, rate_inc], ...]]
        $rateTiers = [
            // Individual (Residential) - class_id 1
            $accountTypes['Residential'] ?? 1 => [
                [1, 0, 10, 100.00, 0.00],      // Minimum charge for 0-10 cu.m
                [2, 11, 20, 100.00, 11.00],   // Base + 11/cu.m for 11-20
                [3, 21, 30, 210.00, 12.00],   // Base + 12/cu.m for 21-30
                [4, 31, 999, 330.00, 13.00],  // Base + 13/cu.m for 31+
            ],
            // Corporation (Commercial) - class_id 2
            $accountTypes['Commercial'] ?? 2 => [
                [1, 0, 10, 200.00, 0.00],
                [2, 11, 20, 200.00, 22.00],
                [3, 21, 30, 420.00, 24.00],
                [4, 31, 999, 660.00, 26.00],
            ],
        ];

        $count = 0;

        foreach ($rateTiers as $classId => $tiers) {
            foreach ($tiers as $tier) {
                [$rangeId, $rangeMin, $rangeMax, $rateVal, $rateInc] = $tier;

                DB::table('water_rates')->updateOrInsert(
                    [
                        'period_id' => null,
                        'class_id' => $classId,
                        'range_id' => $rangeId,
                    ],
                    [
                        'period_id' => null,
                        'class_id' => $classId,
                        'range_id' => $rangeId,
                        'range_min' => $rangeMin,
                        'range_max' => $rangeMax,
                        'rate_val' => $rateVal,
                        'rate_inc' => $rateInc,
                        'stat_id' => $activeStatusId,
                        'updated_at' => now(),
                    ]
                );

                $count++;
            }
        }

        // Set created_at for new inserts
        DB::table('water_rates')
            ->whereNull('created_at')
            ->update(['created_at' => now()]);

        $this->command->info("Water Rate Tiers seeded: {$count} rate tiers across " . count($rateTiers) . ' classes');
    }
}
