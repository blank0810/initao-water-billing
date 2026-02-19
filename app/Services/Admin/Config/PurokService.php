<?php

namespace App\Services\Admin\Config;

use App\Models\Purok;
use App\Models\Status;

class PurokService
{
    public function getAllPuroks(array $filters): array
    {
        $query = Purok::query()->with('status');

        // Search filter
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('p_desc', 'like', "%{$search}%");
        }

        // Status filter
        if (! empty($filters['status'])) {
            $query->where('stat_id', $filters['status']);
        }

        $perPage = $filters['per_page'] ?? 15;
        $paginated = $query->orderBy('p_desc')->paginate($perPage);

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

    public function createPurok(array $data): Purok
    {
        $data['stat_id'] = Status::getIdByDescription(Status::ACTIVE);

        return Purok::create($data);
    }

    public function updatePurok(int $id, array $data): Purok
    {
        $purok = Purok::findOrFail($id);
        $purok->update($data);

        return $purok->fresh();
    }

    public function deletePurok(int $id): void
    {
        $purok = Purok::findOrFail($id);

        // Check for dependencies
        $addressesCount = $purok->consumerAddresses()->count();
        if ($addressesCount > 0) {
            throw new \DomainException(
                "Cannot delete purok '{$purok->p_desc}' because it is used in {$addressesCount} consumer addresses."
            );
        }

        $purok->delete();
    }

    public function getPurokDetails(int $id): Purok
    {
        return Purok::with('status')
            ->withCount(['consumerAddresses as addresses_count'])
            ->findOrFail($id);
    }
}
