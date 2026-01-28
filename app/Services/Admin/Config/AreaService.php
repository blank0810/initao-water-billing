<?php

namespace App\Services\Admin\Config;

use App\Models\Area;
use App\Models\Status;

class AreaService
{
    public function getAllAreas(array $filters): array
    {
        $query = Area::query()->with('status');

        // Search filter
        if (!empty($filters['search'])) {
            $query->where('a_desc', 'like', "%{$filters['search']}%");
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('stat_id', $filters['status']);
        }

        $perPage = $filters['per_page'] ?? 15;
        $paginated = $query->withCount(['activeAreaAssignments', 'serviceConnections', 'consumers'])
            ->orderBy('a_desc')
            ->paginate($perPage);

        return [
            'data' => $paginated->items(),
            'links' => [
                'first' => $paginated->url(1),
                'last' => $paginated->url($paginated->lastPage()),
                'prev' => $paginated->previousPageUrl(),
                'next' => $paginated->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'from' => $paginated->firstItem(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'to' => $paginated->lastItem(),
                'total' => $paginated->total(),
            ],
        ];
    }

    public function createArea(array $data): Area
    {
        $data['stat_id'] = Status::getIdByDescription(Status::ACTIVE);

        return Area::create($data);
    }

    public function updateArea(int $id, array $data): Area
    {
        $area = Area::findOrFail($id);
        $area->update($data);

        return $area->fresh();
    }

    public function deleteArea(int $id): void
    {
        $area = Area::findOrFail($id);

        // Check for active assignments
        $activeAssignments = $area->activeAreaAssignments()->count();
        if ($activeAssignments > 0) {
            throw new \DomainException(
                "Cannot delete area '{$area->a_desc}' because it has {$activeAssignments} active meter reader assignments."
            );
        }

        // Check for service connections
        $connectionCount = $area->serviceConnections()->count();
        if ($connectionCount > 0) {
            throw new \DomainException(
                "Cannot delete area '{$area->a_desc}' because it has {$connectionCount} service connections."
            );
        }

        $area->delete();
    }

    public function getAreaDetails(int $id): Area
    {
        $area = Area::with([
            'status',
            'areaAssignments' => function ($query) {
                $query->where(function ($q) {
                    $q->whereNull('effective_to')
                        ->orWhere('effective_to', '>=', now()->format('Y-m-d'));
                })->with('user:id,name,email');
            }
        ])
        ->withCount(['serviceConnections', 'consumers'])
        ->findOrFail($id);

        // Format active assignments
        $area->active_assignments = $area->areaAssignments->map(function ($assignment) {
            return [
                'assignment_id' => $assignment->area_assignment_id,
                'user' => $assignment->user,
                'effective_from' => $assignment->effective_from,
                'effective_to' => $assignment->effective_to,
            ];
        });

        unset($area->areaAssignments);

        return $area;
    }
}
