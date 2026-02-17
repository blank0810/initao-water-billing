<?php

namespace App\Services\Admin\Config;

use App\Models\ChargeItem;
use App\Models\Status;

class ChargeItemService
{
    public function getAllChargeItems(array $filters): array
    {
        $query = ChargeItem::query()->with('status');

        // Search filter
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Charge type filter
        if (! empty($filters['charge_type'])) {
            $query->where('charge_type', $filters['charge_type']);
        }

        // Status filter
        if (! empty($filters['status'])) {
            $query->where('stat_id', $filters['status']);
        }

        $perPage = $filters['per_page'] ?? 15;
        $paginated = $query->orderBy('charge_type')->orderBy('name')->paginate($perPage);

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

    public function createChargeItem(array $data): ChargeItem
    {
        $data['stat_id'] = Status::getIdByDescription(Status::ACTIVE);
        $data['is_taxable'] = $data['is_taxable'] ?? false;

        return ChargeItem::create($data);
    }

    public function updateChargeItem(int $id, array $data): ChargeItem
    {
        $chargeItem = ChargeItem::findOrFail($id);
        $chargeItem->update($data);

        return $chargeItem->fresh();
    }

    public function deleteChargeItem(int $id): void
    {
        $chargeItem = ChargeItem::findOrFail($id);

        // Check for dependencies
        $chargesCount = $chargeItem->customerCharges()->count();
        if ($chargesCount > 0) {
            throw new \DomainException(
                "Cannot delete charge item '{$chargeItem->name}' because it has {$chargesCount} associated customer charges."
            );
        }

        $chargeItem->delete();
    }

    public function getChargeItemDetails(int $id): ChargeItem
    {
        return ChargeItem::with('status')
            ->withCount(['customerCharges as charges_count'])
            ->findOrFail($id);
    }
}
