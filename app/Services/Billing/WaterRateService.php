<?php

namespace App\Services\Billing;

use App\Models\Status;
use App\Models\WaterRate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WaterRateService
{
    /**
     * Get all rate tiers for a specific class and period.
     * Falls back to default rates if no period-specific rates exist.
     */
    public function getRateTiersForClass(int $classId, ?int $periodId = null): Collection
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        if ($periodId) {
            // Check for period-specific rates first
            $periodRates = WaterRate::where('period_id', $periodId)
                ->where('class_id', $classId)
                ->where('stat_id', $activeStatusId)
                ->orderBy('range_id')
                ->get();

            if ($periodRates->isNotEmpty()) {
                return $periodRates;
            }
        }

        // Fall back to default rates
        return WaterRate::whereNull('period_id')
            ->where('class_id', $classId)
            ->where('stat_id', $activeStatusId)
            ->orderBy('range_id')
            ->get();
    }

    /**
     * Calculate the total water charge for a given consumption.
     *
     * @param  int  $consumption  Total consumption in cu.m
     * @param  int  $classId  Account type / class ID
     * @param  int|null  $periodId  Billing period ID (null for default rates)
     * @return array{total: float, breakdown: array}
     */
    public function calculateCharge(int $consumption, int $classId, ?int $periodId = null): array
    {
        $tiers = $this->getRateTiersForClass($classId, $periodId);
        $total = 0;
        $breakdown = [];

        foreach ($tiers as $tier) {
            if ($consumption < $tier->range_min) {
                continue;
            }

            // Calculate consumption in this tier
            $tierMin = $tier->range_min;
            $tierMax = min($consumption, $tier->range_max);
            $tierConsumption = $tierMax - $tierMin + 1;

            if ($tierConsumption <= 0) {
                continue;
            }

            // For minimum charge tier (rate_inc = 0), just use the flat rate
            if ($tier->rate_inc == 0) {
                $tierCharge = (float) $tier->rate_val;
            } else {
                // Base rate + (excess consumption * rate increment)
                $excessConsumption = max(0, $tierConsumption - 1);
                $tierCharge = (float) $tier->rate_val + ($excessConsumption * (float) $tier->rate_inc);
            }

            $breakdown[] = [
                'tier' => $tier->range_id,
                'range' => "{$tier->range_min}-{$tier->range_max}",
                'consumption' => $tierConsumption,
                'rate_val' => $tier->rate_val,
                'rate_inc' => $tier->rate_inc,
                'charge' => $tierCharge,
            ];

            $total += $tierCharge;
        }

        return [
            'total' => round($total, 2),
            'breakdown' => $breakdown,
        ];
    }

    /**
     * Get all rates for a specific period (grouped by class).
     * Returns period-specific rates if they exist, otherwise default rates.
     */
    public function getRatesForPeriod(?int $periodId = null): Collection
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        if ($periodId) {
            $periodRates = WaterRate::where('period_id', $periodId)
                ->where('stat_id', $activeStatusId)
                ->orderBy('class_id')
                ->orderBy('range_id')
                ->get();

            if ($periodRates->isNotEmpty()) {
                return $periodRates;
            }
        }

        // Return default rates
        return WaterRate::whereNull('period_id')
            ->where('stat_id', $activeStatusId)
            ->orderBy('class_id')
            ->orderBy('range_id')
            ->get();
    }

    /**
     * Check if a period has custom rates.
     */
    public function periodHasCustomRates(int $periodId): bool
    {
        return WaterRate::where('period_id', $periodId)
            ->where('stat_id', Status::getIdByDescription(Status::ACTIVE))
            ->exists();
    }

    /**
     * Get default rates (no period assigned).
     */
    public function getDefaultRates(): Collection
    {
        return WaterRate::whereNull('period_id')
            ->where('stat_id', Status::getIdByDescription(Status::ACTIVE))
            ->orderBy('class_id')
            ->orderBy('range_id')
            ->get();
    }

    /**
     * Copy default rates to a specific period.
     *
     * @param  int  $periodId  Target period ID
     * @param  float  $adjustmentPercent  Percentage adjustment (e.g., 5 for 5% increase)
     * @return int Number of rates created
     */
    public function copyRatesToPeriod(int $periodId, float $adjustmentPercent = 0): int
    {
        $defaultRates = $this->getDefaultRates();
        $multiplier = 1 + ($adjustmentPercent / 100);
        $count = 0;

        DB::beginTransaction();
        try {
            foreach ($defaultRates as $rate) {
                // Check if rate already exists for this period
                $exists = WaterRate::where('period_id', $periodId)
                    ->where('class_id', $rate->class_id)
                    ->where('range_id', $rate->range_id)
                    ->exists();

                if (! $exists) {
                    WaterRate::create([
                        'period_id' => $periodId,
                        'class_id' => $rate->class_id,
                        'range_id' => $rate->range_id,
                        'range_min' => $rate->range_min,
                        'range_max' => $rate->range_max,
                        'rate_val' => round($rate->rate_val * $multiplier, 2),
                        'rate_inc' => round($rate->rate_inc * $multiplier, 2),
                        'stat_id' => $rate->stat_id,
                    ]);
                    $count++;
                }
            }

            DB::commit();

            return $count;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get rates grouped by class for display.
     */
    public function getRatesGroupedByClass(?int $periodId = null): array
    {
        $rates = $this->getRatesForPeriod($periodId);

        return $rates->groupBy('class_id')->toArray();
    }

    /**
     * Get rate summary statistics.
     */
    public function getRateSummary(?int $periodId = null): array
    {
        $rates = $this->getRatesForPeriod($periodId);

        return [
            'total_tiers' => $rates->count(),
            'classes' => $rates->pluck('class_id')->unique()->count(),
            'has_custom_rates' => $periodId ? $this->periodHasCustomRates($periodId) : false,
        ];
    }
}
