<?php

namespace App\Services\Admin\Config;

use App\Models\Barangay;
use Illuminate\Pagination\LengthAwarePaginator;

class BarangayService
{
    public function getAllBarangays(array $filters): array
    {
        $query = Barangay::query()->with('status');

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('b_desc', 'like', "%{$search}%")
                  ->orWhere('b_code', 'like', "%{$search}%");
            });
        }

        // Status filter
        if (!empty($filters['status'])) {
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
}
