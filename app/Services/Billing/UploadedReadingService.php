<?php

namespace App\Services\Billing;

use App\Models\ReadingSchedule;
use App\Models\ReadingScheduleEntry;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Models\UploadedReading;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UploadedReadingService
{
    public function __construct(
        private WaterBillService $waterBillService,
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Process and store uploaded readings from mobile device.
     *
     * @param  array  $readings  Array of reading data
     * @param  int  $userId  Authenticated user ID
     * @return array Result with success status, counts, and data
     */
    public function processUploadedReadings(array $readings, int $userId): array
    {
        $uploaded = [];
        $failed = [];
        $scheduleUploadCounts = [];

        DB::beginTransaction();
        try {
            foreach ($readings as $index => $readingData) {
                try {
                    $result = $this->processSingleReading($readingData, $userId);

                    if ($result['success']) {
                        $uploaded[] = $result['data'];

                        // Track successful upload count per schedule (only for new records)
                        if ($result['is_new']) {
                            $scheduleId = $readingData['schedule_id'];
                            $scheduleUploadCounts[$scheduleId] = ($scheduleUploadCounts[$scheduleId] ?? 0) + 1;
                        }
                    }
                } catch (\Exception $e) {
                    $failed[] = [
                        'index' => $index,
                        'connection_id' => $readingData['connection_id'] ?? null,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            // Update meters_read count for each affected schedule
            $this->updateScheduleMetersRead($scheduleUploadCounts);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Readings uploaded successfully.',
                'uploaded_count' => count($uploaded),
                'failed_count' => count($failed),
                'data' => [
                    'uploaded' => $uploaded,
                    'failed' => $failed,
                ],
            ];
        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Failed to upload readings.',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Process a single reading entry.
     */
    private function processSingleReading(array $readingData, int $userId): array
    {
        // Calculate computed_amount using WaterBillService
        $computedAmount = $this->calculateBillAmount($readingData);

        // Process photo if provided
        $photoPath = null;
        if (! empty($readingData['photo'])) {
            $photoResult = $this->processReadingPhoto(
                $readingData['photo'],
                $readingData['schedule_id'],
                $readingData['connection_id']
            );

            if ($photoResult['success']) {
                $photoPath = $photoResult['path'];
                Log::info('Photo saved successfully', [
                    'path' => $photoPath,
                    'connection_id' => $readingData['connection_id'],
                    'schedule_id' => $readingData['schedule_id'],
                ]);
            } else {
                Log::error('Failed to save reading photo', [
                    'error' => $photoResult['error'] ?? 'Unknown error',
                    'connection_id' => $readingData['connection_id'],
                    'schedule_id' => $readingData['schedule_id'],
                    'photo_data_length' => strlen($readingData['photo']),
                ]);
            }
        }

        // Get existing photo path to preserve if not replacing
        $existingPhotoPath = $this->getExistingPhotoPath($readingData);

        // Use updateOrCreate to handle duplicates
        $uploadedReading = UploadedReading::updateOrCreate(
            [
                'schedule_id' => $readingData['schedule_id'],
                'connection_id' => $readingData['connection_id'],
            ],
            [
                'account_no' => $readingData['account_no'] ?? null,
                'customer_name' => $readingData['customer_name'] ?? null,
                'address' => $readingData['address'] ?? null,
                'area_desc' => $readingData['area_desc'] ?? null,
                'account_type_desc' => $readingData['account_type_desc'] ?? null,
                'connection_status' => $readingData['connection_status'] ?? null,
                'meter_serial' => $readingData['meter_serial'] ?? null,
                'previous_reading' => $readingData['previous_reading'] ?? null,
                'arrear' => $readingData['arrear'] ?? 0,
                'penalty' => $readingData['penalty'] ?? 0,
                'sequence_order' => $readingData['sequence_order'] ?? 0,
                'entry_status' => $readingData['entry_status'] ?? null,
                'present_reading' => $readingData['present_reading'] ?? null,
                'reading_date' => $readingData['reading_date'] ?? null,
                'site_bill_amount' => $readingData['site_bill_amount'] ?? null,
                'computed_amount' => $computedAmount,
                'is_printed' => $readingData['is_printed'] ?? false,
                'is_scanned' => $readingData['is_scanned'] ?? false,
                'photo_path' => $photoPath ?? $existingPhotoPath,
                'user_id' => $userId,
            ]
        );

        // Update reading_schedule_entries status to COMPLETED
        $this->markEntryAsCompleted($readingData['schedule_id'], $readingData['connection_id']);

        return [
            'success' => true,
            'is_new' => $uploadedReading->wasRecentlyCreated,
            'data' => [
                'uploaded_reading_id' => $uploadedReading->uploaded_reading_id,
                'connection_id' => $uploadedReading->connection_id,
                'account_no' => $uploadedReading->account_no,
            ],
        ];
    }

    /**
     * Process and store a reading photo.
     */
    private function processReadingPhoto(string $base64Data, int $scheduleId, int $connectionId): array
    {
        $directory = sprintf('reading-photos/%s/schedule-%d', date('Y/m'), $scheduleId);
        $filename = sprintf('reading-%d-%s', $connectionId, Str::random(8));

        return $this->fileUploadService->storeBase64Image($base64Data, $directory, $filename);
    }

    /**
     * Get existing photo path for updates (preserve if not replacing).
     */
    private function getExistingPhotoPath(array $readingData): ?string
    {
        return UploadedReading::where('schedule_id', $readingData['schedule_id'])
            ->where('connection_id', $readingData['connection_id'])
            ->value('photo_path');
    }

    /**
     * Calculate bill amount based on consumption.
     */
    private function calculateBillAmount(array $readingData): ?float
    {
        $previousReading = $readingData['previous_reading'] ?? 0;
        $presentReading = $readingData['present_reading'] ?? 0;
        $consumption = max(0, $presentReading - $previousReading);

        if ($consumption <= 0) {
            return null;
        }

        // Get account_type_id from ServiceConnection
        $connection = ServiceConnection::find($readingData['connection_id']);
        $accountTypeId = $connection?->account_type_id;

        if (! $accountTypeId) {
            return null;
        }

        // Get period_id from ReadingSchedule
        $schedule = ReadingSchedule::find($readingData['schedule_id']);
        $periodId = $schedule?->period_id;

        $billResult = $this->waterBillService->calculateBillAmount(
            (float) $consumption,
            (int) $accountTypeId,
            $periodId ? (int) $periodId : null
        );

        return $billResult['success'] ? $billResult['amount'] : null;
    }

    /**
     * Mark reading schedule entry as completed.
     */
    private function markEntryAsCompleted(int $scheduleId, int $connectionId): void
    {
        $completedStatusId = Status::getIdByDescription(Status::COMPLETED);

        if ($completedStatusId) {
            ReadingScheduleEntry::where('schedule_id', $scheduleId)
                ->where('connection_id', $connectionId)
                ->update(['status_id' => $completedStatusId]);
        }
    }

    /**
     * Update meters_read count for affected schedules.
     */
    private function updateScheduleMetersRead(array $scheduleUploadCounts): void
    {
        foreach ($scheduleUploadCounts as $scheduleId => $count) {
            ReadingSchedule::where('schedule_id', $scheduleId)
                ->increment('meters_read', $count);
        }
    }

    /**
     * Delete an uploaded reading.
     */
    public function deleteUploadedReading(int $uploadedReadingId): array
    {
        $reading = UploadedReading::find($uploadedReadingId);

        if (! $reading) {
            return ['success' => false, 'message' => 'Reading not found.'];
        }

        if ($reading->is_processed) {
            return ['success' => false, 'message' => 'Cannot delete a processed reading.'];
        }

        DB::beginTransaction();
        try {
            // Delete associated photo
            if ($reading->photo_path) {
                $this->fileUploadService->deleteFile($reading->photo_path);
            }

            // Update schedule meters_read count
            ReadingSchedule::where('schedule_id', $reading->schedule_id)
                ->where('meters_read', '>', 0)
                ->decrement('meters_read');

            // Reset entry status back to pending
            $pendingStatusId = Status::getIdByDescription(Status::PENDING);
            if ($pendingStatusId) {
                ReadingScheduleEntry::where('schedule_id', $reading->schedule_id)
                    ->where('connection_id', $reading->connection_id)
                    ->update(['status_id' => $pendingStatusId]);
            }

            $reading->delete();

            DB::commit();

            return ['success' => true, 'message' => 'Reading deleted successfully.'];
        } catch (\Exception $e) {
            DB::rollBack();

            return ['success' => false, 'message' => 'Failed to delete reading: '.$e->getMessage()];
        }
    }
}
