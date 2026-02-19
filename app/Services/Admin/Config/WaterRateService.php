<?php

namespace App\Services\Admin\Config;

use App\Models\Status;
use App\Models\WaterRate;

class WaterRateService
{
    /**
     * Get all water rates grouped by account type.
     */
    public function getAllRates(?int $periodId = null): array
    {
        $query = WaterRate::query()
            ->with(['accountType', 'status'])
            ->where('stat_id', Status::getIdByDescription(Status::ACTIVE));

        if ($periodId === null) {
            $query->whereNull('period_id');
        } else {
            $query->where('period_id', $periodId);
        }

        $rates = $query->orderBy('class_id')
            ->orderBy('range_id')
            ->get();

        // Group by account type description
        $grouped = $rates->groupBy(function ($rate) {
            return $rate->accountType->at_desc;
        });

        return [
            'data' => $grouped->toArray(),
            'meta' => [
                'total' => $rates->count(),
                'account_types' => $grouped->count(),
            ],
        ];
    }

    /**
     * Create or update a water rate tier.
     * If a tier with the same period_id, class_id, and range_id exists, update it.
     * Otherwise, create a new tier.
     */
    public function createOrUpdateRateTier(array $data): WaterRate
    {
        // Check if tier already exists
        $existing = WaterRate::where('period_id', $data['period_id'] ?? null)
            ->where('class_id', $data['class_id'])
            ->where('range_id', $data['range_id'])
            ->first();

        if ($existing) {
            // Update existing tier
            $existing->update([
                'range_min' => $data['range_min'],
                'range_max' => $data['range_max'],
                'rate_val' => $data['rate_val'],
            ]);

            return $existing->fresh();
        }

        // Create new tier
        $data['stat_id'] = Status::getIdByDescription(Status::ACTIVE);

        return WaterRate::create($data);
    }

    /**
     * Validate that a new tier doesn't overlap with existing tiers
     * for the same period and account type.
     */
    public function validateNoRangeOverlap(
        int $classId,
        ?int $periodId,
        array $tierData,
        ?int $excludeRangeId = null
    ): bool {
        $rangeMin = $tierData['range_min'];
        $rangeMax = $tierData['range_max'];

        // Get all existing tiers for this period and class
        $query = WaterRate::where('class_id', $classId)
            ->where(function ($q) use ($periodId) {
                if ($periodId === null) {
                    $q->whereNull('period_id');
                } else {
                    $q->where('period_id', $periodId);
                }
            })
            ->where('stat_id', Status::getIdByDescription(Status::ACTIVE));

        // Exclude current range if updating
        if ($excludeRangeId !== null) {
            $query->where('range_id', '!=', $excludeRangeId);
        }

        $existingTiers = $query->get();

        // Check for overlaps
        foreach ($existingTiers as $tier) {
            // Check if ranges overlap
            // Overlap occurs if: (start1 <= end2) AND (start2 <= end1)
            if ($rangeMin <= $tier->range_max && $tier->range_min <= $rangeMax) {
                throw new \DomainException(
                    "Range {$rangeMin}-{$rangeMax} overlaps with existing tier ".
                    "({$tier->range_min}-{$tier->range_max})"
                );
            }
        }

        return true;
    }

    /**
     * Delete a water rate tier.
     */
    public function deleteRateTier(int $rateId): void
    {
        $rate = WaterRate::findOrFail($rateId);
        $rate->delete();
    }

    /**
     * Get details of a specific water rate tier.
     */
    public function getRateTierDetails(int $rateId): WaterRate
    {
        return WaterRate::with(['accountType', 'status', 'period'])
            ->findOrFail($rateId);
    }

    /**
     * Get all account types.
     */
    public function getAccountTypes(): array
    {
        return \App\Models\AccountType::where('stat_id', Status::getIdByDescription(Status::ACTIVE))
            ->orderBy('at_desc')
            ->get(['at_id', 'at_desc'])
            ->toArray();
    }
}
