<?php

namespace App\Services\Billing;

use App\Models\Area;
use App\Models\ServiceConnection;
use App\Models\Status;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class AreaService
{
    /**
     * Cache for area_id column existence check.
     */
    private ?bool $hasAreaIdColumn = null;

    /**
     * Check if the area_id column exists on ServiceConnection table.
     * Result is cached for the lifetime of this service instance.
     */
    private function hasAreaIdColumn(): bool
    {
        if ($this->hasAreaIdColumn === null) {
            $this->hasAreaIdColumn = Schema::hasColumn('ServiceConnection', 'area_id');
        }

        return $this->hasAreaIdColumn;
    }

    /**
     * Get all areas with status.
     */
    public function getAllAreas(): Collection
    {
        return Area::with('status')
            ->orderBy('a_desc')
            ->get()
            ->map(fn ($area) => $this->formatAreaData($area));
    }

    /**
     * Get active areas only.
     */
    public function getActiveAreas(): Collection
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        return Area::with('status')
            ->where('stat_id', $activeStatusId)
            ->orderBy('a_desc')
            ->get()
            ->map(fn ($area) => $this->formatAreaData($area));
    }

    /**
     * Get a single area by ID.
     */
    public function getAreaById(int $areaId): ?array
    {
        $area = Area::with(['status', 'areaAssignments.user'])->find($areaId);

        if (! $area) {
            return null;
        }

        $data = $this->formatAreaData($area);
        $data['assignments'] = $area->areaAssignments->map(function ($assignment) {
            return [
                'area_assignment_id' => $assignment->area_assignment_id,
                'user_id' => $assignment->user_id,
                'user_name' => $assignment->user?->name ?? 'Unknown',
                'effective_from' => $assignment->effective_from?->format('Y-m-d'),
                'effective_to' => $assignment->effective_to?->format('Y-m-d'),
                'is_active' => $assignment->isActive(),
            ];
        })->toArray();

        return $data;
    }

    /**
     * Create a new area.
     */
    public function createArea(array $data): array
    {
        // Check for duplicate area name
        $exists = Area::where('a_desc', $data['a_desc'])->exists();
        if ($exists) {
            return [
                'success' => false,
                'message' => 'An area with this name already exists.',
            ];
        }

        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $area = Area::create([
            'a_desc' => $data['a_desc'],
            'stat_id' => $data['stat_id'] ?? $activeStatusId,
        ]);

        return [
            'success' => true,
            'message' => 'Area created successfully.',
            'data' => $this->formatAreaData($area->fresh('status')),
        ];
    }

    /**
     * Update an existing area.
     */
    public function updateArea(int $areaId, array $data): array
    {
        $area = Area::find($areaId);

        if (! $area) {
            return [
                'success' => false,
                'message' => 'Area not found.',
            ];
        }

        // Check for duplicate area name (excluding current)
        if (isset($data['a_desc'])) {
            $exists = Area::where('a_desc', $data['a_desc'])
                ->where('a_id', '!=', $areaId)
                ->exists();

            if ($exists) {
                return [
                    'success' => false,
                    'message' => 'An area with this name already exists.',
                ];
            }
        }

        $updateData = [];
        if (isset($data['a_desc'])) {
            $updateData['a_desc'] = $data['a_desc'];
        }
        if (isset($data['stat_id'])) {
            $updateData['stat_id'] = $data['stat_id'];
        }

        $area->update($updateData);

        return [
            'success' => true,
            'message' => 'Area updated successfully.',
            'data' => $this->formatAreaData($area->fresh('status')),
        ];
    }

    /**
     * Delete an area.
     */
    public function deleteArea(int $areaId): array
    {
        $area = Area::find($areaId);

        if (! $area) {
            return [
                'success' => false,
                'message' => 'Area not found.',
            ];
        }

        // Check if area has any assignments
        if ($area->areaAssignments()->exists()) {
            return [
                'success' => false,
                'message' => 'Cannot delete area with existing assignments. Remove assignments first.',
            ];
        }

        // Check if area has consumers
        if ($area->consumers()->exists()) {
            return [
                'success' => false,
                'message' => 'Cannot delete area with existing consumers.',
            ];
        }

        $area->delete();

        return [
            'success' => true,
            'message' => 'Area deleted successfully.',
        ];
    }

    /**
     * Get area statistics.
     */
    public function getStats(): array
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        return [
            'total_areas' => Area::count(),
            'active_areas' => Area::where('stat_id', $activeStatusId)->count(),
            'areas_with_assignments' => Area::whereHas('areaAssignments', function ($query) {
                $query->active();
            })->count(),
        ];
    }

    /**
     * Format area data for API response.
     */
    private function formatAreaData(Area $area): array
    {
        $isActive = $area->status?->stat_desc === Status::ACTIVE;

        return [
            'a_id' => $area->a_id,
            'a_desc' => $area->a_desc,
            'stat_id' => $area->stat_id,
            'status_name' => $area->status?->stat_desc ?? 'Unknown',
            'is_active' => $isActive,
            'status_class' => $isActive
                ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200'
                : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        ];
    }

    // ========================================================================
    // Service Connection Area Assignment Methods
    // ========================================================================

    /**
     * Get service connections without area assignment.
     */
    public function getConnectionsWithoutArea(string $search = '', ?int $barangayId = null, int $limit = 100): Collection
    {
        if (! $this->hasAreaIdColumn()) {
            return collect();
        }

        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $query = ServiceConnection::with(['customer', 'accountType', 'address.barangay'])
            ->where('stat_id', $activeStatusId)
            ->whereNull('ended_at')
            ->whereNull('area_id');

        if ($barangayId !== null) {
            $query->whereHas('address', function ($q) use ($barangayId) {
                $q->where('b_id', $barangayId);
            });
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('account_no', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('cust_first_name', 'like', "%{$search}%")
                            ->orWhere('cust_last_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('address.barangay', function ($addressQuery) use ($search) {
                        $addressQuery->where('b_desc', 'like', "%{$search}%");
                    });
            });
        }

        return $query->orderBy('account_no')
            ->limit($limit)
            ->get()
            ->map(fn ($conn) => $this->formatConnectionData($conn));
    }

    /**
     * Get service connections by area.
     */
    public function getConnectionsByArea(?int $areaId = null, string $search = '', ?int $barangayId = null, int $limit = 100): Collection
    {
        if (! $this->hasAreaIdColumn()) {
            return collect();
        }

        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $query = ServiceConnection::with(['customer', 'accountType', 'address.barangay', 'area'])
            ->where('stat_id', $activeStatusId)
            ->whereNull('ended_at');

        if ($areaId === -1) {
            // Special case for "All Connections" (both with and without area)
            // No area_id filter needed
        } elseif ($areaId !== null) {
            $query->where('area_id', $areaId);
        } else {
            $query->whereNotNull('area_id');
        }

        if ($barangayId !== null) {
            $query->whereHas('address', function ($q) use ($barangayId) {
                $q->where('b_id', $barangayId);
            });
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('account_no', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('cust_first_name', 'like', "%{$search}%")
                            ->orWhere('cust_last_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('address.barangay', function ($addressQuery) use ($search) {
                        $addressQuery->where('b_desc', 'like', "%{$search}%");
                    });
            });
        }

        return $query->orderBy('account_no')
            ->limit($limit)
            ->get()
            ->map(fn ($conn) => $this->formatConnectionData($conn));
    }

    /**
     * Assign area to one or more service connections.
     */
    public function assignAreaToConnections(int $areaId, array $connectionIds): array
    {
        if (! $this->hasAreaIdColumn()) {
            return [
                'success' => false,
                'message' => 'Area assignment feature is not available. Please run database migrations.',
            ];
        }

        $area = Area::find($areaId);
        if (! $area) {
            return [
                'success' => false,
                'message' => 'Area not found.',
            ];
        }

        if (empty($connectionIds)) {
            return [
                'success' => false,
                'message' => 'No connections selected.',
            ];
        }

        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $updatedCount = ServiceConnection::whereIn('connection_id', $connectionIds)
            ->where('stat_id', $activeStatusId)
            ->whereNull('ended_at')
            ->update(['area_id' => $areaId]);

        return [
            'success' => true,
            'message' => "Successfully assigned {$updatedCount} connection(s) to {$area->a_desc}.",
            'updated_count' => $updatedCount,
        ];
    }

    /**
     * Remove area assignment from service connections.
     */
    public function removeAreaFromConnections(array $connectionIds): array
    {
        if (! $this->hasAreaIdColumn()) {
            return [
                'success' => false,
                'message' => 'Area assignment feature is not available. Please run database migrations.',
            ];
        }

        if (empty($connectionIds)) {
            return [
                'success' => false,
                'message' => 'No connections selected.',
            ];
        }

        $updatedCount = ServiceConnection::whereIn('connection_id', $connectionIds)
            ->update(['area_id' => null]);

        return [
            'success' => true,
            'message' => "Successfully removed area from {$updatedCount} connection(s).",
            'updated_count' => $updatedCount,
        ];
    }

    /**
     * Get connection area assignment statistics.
     */
    public function getConnectionAreaStats(): array
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $totalActive = ServiceConnection::where('stat_id', $activeStatusId)
            ->whereNull('ended_at')
            ->count();

        if (! $this->hasAreaIdColumn()) {
            return [
                'total_active_connections' => $totalActive,
                'connections_with_area' => 0,
                'connections_without_area' => $totalActive,
                'per_area' => collect(),
                'migration_pending' => true,
            ];
        }

        $withArea = ServiceConnection::where('stat_id', $activeStatusId)
            ->whereNull('ended_at')
            ->whereNotNull('area_id')
            ->count();

        $withoutArea = $totalActive - $withArea;

        // Get count per area
        $perArea = Area::withCount(['serviceConnections' => function ($query) use ($activeStatusId) {
            $query->where('stat_id', $activeStatusId)->whereNull('ended_at');
        }])
            ->orderBy('a_desc')
            ->get()
            ->map(fn ($area) => [
                'a_id' => $area->a_id,
                'a_desc' => $area->a_desc,
                'connection_count' => $area->service_connections_count,
            ]);

        return [
            'total_active_connections' => $totalActive,
            'connections_with_area' => $withArea,
            'connections_without_area' => $withoutArea,
            'per_area' => $perArea,
        ];
    }

    /**
     * Format service connection data for API response.
     */
    private function formatConnectionData(ServiceConnection $connection): array
    {
        $customer = $connection->customer;
        $customerName = $customer
            ? trim($customer->cust_first_name.' '.$customer->cust_last_name)
            : 'Unknown';

        return [
            'connection_id' => $connection->connection_id,
            'account_no' => $connection->account_no,
            'customer_name' => $customerName,
            'account_type' => $connection->accountType?->at_desc ?? 'Unknown',
            'barangay' => $connection->address?->barangay?->b_desc ?? 'Unknown',
            'area_id' => $connection->area_id,
            'area_name' => $connection->area?->a_desc ?? null,
        ];
    }
}
