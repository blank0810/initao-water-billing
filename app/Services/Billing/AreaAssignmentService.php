<?php

namespace App\Services\Billing;

use App\Models\Area;
use App\Models\AreaAssignment;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;

class AreaAssignmentService
{
    /**
     * Get all area assignments with details.
     */
    public function getAllAssignments(): Collection
    {
        return AreaAssignment::with(['area.status', 'user'])
            ->orderBy('effective_from', 'desc')
            ->get()
            ->map(fn ($assignment) => $this->formatAssignmentData($assignment));
    }

    /**
     * Get active assignments only.
     */
    public function getActiveAssignments(): Collection
    {
        return AreaAssignment::with(['area.status', 'user'])
            ->active()
            ->orderBy('effective_from', 'desc')
            ->get()
            ->map(fn ($assignment) => $this->formatAssignmentData($assignment));
    }

    /**
     * Get assignments by area.
     */
    public function getAssignmentsByArea(int $areaId): Collection
    {
        return AreaAssignment::with(['area.status', 'user'])
            ->forArea($areaId)
            ->orderBy('effective_from', 'desc')
            ->get()
            ->map(fn ($assignment) => $this->formatAssignmentData($assignment));
    }

    /**
     * Get assignments by user.
     */
    public function getAssignmentsByUser(int $userId): Collection
    {
        return AreaAssignment::with(['area.status', 'user'])
            ->forUser($userId)
            ->orderBy('effective_from', 'desc')
            ->get()
            ->map(fn ($assignment) => $this->formatAssignmentData($assignment));
    }

    /**
     * Get available users (meter readers) for assignment.
     */
    public function getAvailableMeterReaders(): Collection
    {
        return User::whereHas('roles', function ($query) {
            $query->where('role_name', Role::METER_READER);
        })
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'label' => $user->name.' ('.$user->email.')',
                ];
            });
    }

    /**
     * Get assignment details by ID.
     */
    public function getAssignmentDetails(int $assignmentId): ?array
    {
        $assignment = AreaAssignment::with(['area.status', 'user'])->find($assignmentId);

        if (! $assignment) {
            return null;
        }

        return $this->formatAssignmentData($assignment);
    }

    /**
     * Create a new area assignment.
     */
    public function createAssignment(array $data): array
    {
        // Validate area exists
        $area = Area::find($data['area_id']);
        if (! $area) {
            return [
                'success' => false,
                'message' => 'Area not found.',
            ];
        }

        // Validate user exists and is a meter reader
        $user = User::find($data['user_id']);
        if (! $user) {
            return [
                'success' => false,
                'message' => 'User not found.',
            ];
        }

        if (! $user->isMeterReader()) {
            return [
                'success' => false,
                'message' => 'User is not a meter reader.',
            ];
        }

        // Check for overlapping assignments for the same area and user
        $overlapping = AreaAssignment::where('area_id', $data['area_id'])
            ->where('user_id', $data['user_id'])
            ->where(function ($query) use ($data) {
                $effectiveFrom = $data['effective_from'];
                $effectiveTo = $data['effective_to'] ?? null;

                $query->where(function ($q) use ($effectiveFrom) {
                    // New assignment starts during existing assignment
                    $q->where('effective_from', '<=', $effectiveFrom)
                        ->where(function ($inner) use ($effectiveFrom) {
                            $inner->whereNull('effective_to')
                                ->orWhere('effective_to', '>=', $effectiveFrom);
                        });
                })->orWhere(function ($q) use ($effectiveTo) {
                    // New assignment ends during existing assignment
                    if ($effectiveTo) {
                        $q->where('effective_from', '<=', $effectiveTo)
                            ->where(function ($inner) use ($effectiveTo) {
                                $inner->whereNull('effective_to')
                                    ->orWhere('effective_to', '>=', $effectiveTo);
                            });
                    }
                })->orWhere(function ($q) use ($effectiveFrom, $effectiveTo) {
                    // Existing assignment is within new assignment period
                    $q->where('effective_from', '>=', $effectiveFrom);
                    if ($effectiveTo) {
                        $q->where('effective_from', '<=', $effectiveTo);
                    }
                });
            })
            ->exists();

        if ($overlapping) {
            return [
                'success' => false,
                'message' => 'This user already has an overlapping assignment for this area.',
            ];
        }

        $assignment = AreaAssignment::create([
            'area_id' => $data['area_id'],
            'user_id' => $data['user_id'],
            'effective_from' => $data['effective_from'],
            'effective_to' => $data['effective_to'] ?? null,
        ]);

        return [
            'success' => true,
            'message' => 'Area assignment created successfully.',
            'data' => $this->getAssignmentDetails($assignment->area_assignment_id),
        ];
    }

    /**
     * Update an existing assignment.
     */
    public function updateAssignment(int $assignmentId, array $data): array
    {
        $assignment = AreaAssignment::find($assignmentId);

        if (! $assignment) {
            return [
                'success' => false,
                'message' => 'Assignment not found.',
            ];
        }

        // Validate user if provided
        if (isset($data['user_id'])) {
            $user = User::find($data['user_id']);
            if (! $user) {
                return [
                    'success' => false,
                    'message' => 'User not found.',
                ];
            }

            if (! $user->isMeterReader()) {
                return [
                    'success' => false,
                    'message' => 'User is not a meter reader.',
                ];
            }
        }

        $updateData = [];
        if (isset($data['user_id'])) {
            $updateData['user_id'] = $data['user_id'];
        }
        if (isset($data['effective_from'])) {
            $updateData['effective_from'] = $data['effective_from'];
        }
        if (array_key_exists('effective_to', $data)) {
            $updateData['effective_to'] = $data['effective_to'];
        }

        $assignment->update($updateData);

        return [
            'success' => true,
            'message' => 'Assignment updated successfully.',
            'data' => $this->getAssignmentDetails($assignment->area_assignment_id),
        ];
    }

    /**
     * End an assignment (set effective_to date).
     */
    public function endAssignment(int $assignmentId, string $endDate): array
    {
        $assignment = AreaAssignment::find($assignmentId);

        if (! $assignment) {
            return [
                'success' => false,
                'message' => 'Assignment not found.',
            ];
        }

        if ($assignment->effective_to !== null) {
            return [
                'success' => false,
                'message' => 'This assignment has already ended.',
            ];
        }

        if ($endDate < $assignment->effective_from->format('Y-m-d')) {
            return [
                'success' => false,
                'message' => 'End date cannot be before the start date.',
            ];
        }

        $assignment->update(['effective_to' => $endDate]);

        return [
            'success' => true,
            'message' => 'Assignment ended successfully.',
            'data' => $this->getAssignmentDetails($assignment->area_assignment_id),
        ];
    }

    /**
     * Delete an assignment.
     */
    public function deleteAssignment(int $assignmentId): array
    {
        $assignment = AreaAssignment::find($assignmentId);

        if (! $assignment) {
            return [
                'success' => false,
                'message' => 'Assignment not found.',
            ];
        }

        $assignment->delete();

        return [
            'success' => true,
            'message' => 'Assignment deleted successfully.',
        ];
    }

    /**
     * Get assignment statistics.
     */
    public function getStats(): array
    {
        $totalAssignments = AreaAssignment::count();
        $activeAssignments = AreaAssignment::active()->count();

        $meterReadersWithAssignments = User::whereHas('roles', function ($query) {
            $query->where('role_name', Role::METER_READER);
        })
            ->whereHas('areaAssignments', function ($query) {
                $query->active();
            })
            ->count();

        $areasWithAssignments = Area::whereHas('areaAssignments', function ($query) {
            $query->active();
        })->count();

        return [
            'total_assignments' => $totalAssignments,
            'active_assignments' => $activeAssignments,
            'meter_readers_assigned' => $meterReadersWithAssignments,
            'areas_with_assignments' => $areasWithAssignments,
        ];
    }

    /**
     * Format assignment data for API response.
     */
    private function formatAssignmentData(AreaAssignment $assignment): array
    {
        $isActive = $assignment->isActive();

        return [
            'area_assignment_id' => $assignment->area_assignment_id,
            'area_id' => $assignment->area_id,
            'user_id' => $assignment->user_id,
            'area_name' => $assignment->area?->a_desc ?? 'Unknown',
            'user_name' => $assignment->user?->name ?? 'Unknown',
            'user_email' => $assignment->user?->email ?? '',
            'effective_from' => $assignment->effective_from?->format('Y-m-d'),
            'effective_to' => $assignment->effective_to?->format('Y-m-d'),
            'is_active' => $isActive,
            'status' => $isActive ? 'Active' : 'Ended',
            'status_class' => $isActive
                ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200'
                : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        ];
    }
}
