<?php

namespace App\Services\Meter;

use App\Models\Meter;
use App\Models\MeterAssignment;
use App\Models\Status;
use Illuminate\Support\Collection;

class MeterService
{
    /**
     * Status configuration for UI display.
     */
    private const STATUS_CONFIG = [
        'ACTIVE' => [
            'key' => 'available',
            'label' => 'Available',
            'class' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
        ],
        'INSTALLED' => [
            'key' => 'installed',
            'label' => 'Installed',
            'class' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200',
        ],
        'INACTIVE' => [
            'key' => 'faulty',
            'label' => 'Faulty',
            'class' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200',
        ],
        'REMOVED' => [
            'key' => 'removed',
            'label' => 'Removed',
            'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        ],
    ];

    /**
     * Get all meters with status and assignment info.
     */
    public function getAllMetersWithStatus(): Collection
    {
        return Meter::with(['status', 'meterAssignments' => function ($query) {
            $query->whereNull('removed_at')
                ->with('serviceConnection.customer');
        }])
            ->orderBy('mtr_id', 'desc')
            ->get()
            ->map(function ($meter) {
                return $this->formatMeterData($meter);
            });
    }

    /**
     * Get meter details by ID.
     */
    public function getMeterDetails(int $meterId): ?array
    {
        $meter = Meter::with(['status', 'meterAssignments' => function ($query) {
            $query->with('serviceConnection.customer')
                ->orderBy('installed_at', 'desc');
        }])
            ->find($meterId);

        if (! $meter) {
            return null;
        }

        $data = $this->formatMeterData($meter);

        // Add assignment history
        $data['assignment_history'] = $meter->meterAssignments->map(function ($assignment) {
            return [
                'assignment_id' => $assignment->assignment_id,
                'consumer' => $assignment->serviceConnection?->customer?->full_name ?? 'Unknown',
                'installed_at' => $assignment->installed_at?->format('Y-m-d'),
                'removed_at' => $assignment->removed_at?->format('Y-m-d'),
                'install_read' => $assignment->install_read,
                'removal_read' => $assignment->removal_read,
                'is_active' => is_null($assignment->removed_at),
            ];
        })->toArray();

        return $data;
    }

    /**
     * Create a new meter.
     */
    public function createMeter(array $data): array
    {
        // Validate serial uniqueness
        if (Meter::where('mtr_serial', $data['mtr_serial'])->exists()) {
            return [
                'success' => false,
                'message' => 'Meter serial number already exists.',
            ];
        }

        $meter = Meter::create([
            'mtr_serial' => $data['mtr_serial'],
            'mtr_brand' => $data['mtr_brand'],
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        return [
            'success' => true,
            'message' => 'Meter created successfully.',
            'data' => $this->getMeterDetails($meter->mtr_id),
        ];
    }

    /**
     * Update a meter.
     */
    public function updateMeter(int $meterId, array $data): array
    {
        $meter = Meter::find($meterId);

        if (! $meter) {
            return [
                'success' => false,
                'message' => 'Meter not found.',
            ];
        }

        // Check if meter is installed
        $isInstalled = $this->isMeterInstalled($meterId);

        // If serial is being changed and meter is installed, reject
        if (isset($data['mtr_serial']) && $data['mtr_serial'] !== $meter->mtr_serial && $isInstalled) {
            return [
                'success' => false,
                'message' => 'Cannot change serial number of an installed meter.',
            ];
        }

        // Validate serial uniqueness (excluding current meter)
        if (isset($data['mtr_serial']) && $data['mtr_serial'] !== $meter->mtr_serial) {
            if (Meter::where('mtr_serial', $data['mtr_serial'])->where('mtr_id', '!=', $meterId)->exists()) {
                return [
                    'success' => false,
                    'message' => 'Meter serial number already exists.',
                ];
            }
        }

        $meter->update([
            'mtr_serial' => $data['mtr_serial'] ?? $meter->mtr_serial,
            'mtr_brand' => $data['mtr_brand'] ?? $meter->mtr_brand,
        ]);

        return [
            'success' => true,
            'message' => 'Meter updated successfully.',
            'data' => $this->getMeterDetails($meter->mtr_id),
        ];
    }

    /**
     * Delete a meter.
     */
    public function deleteMeter(int $meterId): array
    {
        $meter = Meter::find($meterId);

        if (! $meter) {
            return [
                'success' => false,
                'message' => 'Meter not found.',
            ];
        }

        // Check if meter is currently installed
        if ($this->isMeterInstalled($meterId)) {
            return [
                'success' => false,
                'message' => 'Cannot delete an installed meter. Remove it from the connection first.',
            ];
        }

        // Check if meter has any assignment history
        $hasHistory = MeterAssignment::where('meter_id', $meterId)->exists();
        if ($hasHistory) {
            return [
                'success' => false,
                'message' => 'Cannot delete meter with assignment history. Mark it as removed instead.',
            ];
        }

        $meter->delete();

        return [
            'success' => true,
            'message' => 'Meter deleted successfully.',
        ];
    }

    /**
     * Mark a meter as faulty.
     */
    public function markAsFaulty(int $meterId): array
    {
        $meter = Meter::find($meterId);

        if (! $meter) {
            return [
                'success' => false,
                'message' => 'Meter not found.',
            ];
        }

        $meter->update([
            'stat_id' => Status::getIdByDescription(Status::INACTIVE),
        ]);

        return [
            'success' => true,
            'message' => 'Meter marked as faulty.',
            'data' => $this->getMeterDetails($meter->mtr_id),
        ];
    }

    /**
     * Get inventory statistics.
     */
    public function getStats(): array
    {
        $total = Meter::count();

        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $inactiveStatusId = Status::getIdByDescription(Status::INACTIVE);

        $available = Meter::where('stat_id', $activeStatusId)
            ->whereDoesntHave('meterAssignments', function ($query) {
                $query->whereNull('removed_at');
            })
            ->count();

        $installed = MeterAssignment::whereNull('removed_at')->count();

        $faulty = Meter::where('stat_id', $inactiveStatusId)->count();

        $removed = $total - $available - $installed - $faulty;
        if ($removed < 0) {
            $removed = 0;
        }

        return [
            'total' => $total,
            'available' => $available,
            'installed' => $installed,
            'faulty' => $faulty,
            'removed' => $removed,
        ];
    }

    /**
     * Check if a meter is currently installed.
     */
    private function isMeterInstalled(int $meterId): bool
    {
        return MeterAssignment::where('meter_id', $meterId)
            ->whereNull('removed_at')
            ->exists();
    }

    /**
     * Format meter data for API response.
     */
    private function formatMeterData(Meter $meter): array
    {
        $statusDesc = $meter->status?->stat_desc ?? 'ACTIVE';
        $statusConfig = self::STATUS_CONFIG[$statusDesc] ?? self::STATUS_CONFIG['ACTIVE'];

        // Check if meter is installed
        $activeAssignment = $meter->meterAssignments?->first(function ($assignment) {
            return is_null($assignment->removed_at);
        });

        $isInstalled = ! is_null($activeAssignment);
        $consumer = null;

        if ($isInstalled) {
            $statusConfig = self::STATUS_CONFIG['INSTALLED'];
            $consumer = $activeAssignment->serviceConnection?->customer?->full_name ?? null;
        }

        return [
            'mtr_id' => $meter->mtr_id,
            'mtr_serial' => $meter->mtr_serial,
            'mtr_brand' => $meter->mtr_brand,
            'stat_id' => $meter->stat_id,
            'status' => $statusConfig['key'],
            'status_label' => $statusConfig['label'],
            'status_class' => $statusConfig['class'],
            'consumer' => $consumer,
            'is_installed' => $isInstalled,
            'can_edit' => true,
            'can_delete' => ! $isInstalled && ! MeterAssignment::where('meter_id', $meter->mtr_id)->exists(),
            'created_at' => $meter->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $meter->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
