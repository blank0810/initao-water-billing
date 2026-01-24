<?php

namespace App\Services\Admin\Config;

use App\Models\AccountType;
use App\Models\Status;

class AccountTypeService
{
    public function getAllAccountTypes(array $filters): array
    {
        $query = AccountType::query()->with('status');

        // Search filter
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('at_desc', 'like', "%{$search}%");
        }

        // Status filter
        if (!empty($filters['status'])) {
            $query->where('stat_id', $filters['status']);
        }

        $perPage = $filters['per_page'] ?? 15;
        $paginated = $query->orderBy('at_desc')->paginate($perPage);

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

    public function createAccountType(array $data): AccountType
    {
        $data['stat_id'] = Status::getIdByDescription(Status::ACTIVE);

        return AccountType::create($data);
    }

    public function updateAccountType(int $id, array $data): AccountType
    {
        $accountType = AccountType::findOrFail($id);
        $accountType->update($data);

        return $accountType->fresh();
    }

    public function deleteAccountType(int $id): void
    {
        $accountType = AccountType::findOrFail($id);

        // Check for dependencies
        $connectionsCount = $accountType->serviceConnections()->count();
        if ($connectionsCount > 0) {
            throw new \DomainException(
                "Cannot delete account type '{$accountType->at_desc}' because it has {$connectionsCount} associated service connections."
            );
        }

        $accountType->delete();
    }

    public function getAccountTypeDetails(int $id): AccountType
    {
        return AccountType::with('status')
            ->withCount(['serviceConnections as connections_count'])
            ->findOrFail($id);
    }
}
