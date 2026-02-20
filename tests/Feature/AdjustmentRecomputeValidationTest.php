<?php

use App\Models\AccountType;
use App\Models\Area;
use App\Models\ConsumerAddress;
use App\Models\Customer;
use App\Models\Meter;
use App\Models\MeterAssignment;
use App\Models\MeterReading;
use App\Models\Period;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Models\User;
use App\Models\WaterBillHistory;
use App\Services\Billing\BillAdjustmentService;
use Database\Seeders\StatusSeeder;
use Database\Seeders\UserTypeSeeder;

beforeEach(function () {
    $this->seed(StatusSeeder::class);
    $this->seed(UserTypeSeeder::class);

    $this->activeStatusId = Status::getIdByDescription(Status::ACTIVE);

    // Create required FK records
    $address = ConsumerAddress::factory()->create(['stat_id' => $this->activeStatusId]);
    $this->area = Area::factory()->create(['stat_id' => $this->activeStatusId]);
    $this->accountType = AccountType::factory()->create(['stat_id' => $this->activeStatusId]);
    $this->customer = Customer::factory()->create([
        'ca_id' => $address->ca_id,
        'stat_id' => $this->activeStatusId,
    ]);

    $this->connection = ServiceConnection::factory()->create([
        'customer_id' => $this->customer->cust_id,
        'address_id' => $address->ca_id,
        'area_id' => $this->area->a_id,
        'account_type_id' => $this->accountType->at_id,
        'stat_id' => $this->activeStatusId,
    ]);

    // Create meter + assignment for MeterReading FK
    $this->meter = Meter::factory()->create(['stat_id' => $this->activeStatusId]);
    $this->assignment = MeterAssignment::factory()->create([
        'connection_id' => $this->connection->connection_id,
        'meter_id' => $this->meter->mtr_id,
    ]);
    $this->readerUser = User::factory()->create();
});

/**
 * Helper to create a bill in a period with proper meter reading FKs.
 */
function createAdjustmentTestBill(
    int $connectionId,
    int $assignmentId,
    int $readerUserId,
    int $activeStatusId,
    bool $periodClosed,
    string $perCode = '202601',
    string $perName = 'January 2026'
): array {
    $period = Period::create([
        'per_name' => $perName,
        'per_code' => $perCode,
        'start_date' => '2026-01-01',
        'end_date' => '2026-01-31',
        'grace_period' => 10,
        'is_closed' => $periodClosed,
        'stat_id' => $activeStatusId,
    ]);

    $prevReading = MeterReading::create([
        'assignment_id' => $assignmentId,
        'period_id' => $period->per_id,
        'reading_date' => '2026-01-01',
        'reading_value' => 100.000,
        'is_estimated' => false,
        'meter_reader_id' => $readerUserId,
    ]);

    $currReading = MeterReading::create([
        'assignment_id' => $assignmentId,
        'period_id' => $period->per_id,
        'reading_date' => '2026-01-15',
        'reading_value' => 110.000,
        'is_estimated' => false,
        'meter_reader_id' => $readerUserId,
    ]);

    $bill = WaterBillHistory::create([
        'connection_id' => $connectionId,
        'period_id' => $period->per_id,
        'prev_reading_id' => $prevReading->reading_id,
        'curr_reading_id' => $currReading->reading_id,
        'consumption' => 10.000,
        'water_amount' => 150.00,
        'due_date' => now()->addDays(15),
        'adjustment_total' => 0,
        'stat_id' => $activeStatusId,
    ]);

    return ['period' => $period, 'bill' => $bill];
}

test('consumption adjustment is blocked on open period', function () {
    $data = createAdjustmentTestBill(
        $this->connection->connection_id,
        $this->assignment->assignment_id,
        $this->readerUser->id,
        $this->activeStatusId,
        false // Open period
    );

    $service = app(BillAdjustmentService::class);
    $result = $service->adjustConsumption([
        'bill_id' => $data['bill']->bill_id,
        'new_curr_reading' => 120,
        'new_prev_reading' => 100,
    ]);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('still open');
    expect($result['message'])->toContain('Recompute Bill');
});

test('amount adjustment is blocked on open period', function () {
    $data = createAdjustmentTestBill(
        $this->connection->connection_id,
        $this->assignment->assignment_id,
        $this->readerUser->id,
        $this->activeStatusId,
        false, // Open period
        '202602',
        'February 2026'
    );

    $service = app(BillAdjustmentService::class);
    $result = $service->adjustAmount([
        'bill_id' => $data['bill']->bill_id,
        'bill_adjustment_type_id' => 1,
        'amount' => 50.00,
    ]);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('still open');
    expect($result['message'])->toContain('Recompute Bill');
});

test('recompute is blocked on closed period', function () {
    $data = createAdjustmentTestBill(
        $this->connection->connection_id,
        $this->assignment->assignment_id,
        $this->readerUser->id,
        $this->activeStatusId,
        true, // Closed period
        '202603',
        'March 2026'
    );

    $service = app(BillAdjustmentService::class);
    $result = $service->recomputeBill($data['bill']->bill_id, 'Test recompute');

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('closed period');
    expect($result['message'])->toContain('adjustments');
});

test('recompute is allowed on open period', function () {
    $data = createAdjustmentTestBill(
        $this->connection->connection_id,
        $this->assignment->assignment_id,
        $this->readerUser->id,
        $this->activeStatusId,
        false, // Open period
        '202604',
        'April 2026'
    );

    $service = app(BillAdjustmentService::class);

    // This will fail with "No change in consumption" or "No water rates found" since we are not
    // changing readings and haven't set up rates - which proves it passed the period check.
    $result = $service->recomputeBill($data['bill']->bill_id, 'Test recompute');

    // The result may fail for other reasons (no rates, no change, etc.)
    // but it should NOT fail with "closed period" message.
    expect($result['message'])->not->toContain('closed period');
});

test('consumption adjustment on non-existent bill returns error', function () {
    $service = app(BillAdjustmentService::class);
    $result = $service->adjustConsumption([
        'bill_id' => 99999,
        'new_curr_reading' => 120,
        'new_prev_reading' => 100,
    ]);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('Bill not found');
});

test('recompute on non-existent bill returns error', function () {
    $service = app(BillAdjustmentService::class);
    $result = $service->recomputeBill(99999);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('Bill not found');
});
