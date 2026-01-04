<?php

namespace App\Services;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\User;

class ActivityLogService
{
    /**
     * Get paginated activity logs with filters
     */
    public function getActivityLogs(Request $request): LengthAwarePaginator
    {
        $query = Activity::query()
            ->with('causer')
            ->orderBy('created_at', 'desc');

        // Filter by log name (authentication, default, etc.)
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id)
                  ->where('causer_type', User::class);
        }

        // Search in description or properties
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('properties', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 25);

        return $query->paginate($perPage);
    }

    /**
     * Get users who have activity for filter dropdown
     */
    public function getUsersWithActivity(): Collection
    {
        $userIds = Activity::where('causer_type', User::class)
            ->distinct()
            ->pluck('causer_id');

        return User::whereIn('id', $userIds)
            ->select('id', 'name', 'username', 'email')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get available log names for filter dropdown
     */
    public function getLogNames(): array
    {
        return Activity::distinct()
            ->pluck('log_name')
            ->toArray();
    }

    /**
     * Format activity for display
     */
    public function formatActivityForDisplay(Activity $activity): array
    {
        $properties = $activity->properties->toArray();

        return [
            'id' => $activity->id,
            'description' => $activity->description,
            'log_name' => $activity->log_name,
            'causer_name' => $activity->causer?->name ?? 'System',
            'causer_email' => $activity->causer?->email ?? '-',
            'ip_address' => $properties['ip_address'] ?? '-',
            'user_agent' => $properties['user_agent'] ?? '-',
            'created_at' => $activity->created_at->format('M d, Y h:i:s A'),
            'created_at_human' => $activity->created_at->diffForHumans(),
            'properties' => $properties,
        ];
    }
}
