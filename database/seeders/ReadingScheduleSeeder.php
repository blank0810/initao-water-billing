<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\ReadingSchedule;
use App\Models\ReadingScheduleEntry;
use App\Models\Role;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReadingScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates one pending reading schedule per area that has active service connections
     * for the current open period. Each schedule is auto-populated with entries.
     */
    public function run(): void
    {
        // Get the current open period
        $period = DB::table('period')
            ->where('is_closed', false)
            ->orderBy('start_date', 'desc')
            ->first();

        if (! $period) {
            $this->command->error('No open period found. Please run PeriodSeeder first.');

            return;
        }

        // Get status IDs
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $pendingStatusId = Status::getIdByDescription(Status::PENDING);

        // Get the meter reader user
        $meterReader = User::whereHas('roles', function ($query) {
            $query->where('role_name', Role::METER_READER);
        })->first();

        if (! $meterReader) {
            $this->command->error('No meter reader user found. Please run TestUsersSeeder first.');

            return;
        }

        // Get an admin user for created_by
        $admin = User::whereHas('roles', function ($query) {
            $query->where('role_name', Role::ADMIN);
        })->first();

        if (! $admin) {
            $this->command->error('No admin user found. Please run TestUsersSeeder first.');

            return;
        }

        $schedulesCreated = 0;
        $totalEntries = 0;

        // Get all areas
        $areas = Area::where('stat_id', $activeStatusId)->get();

        foreach ($areas as $area) {
            // Get active, non-ended connections in this area
            $connections = ServiceConnection::where('area_id', $area->a_id)
                ->where('stat_id', $activeStatusId)
                ->whereNull('ended_at')
                ->orderBy('account_no')
                ->get();

            // Skip areas with no connections
            if ($connections->isEmpty()) {
                continue;
            }

            // Skip if schedule already exists for this area+period
            $exists = ReadingSchedule::where('area_id', $area->a_id)
                ->where('period_id', $period->per_id)
                ->exists();

            if ($exists) {
                continue;
            }

            // Create the reading schedule
            $schedule = ReadingSchedule::create([
                'period_id' => $period->per_id,
                'area_id' => $area->a_id,
                'reader_id' => $meterReader->id,
                'scheduled_start_date' => now()->startOfMonth()->format('Y-m-d'),
                'scheduled_end_date' => now()->startOfMonth()->addDays(14)->format('Y-m-d'),
                'status' => 'pending',
                'total_meters' => $connections->count(),
                'meters_read' => 0,
                'meters_missed' => 0,
                'created_by' => $admin->id,
                'stat_id' => $activeStatusId,
            ]);

            // Create entries for each connection
            $entries = [];
            foreach ($connections as $index => $connection) {
                $entries[] = [
                    'schedule_id' => $schedule->schedule_id,
                    'connection_id' => $connection->connection_id,
                    'sequence_order' => $index + 1,
                    'status_id' => $pendingStatusId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            ReadingScheduleEntry::insert($entries);

            $schedulesCreated++;
            $totalEntries += count($entries);
        }

        $this->command->info("Reading Schedules seeded: {$schedulesCreated} schedules with {$totalEntries} total entries");
    }
}
