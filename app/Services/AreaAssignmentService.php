<?php

namespace App\Services;

use App\Models\AreaAssignment;

class AreaAssignmentService
{
    /**
     * Assign areas to a user (typically a meter reader).
     */
    public function assignAreasToUser(int $userId, array $areaIds): void
    {
        foreach ($areaIds as $areaId) {
            AreaAssignment::create([
                'user_id' => $userId,
                'area_id' => $areaId,
                'effective_from' => now()->toDateString(),
                'effective_to' => null,
            ]);
        }
    }

    /**
     * End area assignments for a user.
     */
    public function endUserAreaAssignments(int $userId, ?array $areaIds = null): void
    {
        $query = AreaAssignment::where('user_id', $userId)
            ->whereNull('effective_to');

        if ($areaIds !== null) {
            $query->whereIn('area_id', $areaIds);
        }

        $query->update(['effective_to' => now()->toDateString()]);
    }

    /**
     * Get active area assignments for a user.
     */
    public function getActiveUserAreas(int $userId): array
    {
        return AreaAssignment::where('user_id', $userId)
            ->whereNull('effective_to')
            ->with('area')
            ->get()
            ->toArray();
    }
}
