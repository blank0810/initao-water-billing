<?php

namespace App\Services\Meter;

use App\Models\Meter;
use App\Models\MeterAssignment;
use App\Models\ServiceConnection;
use App\Models\Status;
use Illuminate\Support\Collection;

class MeterAssignmentService
{
    /**
     * Get all meter assignments with details.
     */
    public function getAllAssignments(): Collection
    {
        return MeterAssignment::with([
            'meter',
            'serviceConnection.customer',
            'serviceConnection.accountType',
            'serviceConnection.address.barangay',
        ])
            ->orderByRaw('CASE WHEN removed_at IS NULL THEN 0 ELSE 1 END')
            ->orderBy('installed_at', 'desc')
            ->get()
            ->map(function ($assignment) {
                return $this->formatAssignmentData($assignment);
            });
    }

    /**
     * Get active assignments only (currently installed meters).
     */
    public function getActiveAssignments(): Collection
    {
        return MeterAssignment::with([
            'meter',
            'serviceConnection.customer',
            'serviceConnection.accountType',
            'serviceConnection.address.barangay',
        ])
            ->whereNull('removed_at')
            ->orderBy('installed_at', 'desc')
            ->get()
            ->map(function ($assignment) {
                return $this->formatAssignmentData($assignment);
            });
    }

    /**
     * Get available meters (active status, not currently assigned).
     */
    public function getAvailableMeters(): Collection
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        return Meter::where('stat_id', $activeStatusId)
            ->whereDoesntHave('meterAssignments', function ($query) {
                $query->whereNull('removed_at');
            })
            ->orderBy('mtr_serial')
            ->get()
            ->map(function ($meter) {
                return [
                    'mtr_id' => $meter->mtr_id,
                    'mtr_serial' => $meter->mtr_serial,
                    'mtr_brand' => $meter->mtr_brand,
                    'label' => $meter->mtr_serial.' ('.$meter->mtr_brand.')',
                ];
            });
    }

    /**
     * Get service connections without active meter assignment.
     */
    public function getUnassignedConnections(): Collection
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        return ServiceConnection::with(['customer', 'accountType', 'address.barangay'])
            ->where('stat_id', $activeStatusId)
            ->whereNull('ended_at')
            ->whereDoesntHave('meterAssignments', function ($query) {
                $query->whereNull('removed_at');
            })
            ->orderBy('account_no')
            ->get()
            ->map(function ($connection) {
                $customer = $connection->customer;
                $customerName = $customer
                    ? trim($customer->cust_first_name.' '.$customer->cust_last_name)
                    : 'Unknown';

                return [
                    'connection_id' => $connection->connection_id,
                    'account_no' => $connection->account_no,
                    'customer_name' => $customerName,
                    'account_type' => $connection->accountType?->at_desc ?? 'Unknown',
                    'barangay' => $connection->address?->barangay?->b_name ?? 'Unknown',
                    'label' => $connection->account_no.' - '.$customerName,
                ];
            });
    }

    /**
     * Assign a meter to a service connection.
     */
    public function assignMeter(array $data): array
    {
        // Validate meter exists and is available
        $meter = Meter::find($data['meter_id']);
        if (! $meter) {
            return [
                'success' => false,
                'message' => 'Meter not found.',
            ];
        }

        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        if ($meter->stat_id !== $activeStatusId) {
            return [
                'success' => false,
                'message' => 'Meter is not in active status.',
            ];
        }

        // Check if meter is already assigned
        $existingAssignment = MeterAssignment::where('meter_id', $data['meter_id'])
            ->whereNull('removed_at')
            ->first();

        if ($existingAssignment) {
            return [
                'success' => false,
                'message' => 'This meter is already assigned to another connection.',
            ];
        }

        // Validate service connection exists
        $connection = ServiceConnection::find($data['connection_id']);
        if (! $connection) {
            return [
                'success' => false,
                'message' => 'Service connection not found.',
            ];
        }

        // Check if connection already has an active meter
        $connectionMeter = MeterAssignment::where('connection_id', $data['connection_id'])
            ->whereNull('removed_at')
            ->first();

        if ($connectionMeter) {
            return [
                'success' => false,
                'message' => 'This connection already has an active meter assigned.',
            ];
        }

        // Create the assignment
        $assignment = MeterAssignment::create([
            'connection_id' => $data['connection_id'],
            'meter_id' => $data['meter_id'],
            'installed_at' => $data['installed_at'] ?? now()->format('Y-m-d'),
            'removed_at' => null,
            'install_read' => $data['install_read'] ?? 0,
            'removal_read' => null,
        ]);

        return [
            'success' => true,
            'message' => 'Meter assigned successfully.',
            'data' => $this->getAssignmentDetails($assignment->assignment_id),
        ];
    }

    /**
     * Remove a meter from a service connection.
     */
    public function removeMeter(int $assignmentId, array $data): array
    {
        $assignment = MeterAssignment::find($assignmentId);

        if (! $assignment) {
            return [
                'success' => false,
                'message' => 'Assignment not found.',
            ];
        }

        if ($assignment->removed_at !== null) {
            return [
                'success' => false,
                'message' => 'This meter has already been removed.',
            ];
        }

        // Validate removal reading is >= install reading
        $removalRead = $data['removal_read'] ?? 0;
        if ($removalRead < $assignment->install_read) {
            return [
                'success' => false,
                'message' => 'Removal reading cannot be less than installation reading.',
            ];
        }

        $assignment->update([
            'removed_at' => $data['removed_at'] ?? now()->format('Y-m-d'),
            'removal_read' => $removalRead,
        ]);

        return [
            'success' => true,
            'message' => 'Meter removed successfully.',
            'data' => $this->getAssignmentDetails($assignment->assignment_id),
        ];
    }

    /**
     * Get assignment details by ID.
     */
    public function getAssignmentDetails(int $assignmentId): ?array
    {
        $assignment = MeterAssignment::with([
            'meter',
            'serviceConnection.customer',
            'serviceConnection.accountType',
            'serviceConnection.address.barangay',
            'meterReadings' => function ($query) {
                $query->orderBy('reading_date', 'desc')->limit(5);
            },
        ])->find($assignmentId);

        if (! $assignment) {
            return null;
        }

        $data = $this->formatAssignmentData($assignment);

        // Add recent readings
        $data['recent_readings'] = $assignment->meterReadings->map(function ($reading) {
            return [
                'reading_id' => $reading->reading_id,
                'reading_date' => $reading->reading_date?->format('Y-m-d'),
                'current_reading' => $reading->current_reading,
                'consumption' => $reading->consumption,
            ];
        })->toArray();

        return $data;
    }

    /**
     * Get assignment statistics.
     */
    public function getStats(): array
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        $totalMeters = Meter::count();
        $availableMeters = Meter::where('stat_id', $activeStatusId)
            ->whereDoesntHave('meterAssignments', function ($query) {
                $query->whereNull('removed_at');
            })
            ->count();

        $activeAssignments = MeterAssignment::whereNull('removed_at')->count();

        $unassignedConnections = ServiceConnection::where('stat_id', $activeStatusId)
            ->whereNull('ended_at')
            ->whereDoesntHave('meterAssignments', function ($query) {
                $query->whereNull('removed_at');
            })
            ->count();

        return [
            'total_meters' => $totalMeters,
            'available_meters' => $availableMeters,
            'active_assignments' => $activeAssignments,
            'unassigned_connections' => $unassignedConnections,
        ];
    }

    /**
     * Format assignment data for API response.
     */
    private function formatAssignmentData(MeterAssignment $assignment): array
    {
        $connection = $assignment->serviceConnection;
        $customer = $connection?->customer;
        $meter = $assignment->meter;

        $customerName = $customer
            ? trim($customer->cust_first_name.' '.$customer->cust_last_name)
            : 'Unknown';

        $isActive = is_null($assignment->removed_at);

        return [
            'assignment_id' => $assignment->assignment_id,
            'connection_id' => $assignment->connection_id,
            'meter_id' => $assignment->meter_id,
            'account_no' => $connection?->account_no ?? 'N/A',
            'customer_name' => $customerName,
            'account_type' => $connection?->accountType?->at_desc ?? 'Unknown',
            'barangay' => $connection?->address?->barangay?->b_name ?? 'Unknown',
            'meter_serial' => $meter?->mtr_serial ?? 'N/A',
            'meter_brand' => $meter?->mtr_brand ?? 'N/A',
            'installed_at' => $assignment->installed_at?->format('Y-m-d'),
            'removed_at' => $assignment->removed_at?->format('Y-m-d'),
            'install_read' => $assignment->install_read,
            'removal_read' => $assignment->removal_read,
            'is_active' => $isActive,
            'status' => $isActive ? 'Active' : 'Removed',
            'status_class' => $isActive
                ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200'
                : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        ];
    }
}
