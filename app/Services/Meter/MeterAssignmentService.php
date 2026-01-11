<?php

namespace App\Services\Meter;

use App\Models\Meter;
use App\Models\MeterAssignment;
use App\Models\ServiceConnection;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MeterAssignmentService
{
    /**
     * Create a new meter and assign it to a service connection
     * Used for customer-purchased meters
     */
    public function createAndAssignMeter(
        int $connectionId,
        string $meterSerial,
        string $meterBrand,
        float $installRead,
        Carbon $installedAt
    ): MeterAssignment {
        // Verify connection exists and is active
        $connection = ServiceConnection::findOrFail($connectionId);

        if ($connection->stat_id !== Status::getIdByDescription(Status::ACTIVE)) {
            throw new \Exception('Can only assign meters to ACTIVE connections');
        }

        // Check if connection already has an active meter
        $currentAssignment = $this->getCurrentAssignment($connectionId);
        if ($currentAssignment) {
            throw new \Exception('Connection already has an active meter. Remove it first.');
        }

        // Check if meter serial already exists
        $existingMeter = Meter::where('mtr_serial', $meterSerial)->first();
        if ($existingMeter) {
            throw new \Exception('A meter with this serial number already exists');
        }

        return DB::transaction(function () use ($connectionId, $meterSerial, $meterBrand, $installRead, $installedAt) {
            // Create the meter record (status INACTIVE since it's being assigned)
            $meter = Meter::create([
                'mtr_serial' => $meterSerial,
                'mtr_brand' => $meterBrand,
                'stat_id' => Status::getIdByDescription(Status::INACTIVE),
            ]);

            // Create assignment
            $assignment = MeterAssignment::create([
                'connection_id' => $connectionId,
                'meter_id' => $meter->mtr_id,
                'installed_at' => $installedAt,
                'install_read' => $installRead,
            ]);

            return $assignment;
        });
    }

    /**
     * Assign a meter to a service connection
     */
    public function assignMeter(
        int $connectionId,
        int $meterId,
        float $installRead,
        Carbon $installedAt
    ): MeterAssignment {
        // Verify connection exists and is active
        $connection = ServiceConnection::findOrFail($connectionId);

        if ($connection->stat_id !== Status::getIdByDescription(Status::ACTIVE)) {
            throw new \Exception('Can only assign meters to ACTIVE connections');
        }

        // Check if connection already has an active meter
        $currentAssignment = $this->getCurrentAssignment($connectionId);
        if ($currentAssignment) {
            throw new \Exception('Connection already has an active meter. Remove it first.');
        }

        // Verify meter is available
        $meter = Meter::findOrFail($meterId);

        if ($meter->stat_id !== Status::getIdByDescription(Status::ACTIVE)) {
            throw new \Exception('Meter is not available for assignment');
        }

        // Check if meter is already assigned elsewhere
        $existingAssignment = MeterAssignment::where('meter_id', $meterId)
            ->whereNull('removed_at')
            ->first();

        if ($existingAssignment) {
            throw new \Exception('Meter is already assigned to another connection');
        }

        return DB::transaction(function () use ($connectionId, $meterId, $installRead, $installedAt, $meter) {
            // Create assignment
            $assignment = MeterAssignment::create([
                'connection_id' => $connectionId,
                'meter_id' => $meterId,
                'installed_at' => $installedAt,
                'install_read' => $installRead,
            ]);

            // Update meter status to indicate it's in use
            $meter->update([
                'stat_id' => Status::getIdByDescription(Status::INACTIVE),
            ]);

            return $assignment;
        });
    }

    /**
     * Remove a meter from a connection
     */
    public function removeMeter(
        int $assignmentId,
        float $removalRead,
        Carbon $removedAt,
        string $reason
    ): MeterAssignment {
        $assignment = MeterAssignment::with('meter')->findOrFail($assignmentId);

        if ($assignment->removed_at !== null) {
            throw new \Exception('Meter has already been removed');
        }

        if ($removalRead < $assignment->install_read) {
            throw new \Exception('Removal reading cannot be less than install reading');
        }

        return DB::transaction(function () use ($assignment, $removalRead, $removedAt) {
            // Update assignment with removal info
            $assignment->update([
                'removed_at' => $removedAt,
                'removal_read' => $removalRead,
            ]);

            // Make meter available again
            $assignment->meter->update([
                'stat_id' => Status::getIdByDescription(Status::ACTIVE),
            ]);

            return $assignment->fresh();
        });
    }

    /**
     * Replace a meter on a connection (remove old, assign new)
     */
    public function replaceMeter(
        int $connectionId,
        int $newMeterId,
        float $oldMeterRead,
        float $newMeterRead
    ): array {
        $currentAssignment = $this->getCurrentAssignment($connectionId);

        if (! $currentAssignment) {
            throw new \Exception('No active meter assignment found to replace');
        }

        return DB::transaction(function () use ($currentAssignment, $connectionId, $newMeterId, $oldMeterRead, $newMeterRead) {
            // Remove old meter
            $removedAssignment = $this->removeMeter(
                $currentAssignment->assignment_id,
                $oldMeterRead,
                now(),
                'Meter replacement'
            );

            // Assign new meter
            $newAssignment = $this->assignMeter(
                $connectionId,
                $newMeterId,
                $newMeterRead,
                now()
            );

            return [
                'removed_assignment' => $removedAssignment,
                'new_assignment' => $newAssignment,
            ];
        });
    }

    /**
     * Get all available meters (not currently assigned)
     */
    public function getAvailableMeters(): Collection
    {
        $assignedMeterIds = MeterAssignment::whereNull('removed_at')
            ->pluck('meter_id');

        return Meter::with('status')
            ->where('stat_id', Status::getIdByDescription(Status::ACTIVE))
            ->whereNotIn('mtr_id', $assignedMeterIds)
            ->orderBy('mtr_serial')
            ->get();
    }

    /**
     * Get current active assignment for a connection
     */
    public function getCurrentAssignment(int $connectionId): ?MeterAssignment
    {
        return MeterAssignment::with('meter')
            ->where('connection_id', $connectionId)
            ->whereNull('removed_at')
            ->first();
    }

    /**
     * Get assignment history for a connection
     */
    public function getAssignmentHistory(int $connectionId): Collection
    {
        return MeterAssignment::with('meter')
            ->where('connection_id', $connectionId)
            ->orderBy('installed_at', 'desc')
            ->get();
    }

    /**
     * Get all assignments for a meter (to track its history)
     */
    public function getMeterHistory(int $meterId): Collection
    {
        return MeterAssignment::with('serviceConnection.customer')
            ->where('meter_id', $meterId)
            ->orderBy('installed_at', 'desc')
            ->get();
    }

    /**
     * Calculate consumption for an assignment during a period
     */
    public function calculateConsumption(int $assignmentId, float $previousRead, float $currentRead): float
    {
        if ($currentRead < $previousRead) {
            throw new \Exception('Current reading cannot be less than previous reading');
        }

        return round($currentRead - $previousRead, 3);
    }

    /**
     * Get connections without assigned meters
     */
    public function getConnectionsWithoutMeters(): Collection
    {
        $connectionsWithMeters = MeterAssignment::whereNull('removed_at')
            ->pluck('connection_id');

        return ServiceConnection::with(['customer', 'address'])
            ->where('stat_id', Status::getIdByDescription(Status::ACTIVE))
            ->whereNotIn('connection_id', $connectionsWithMeters)
            ->get();
    }
}
