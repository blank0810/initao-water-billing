<?php

use App\Models\AccountType;
use App\Models\Area;
use App\Models\ConsumerAddress;
use App\Models\Customer;
use App\Models\Period;
use App\Models\ReadingSchedule;
use App\Models\ReadingScheduleEntry;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\Billing\ReadingScheduleService;
use Database\Seeders\StatusSeeder;
use Database\Seeders\UserTypeSeeder;

beforeEach(function () {
    $this->seed(StatusSeeder::class);
    $this->seed(UserTypeSeeder::class);

    $this->activeStatusId = Status::getIdByDescription(Status::ACTIVE);
    $this->completedStatusId = Status::getIdByDescription(Status::COMPLETED);
    $this->pendingStatusId = Status::getIdByDescription(Status::PENDING);

    // Create user for created_by FK constraint
    $this->user = User::factory()->create();

    // Create an account type for service connections
    $this->accountType = AccountType::factory()->create(['stat_id' => $this->activeStatusId]);

    // Create an open period
    $this->period = Period::create([
        'per_name' => 'February 2026',
        'per_code' => '202602',
        'start_date' => '2026-02-01',
        'end_date' => '2026-02-28',
        'grace_period' => 10,
        'is_closed' => false,
        'stat_id' => $this->activeStatusId,
    ]);

    // Create an area
    $this->area = Area::factory()->create(['stat_id' => $this->activeStatusId]);
});

/**
 * Create a reading schedule with entries, satisfying all FK constraints.
 */
function createTestScheduleWithEntries(
    int $periodId,
    int $areaId,
    int $activeStatusId,
    int $createdByUserId,
    int $entryCount = 3,
    string $status = 'in_progress',
    ?int $accountTypeId = null
): array {
    $schedule = ReadingSchedule::create([
        'period_id' => $periodId,
        'area_id' => $areaId,
        'scheduled_start_date' => '2026-02-01',
        'scheduled_end_date' => '2026-02-15',
        'status' => $status,
        'total_meters' => $entryCount,
        'meters_read' => 0,
        'created_by' => $createdByUserId,
        'stat_id' => $activeStatusId,
    ]);

    $pendingStatusId = Status::getIdByDescription(Status::PENDING);
    $connections = [];

    for ($i = 0; $i < $entryCount; $i++) {
        // Create valid FK dependencies for the connection
        $address = ConsumerAddress::factory()->create(['stat_id' => $activeStatusId]);
        $customer = Customer::factory()->create([
            'ca_id' => $address->ca_id,
            'stat_id' => $activeStatusId,
        ]);
        $atId = $accountTypeId ?? AccountType::first()?->at_id ?? AccountType::factory()->create(['stat_id' => $activeStatusId])->at_id;

        $connection = ServiceConnection::factory()->create([
            'customer_id' => $customer->cust_id,
            'area_id' => $areaId,
            'address_id' => $address->ca_id,
            'account_type_id' => $atId,
            'stat_id' => $activeStatusId,
        ]);
        $connections[] = $connection;

        ReadingScheduleEntry::create([
            'schedule_id' => $schedule->schedule_id,
            'connection_id' => $connection->connection_id,
            'sequence_order' => $i + 1,
            'status_id' => $pendingStatusId,
        ]);
    }

    return ['schedule' => $schedule, 'connections' => $connections];
}

test('schedule auto-completes when all entries are completed', function () {
    $data = createTestScheduleWithEntries(
        $this->period->per_id,
        $this->area->a_id,
        $this->activeStatusId,
        $this->user->id,
        3,
        'in_progress',
        $this->accountType->at_id
    );
    $schedule = $data['schedule'];

    // Mark all entries as completed
    ReadingScheduleEntry::where('schedule_id', $schedule->schedule_id)
        ->update(['status_id' => $this->completedStatusId]);

    // Call autoCompleteSchedule
    $service = app(ReadingScheduleService::class);
    $result = $service->autoCompleteSchedule($schedule->schedule_id);

    expect($result['success'])->toBeTrue();
    expect($result['message'])->toContain('auto-completed');

    // Assert schedule status is now 'completed'
    $schedule->refresh();
    expect($schedule->status)->toBe('completed');
    expect($schedule->completed_by)->toBeNull(); // System-initiated
    expect($schedule->actual_end_date)->not->toBeNull();
});

test('schedule does not auto-complete with pending entries', function () {
    $data = createTestScheduleWithEntries(
        $this->period->per_id,
        $this->area->a_id,
        $this->activeStatusId,
        $this->user->id,
        3,
        'in_progress',
        $this->accountType->at_id
    );
    $schedule = $data['schedule'];

    // Mark only 2 of 3 entries as completed
    $entries = ReadingScheduleEntry::where('schedule_id', $schedule->schedule_id)->get();
    $entries[0]->update(['status_id' => $this->completedStatusId]);
    $entries[1]->update(['status_id' => $this->completedStatusId]);
    // $entries[2] remains PENDING

    $service = app(ReadingScheduleService::class);
    $result = $service->autoCompleteSchedule($schedule->schedule_id);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('pending');

    // Schedule should still be in_progress
    $schedule->refresh();
    expect($schedule->status)->toBe('in_progress');
});

test('schedule does not auto-complete when it has no entries', function () {
    // Create a schedule with no entries
    $schedule = ReadingSchedule::create([
        'period_id' => $this->period->per_id,
        'area_id' => $this->area->a_id,
        'scheduled_start_date' => '2026-02-01',
        'scheduled_end_date' => '2026-02-15',
        'status' => 'in_progress',
        'total_meters' => 0,
        'meters_read' => 0,
        'created_by' => $this->user->id,
        'stat_id' => $this->activeStatusId,
    ]);

    $service = app(ReadingScheduleService::class);
    $result = $service->autoCompleteSchedule($schedule->schedule_id);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('no entries');

    $schedule->refresh();
    expect($schedule->status)->toBe('in_progress');
});

test('schedule does not auto-complete when status is not in_progress', function () {
    $data = createTestScheduleWithEntries(
        $this->period->per_id,
        $this->area->a_id,
        $this->activeStatusId,
        $this->user->id,
        2,
        'pending', // Not in_progress
        $this->accountType->at_id
    );
    $schedule = $data['schedule'];

    // Mark all entries as completed
    ReadingScheduleEntry::where('schedule_id', $schedule->schedule_id)
        ->update(['status_id' => $this->completedStatusId]);

    $service = app(ReadingScheduleService::class);
    $result = $service->autoCompleteSchedule($schedule->schedule_id);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('not eligible');

    $schedule->refresh();
    expect($schedule->status)->toBe('pending');
});

test('auto-close toggle returns correct values', function () {
    // Without setting, should return false
    expect(SystemSetting::isEnabled(SystemSetting::AUTO_CLOSE_READING_SCHEDULE))->toBeFalse();

    // Set to true
    SystemSetting::create([
        'key' => SystemSetting::AUTO_CLOSE_READING_SCHEDULE,
        'value' => 'true',
        'type' => 'boolean',
        'group' => 'automation',
    ]);

    expect(SystemSetting::isEnabled(SystemSetting::AUTO_CLOSE_READING_SCHEDULE))->toBeTrue();

    // Set to false
    SystemSetting::setValue(SystemSetting::AUTO_CLOSE_READING_SCHEDULE, 'false');
    expect(SystemSetting::isEnabled(SystemSetting::AUTO_CLOSE_READING_SCHEDULE))->toBeFalse();
});
