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
     * - period_id: NULL for default rates, or specific period ID
     * - class_id: Links to account_type (1=Individual, 2=Corporation, etc.)
     * - range_id: Tier level (1, 2, 3, 4)
     * - range_min/max: Consumption range in cu.m
     * - rate_val: Rate per cu.m. (Bill = consumption x rate_val)
     */
    public function run(): void
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        // Get account type IDs
        $accountTypes = DB::table('account_type')->pluck('at_id', 'at_desc');

        // Get the current period (created by PeriodSeeder)
        $currentPeriod = DB::table('period')
            ->where('is_closed', false)
            ->where('stat_id', $activeStatusId)
            ->orderBy('start_date', 'desc')
            ->first();

        // Define rate tiers for each class (using class_id from account_type)
        // Format: [class_id => [[range_id, range_min, range_max, rate_val], ...]]
        $rateTiers = [
            // Individual (Residential) - class_id 1
            $accountTypes['Residential'] ?? 1 => [
                [1, 0, 999, 20.00],    // 10.00/cu.m for 0-10 cu.m
            ],
            // Corporation (Commercial) - class_id 2
            $accountTypes['Commercial'] ?? 2 => [
                [1, 0, 999, 40.00],    // 20.00/cu.m for 0-10 cu.m
            ],
        ];

        $count = 0;

        // Seed rates for both NULL (default) and the current period
        $periodIds = [null];
        if ($currentPeriod) {
            $periodIds[] = $currentPeriod->per_id;
        }

        foreach ($periodIds as $periodId) {
            foreach ($rateTiers as $classId => $tiers) {
                foreach ($tiers as $tier) {
                    [$rangeId, $rangeMin, $rangeMax, $rateVal] = $tier;

                    DB::table('water_rates')->updateOrInsert(
                        [
                            'period_id' => $periodId,
                            'class_id' => $classId,
                            'range_id' => $rangeId,
                        ],
                        [
                            'period_id' => $periodId,
                            'class_id' => $classId,
                            'range_id' => $rangeId,
                            'range_min' => $rangeMin,
                            'range_max' => $rangeMax,
                            'rate_val' => $rateVal,
                            'stat_id' => $activeStatusId,
                            'updated_at' => now(),
                        ]
                    );

                    $count++;
                }
            }
        }

        // Set created_at for new inserts
        DB::table('water_rates')
            ->whereNull('created_at')
            ->update(['created_at' => now()]);

        $periodInfo = $currentPeriod ? " (including period: {$currentPeriod->per_name})" : ' (default rates only)';
        $this->command->info("Water Rate Tiers seeded: {$count} rate tiers across ".count($rateTiers)." classes{$periodInfo}");
    }
}
