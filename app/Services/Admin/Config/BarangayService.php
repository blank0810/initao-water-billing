<?php

namespace App\Services\Admin\Config;

use App\Models\Barangay;
use App\Models\Status;

class BarangayService
{
    public function getAllBarangays(array $filters): array
    {
        $query = Barangay::query()->with('status');

        // Search filter
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('b_desc', 'like', "%{$search}%")
                    ->orWhere('b_code', 'like', "%{$search}%");
            });
        }

        // Status filter
        if (! empty($filters['status'])) {
            $query->where('stat_id', $filters['status']);
        }

        $perPage = $filters['per_page'] ?? 15;
        $paginated = $query->orderBy('b_desc')->paginate($perPage);

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

    public function createBarangay(array $data): Barangay
    {
        $data['stat_id'] = Status::getIdByDescription(Status::ACTIVE);

        return Barangay::create($data);
    }

    public function updateBarangay(int $id, array $data): Barangay
    {
        $barangay = Barangay::findOrFail($id);
        $barangay->update($data);

        return $barangay->fresh();
    }

    public function deleteBarangay(int $id): void
    {
        $barangay = Barangay::findOrFail($id);

        // Check for dependencies
        $puroksCount = $barangay->puroks()->count();
        if ($puroksCount > 0) {
            throw new \DomainException(
                "Cannot delete barangay '{$barangay->b_desc}' because it has {$puroksCount} associated puroks."
            );
        }

        $addressesCount = $barangay->consumerAddresses()->count();
        if ($addressesCount > 0) {
            throw new \DomainException(
                "Cannot delete barangay '{$barangay->b_desc}' because it is used in {$addressesCount} consumer addresses."
            );
        }

        $barangay->delete();
    }

    public function getBarangayDetails(int $id): Barangay
    {
        return Barangay::with('status', 'puroks')
            ->withCount(['puroks', 'consumerAddresses as addresses_count'])
            ->findOrFail($id);
    }
}
