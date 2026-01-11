<?php

namespace App\Console\Commands;

use App\Models\Period;
use App\Models\Status;
use App\Models\WaterRate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetupPeriodRates extends Command
{
    protected $signature = 'periods:setup-rates';

    protected $description = 'Setup period rates: keep only current month period and assign default rates to it';

    public function handle(): int
    {
        $this->info('Setting up period rates...');

        DB::beginTransaction();

        try {
            // Step 1: Get the current month period (January 2026)
            $currentPeriod = Period::orderBy('start_date', 'asc')->first();

            if (! $currentPeriod) {
                $this->error('No periods found. Please run the PeriodSeeder first.');

                return Command::FAILURE;
            }

            $this->info("Current period: {$currentPeriod->per_name} (ID: {$currentPeriod->per_id})");

            // Step 2: Delete all other periods (they have no bills/readings yet)
            $deletedCount = Period::where('per_id', '!=', $currentPeriod->per_id)->delete();
            $this->info("Deleted {$deletedCount} future periods.");

            // Step 3: Get default rates (period_id = NULL)
            $defaultRates = WaterRate::whereNull('period_id')
                ->where('stat_id', Status::getIdByDescription(Status::ACTIVE))
                ->get();

            if ($defaultRates->isEmpty()) {
                $this->warn('No default rates found. Please run the WaterRateSeeder first.');
                DB::rollBack();

                return Command::FAILURE;
            }

            $this->info("Found {$defaultRates->count()} default rate tiers.");

            // Step 4: Check if current period already has rates
            $existingRates = WaterRate::where('period_id', $currentPeriod->per_id)->count();

            if ($existingRates > 0) {
                $this->info("Period already has {$existingRates} rates. Skipping rate copy.");
            } else {
                // Step 5: Copy default rates to current period
                $copiedCount = 0;
                foreach ($defaultRates as $rate) {
                    WaterRate::create([
                        'period_id' => $currentPeriod->per_id,
                        'class_id' => $rate->class_id,
                        'range_id' => $rate->range_id,
                        'range_min' => $rate->range_min,
                        'range_max' => $rate->range_max,
                        'rate_val' => $rate->rate_val,
                        'rate_inc' => $rate->rate_inc,
                        'stat_id' => $rate->stat_id,
                    ]);
                    $copiedCount++;
                }

                $this->info("Copied {$copiedCount} rate tiers to {$currentPeriod->per_name}.");
            }

            DB::commit();

            $this->newLine();
            $this->info('Setup complete!');
            $this->table(
                ['Period', 'Code', 'Rates'],
                [[
                    $currentPeriod->per_name,
                    $currentPeriod->per_code,
                    WaterRate::where('period_id', $currentPeriod->per_id)->count(),
                ]]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
