<?php

namespace App\Services\Billing;

use App\Models\Area;
use App\Models\AreaAssignment;
use App\Models\Period;
use App\Models\ReadingSchedule;
use App\Models\ReadingScheduleEntry;
use App\Models\ServiceConnection;
use App\Models\Role;
use App\Models\Status;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReadingScheduleService
{
    /**
     * Get all reading schedules with related data.
     */
    public function getAllSchedules(): Collection
    {
        return ReadingSchedule::with(['area', 'period', 'reader', 'statusRecord'])
            ->orderBy('scheduled_start_date', 'desc')
            ->get()
            ->map(fn ($schedule) => $this->formatScheduleData($schedule));
    }

    /**
     * Get schedules filtered by status.
     */
    public function getSchedulesByStatus(string $status): Collection
    {
        $statuses = explode(',', $status);

        return ReadingSchedule::with(['area', 'period', 'reader', 'statusRecord'])
            ->whereIn('status', $statuses)
            ->orderBy('scheduled_start_date', 'desc')
            ->get()
            ->map(fn ($schedule) => $this->formatScheduleData($schedule));
    }

    /**
     * Get schedules for a specific period.
     */
    public function getSchedulesByPeriod(int $periodId): Collection
    {
        return ReadingSchedule::with(['area', 'period', 'reader', 'statusRecord'])
            ->forPeriod($periodId)
            ->orderBy('scheduled_start_date', 'desc')
            ->get()
            ->map(fn ($schedule) => $this->formatScheduleData($schedule));
    }

    /**
     * Get schedules for a specific area.
     */
    public function getSchedulesByArea(int $areaId): Collection
    {
        return ReadingSchedule::with(['area', 'period', 'reader', 'statusRecord'])
            ->forArea($areaId)
            ->orderBy('scheduled_start_date', 'desc')
            ->get()
            ->map(fn ($schedule) => $this->formatScheduleData($schedule));
    }

    /**
     * Get schedules for a specific reader.
     */
    public function getSchedulesByReader(int $readerId): Collection
    {
        return ReadingSchedule::with(['area', 'period', 'reader', 'statusRecord'])
            ->forReader($readerId)
            ->orderBy('scheduled_start_date', 'desc')
            ->get()
            ->map(fn ($schedule) => $this->formatScheduleData($schedule));
    }

    /**
     * Get a single schedule by ID.
     */
    public function getScheduleById(int $scheduleId): ?array
    {
        $schedule = ReadingSchedule::with(['area', 'period', 'reader', 'creator', 'completer', 'statusRecord'])
            ->find($scheduleId);

        if (! $schedule) {
            return null;
        }

        return $this->formatScheduleData($schedule);
    }

    /**
     * Get available meter readers for scheduling.
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
     * Get available areas for scheduling.
     */
    public function getAvailableAreas(): Collection
    {
        return Area::with('status')
            ->orderBy('a_desc')
            ->get()
            ->map(function ($area) {
                return [
                    'id' => $area->a_id,
                    'name' => $area->a_desc,
                    'status' => $area->status?->stat_desc ?? 'Unknown',
                ];
            });
    }

    /**
     * Get available periods for scheduling.
     */
    public function getAvailablePeriods(): Collection
    {
        return Period::where('is_closed', false)
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($period) {
                return [
                    'id' => $period->per_id,
                    'name' => $period->per_name ?? $period->per_code ?? 'Period #'.$period->per_id,
                    'code' => $period->per_code,
                    'start_date' => $period->start_date?->format('Y-m-d'),
                    'end_date' => $period->end_date?->format('Y-m-d'),
                ];
            });
    }

    /**
     * Get the assigned meter reader for an area.
     */
    public function getAssignedReaderForArea(int $areaId): ?array
    {
        $assignment = AreaAssignment::with('user')
            ->active()
            ->forArea($areaId)
            ->orderBy('effective_from', 'desc')
            ->first();

        if (! $assignment || ! $assignment->user) {
            return null;
        }

        return [
            'id' => $assignment->user->id,
            'name' => $assignment->user->name,
            'email' => $assignment->user->email,
        ];
    }

    /**
     * Get the count of billable connections in an area for a period
     * (connections that don't have a bill yet for this period).
     */
    public function getBillableCountForArea(int $areaId, int $periodId): int
    {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        // Get all active connections in the area
        // that DO NOT have a WaterBillHistory record for this period
        return ServiceConnection::where('area_id', $areaId)
            ->where('stat_id', $activeStatusId)
            ->whereNull('ended_at')
            ->whereDoesntHave('waterBillHistory', function ($query) use ($periodId) {
                $query->where('period_id', $periodId);
            })
            ->count();
    }

    /**
     * Create a new reading schedule.
     */
    public function createSchedule(array $data): array
    {
        // Validate area exists
        $area = Area::find($data['area_id']);
        if (! $area) {
            return [
                'success' => false,
                'message' => 'Area not found.',
            ];
        }

        // Validate period exists
        $period = Period::find($data['period_id']);
        if (! $period) {
            return [
                'success' => false,
                'message' => 'Period not found.',
            ];
        }

        // Validate reader exists and is a meter reader
        $reader = User::find($data['reader_id']);
        if (! $reader) {
            return [
                'success' => false,
                'message' => 'Reader not found.',
            ];
        }

        if (! $reader->isMeterReader()) {
            return [
                'success' => false,
                'message' => 'Selected user is not a meter reader.',
            ];
        }

        // Check for duplicate schedule (same area, period, reader)
        $exists = ReadingSchedule::where('area_id', $data['area_id'])
            ->where('period_id', $data['period_id'])
            ->where('reader_id', $data['reader_id'])
            ->exists();

        if ($exists) {
            return [
                'success' => false,
                'message' => 'A schedule already exists for this area, period, and reader.',
            ];
        }

        // Calculate total meters if not provided
        $totalMeters = $data['total_meters'] ?? $this->getBillableCountForArea($data['area_id'], $data['period_id']);

        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        DB::beginTransaction();
        try {
            $schedule = ReadingSchedule::create([
                'period_id' => $data['period_id'],
                'area_id' => $data['area_id'],
                'reader_id' => $data['reader_id'],
                'scheduled_start_date' => $data['scheduled_start_date'],
                'scheduled_end_date' => $data['scheduled_end_date'],
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
                'total_meters' => $totalMeters,
                'meters_read' => 0,
                'meters_missed' => 0,
                'created_by' => Auth::id(),
                'stat_id' => $activeStatusId,
            ]);

            // Populate reading_schedule_entries
            $connections = ServiceConnection::where('area_id', $data['area_id'])
                ->where('stat_id', $activeStatusId)
                ->whereNull('ended_at')
                ->whereDoesntHave('waterBillHistory', function ($query) use ($data) {
                    $query->where('period_id', $data['period_id']);
                })
                ->orderBy('account_no') // Default sequence by account number
                ->get();

            $entries = [];
            foreach ($connections as $index => $conn) {
                $entries[] = [
                    'schedule_id' => $schedule->schedule_id,
                    'connection_id' => $conn->connection_id,
                    'sequence_order' => $index + 1,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (! empty($entries)) {
                // Bulk insert
                ReadingScheduleEntry::insert($entries);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Reading schedule created successfully with ' . count($entries) . ' entries.',
                'data' => $this->getScheduleById($schedule->schedule_id),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Error creating schedule: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update an existing schedule.
     */
    public function updateSchedule(int $scheduleId, array $data): array
    {
        $schedule = ReadingSchedule::find($scheduleId);

        if (! $schedule) {
            return [
                'success' => false,
                'message' => 'Schedule not found.',
            ];
        }

        // Validate reader if provided
        if (isset($data['reader_id'])) {
            $reader = User::find($data['reader_id']);
            if (! $reader) {
                return [
                    'success' => false,
                    'message' => 'Reader not found.',
                ];
            }

            if (! $reader->isMeterReader()) {
                return [
                    'success' => false,
                    'message' => 'Selected user is not a meter reader.',
                ];
            }
        }

        $updateData = [];

        $allowedFields = [
            'reader_id',
            'scheduled_start_date',
            'scheduled_end_date',
            'actual_start_date',
            'actual_end_date',
            'status',
            'notes',
            'total_meters',
            'meters_read',
            'meters_missed',
        ];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        $schedule->update($updateData);

        return [
            'success' => true,
            'message' => 'Schedule updated successfully.',
            'data' => $this->getScheduleById($schedule->schedule_id),
        ];
    }

    /**
     * Start a schedule (change status to in_progress).
     */
    public function startSchedule(int $scheduleId): array
    {
        $schedule = ReadingSchedule::find($scheduleId);

        if (! $schedule) {
            return [
                'success' => false,
                'message' => 'Schedule not found.',
            ];
        }

        if ($schedule->status !== 'pending') {
            return [
                'success' => false,
                'message' => 'Only pending schedules can be started.',
            ];
        }

        $schedule->update([
            'status' => 'in_progress',
            'actual_start_date' => now()->format('Y-m-d'),
        ]);

        return [
            'success' => true,
            'message' => 'Schedule started successfully.',
            'data' => $this->getScheduleById($schedule->schedule_id),
        ];
    }

    /**
     * Complete a schedule.
     */
    public function completeSchedule(int $scheduleId, array $data = []): array
    {
        $schedule = ReadingSchedule::find($scheduleId);

        if (! $schedule) {
            return [
                'success' => false,
                'message' => 'Schedule not found.',
            ];
        }

        if ($schedule->status !== 'in_progress') {
            return [
                'success' => false,
                'message' => 'Only in-progress schedules can be completed.',
            ];
        }

        $updateData = [
            'status' => 'completed',
            'actual_end_date' => now()->format('Y-m-d'),
            'completed_by' => Auth::id(),
        ];

        if (isset($data['meters_read'])) {
            $updateData['meters_read'] = $data['meters_read'];
        }

        if (isset($data['meters_missed'])) {
            $updateData['meters_missed'] = $data['meters_missed'];
        }

        $schedule->update($updateData);

        return [
            'success' => true,
            'message' => 'Schedule completed successfully.',
            'data' => $this->getScheduleById($schedule->schedule_id),
        ];
    }

    /**
     * Mark schedule as delayed.
     */
    public function markAsDelayed(int $scheduleId, ?string $notes = null): array
    {
        $schedule = ReadingSchedule::find($scheduleId);

        if (! $schedule) {
            return [
                'success' => false,
                'message' => 'Schedule not found.',
            ];
        }

        $updateData = ['status' => 'delayed'];

        if ($notes) {
            $updateData['notes'] = $schedule->notes
                ? $schedule->notes."\n".$notes
                : $notes;
        }

        $schedule->update($updateData);

        return [
            'success' => true,
            'message' => 'Schedule marked as delayed.',
            'data' => $this->getScheduleById($schedule->schedule_id),
        ];
    }

    /**
     * Delete a schedule.
     */
    public function deleteSchedule(int $scheduleId): array
    {
        $schedule = ReadingSchedule::find($scheduleId);

        if (! $schedule) {
            return [
                'success' => false,
                'message' => 'Schedule not found.',
            ];
        }

        if ($schedule->status === 'completed') {
            return [
                'success' => false,
                'message' => 'Cannot delete completed schedules.',
            ];
        }

        $schedule->delete();

        return [
            'success' => true,
            'message' => 'Schedule deleted successfully.',
        ];
    }

    /**
     * Get reading statistics.
     */
    public function getStats(): array
    {
        return [
            'total_schedules' => ReadingSchedule::count(),
            'pending' => ReadingSchedule::pending()->count(),
            'in_progress' => ReadingSchedule::inProgress()->count(),
            'completed' => ReadingSchedule::completed()->count(),
            'delayed' => ReadingSchedule::delayed()->count(),
        ];
    }

    /**
     * Get readings data for download (to handheld/mobile).
     */
    public function getDownloadData(int $scheduleId): array
    {
        $schedule = ReadingSchedule::with(['area', 'period', 'reader'])->find($scheduleId);

        if (! $schedule) {
            return [
                'success' => false,
                'message' => 'Schedule not found.',
            ];
        }

        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        // Get all active service connections in this area
        $connections = ServiceConnection::with([
            'customer',
            'address.barangay',
            'address.purok',
            'meterAssignments' => function ($query) {
                $query->whereNull('removed_at');
            },
            'meterAssignments.meter',
        ])
            ->where('area_id', $schedule->area_id)
            ->where('stat_id', $activeStatusId)
            ->whereNull('ended_at')
            ->get();

        $readings = $connections->map(function ($conn) {
            $customer = $conn->customer;
            $activeAssignment = $conn->meterAssignments->first();

            // Get last reading for this assignment
            $lastReading = MeterReading::where('assignment_id', $activeAssignment?->assignment_id)
                ->orderBy('reading_date', 'desc')
                ->orderBy('reading_id', 'desc')
                ->first();

            $prevReading = $lastReading ? (float) $lastReading->reading_value : (float) ($activeAssignment?->install_read ?? 0);
            $prevDate = $lastReading ? $lastReading->reading_date?->format('Y-m-d') : $activeAssignment?->installed_at?->format('Y-m-d');

            return [
                'connection_id' => $conn->connection_id,
                'account_no' => $conn->account_no,
                'customer_name' => $customer ? trim($customer->cust_first_name.' '.$customer->cust_last_name) : 'Unknown',
                'barangay' => $conn->address?->barangay?->b_desc ?? 'N/A',
                'purok' => $conn->address?->purok?->p_desc ?? 'N/A',
                'meter_serial' => $activeAssignment?->meter?->mtr_serial ?? 'N/A',
                'prev_reading' => $prevReading,
                'prev_reading_date' => $prevDate,
            ];
        });

        return [
            'success' => true,
            'schedule' => [
                'schedule_id' => $schedule->schedule_id,
                'area_name' => $schedule->area?->a_desc,
                'period_name' => $schedule->period?->per_name ?? $schedule->period?->per_code,
                'reader_name' => $schedule->reader?->name,
            ],
            'readings' => $readings,
        ];
    }

    /**
     * Format schedule data for API response.
     */
    private function formatScheduleData(ReadingSchedule $schedule): array
    {
        $statusClasses = [
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200',
            'in_progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200',
            'completed' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
            'delayed' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200',
        ];

        return [
            'schedule_id' => $schedule->schedule_id,
            'period_id' => $schedule->period_id,
            'area_id' => $schedule->area_id,
            'reader_id' => $schedule->reader_id,
            'area_name' => $schedule->area?->a_desc ?? 'Unknown',
            'period_name' => $schedule->period
                ? ($schedule->period->per_name ?? $schedule->period->per_code ?? 'Period #'.$schedule->period->per_id)
                : 'Unknown',
            'reader_name' => $schedule->reader?->name ?? 'Unknown',
            'reader_email' => $schedule->reader?->email ?? '',
            'scheduled_start_date' => $schedule->scheduled_start_date?->format('Y-m-d'),
            'scheduled_end_date' => $schedule->scheduled_end_date?->format('Y-m-d'),
            'actual_start_date' => $schedule->actual_start_date?->format('Y-m-d'),
            'actual_end_date' => $schedule->actual_end_date?->format('Y-m-d'),
            'status' => $schedule->status,
            'status_label' => ucfirst(str_replace('_', ' ', $schedule->status)),
            'status_class' => $statusClasses[$schedule->status] ?? $statusClasses['pending'],
            'notes' => $schedule->notes,
            'total_meters' => $schedule->total_meters,
            'meters_read' => $schedule->meters_read,
            'meters_missed' => $schedule->meters_missed,
            'completion_percentage' => $schedule->getCompletionPercentage(),
            'created_by' => $schedule->created_by,
            'creator_name' => $schedule->creator?->name ?? 'Unknown',
            'completed_by' => $schedule->completed_by,
            'completer_name' => $schedule->completer?->name ?? null,
            'created_at' => $schedule->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $schedule->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}