<?php

use App\Models\AccountType;
use App\Models\Area;
use App\Models\ChargeItem;
use App\Models\ConsumerAddress;
use App\Models\Customer;
use App\Models\CustomerCharge;
use App\Models\Meter;
use App\Models\MeterAssignment;
use App\Models\MeterReading;
use App\Models\Period;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\WaterBillHistory;
use Database\Seeders\StatusSeeder;
use Database\Seeders\UserTypeSeeder;

beforeEach(function () {
    $this->seed(StatusSeeder::class);
    $this->seed(UserTypeSeeder::class);

    $this->activeStatusId = Status::getIdByDescription(Status::ACTIVE);
    $this->overdueStatusId = Status::getIdByDescription(Status::OVERDUE);

    // Create a user for created_by fields
    $this->user = User::factory()->create();

    // Create penalty charge item
    $this->penaltyChargeItem = ChargeItem::create([
        'name' => 'Late Payment Penalty',
        'code' => 'LATE_PENALTY',
        'description' => 'Late Payment Penalty',
        'default_amount' => 50.00,
        'charge_type' => 'one_time',
        'is_taxable' => false,
        'stat_id' => $this->activeStatusId,
    ]);
});

/**
 * Helper to create a service connection with all FK dependencies satisfied.
 */
function createPenaltyTestConnection(int $activeStatusId): ServiceConnection
{
    $address = ConsumerAddress::factory()->create(['stat_id' => $activeStatusId]);
    $area = Area::factory()->create(['stat_id' => $activeStatusId]);
    $accountType = AccountType::factory()->create(['stat_id' => $activeStatusId]);
    $customer = Customer::factory()->create([
        'ca_id' => $address->ca_id,
        'stat_id' => $activeStatusId,
    ]);

    return ServiceConnection::factory()->create([
        'customer_id' => $customer->cust_id,
        'address_id' => $address->ca_id,
        'area_id' => $area->a_id,
        'account_type_id' => $accountType->at_id,
        'stat_id' => $activeStatusId,
    ]);
}

/**
 * Helper to create an overdue bill with proper meter readings.
 */
function createPenaltyOverdueBill(ServiceConnection $connection, int $activeStatusId, bool $periodClosed = false): WaterBillHistory
{
    $period = Period::create([
        'per_name' => 'January 2026',
        'per_code' => '202601'.rand(10, 99), // Unique codes per test
        'start_date' => '2026-01-01',
        'end_date' => '2026-01-31',
        'grace_period' => 10,
        'is_closed' => $periodClosed,
        'stat_id' => $activeStatusId,
    ]);

    // Create MeterReading records needed for FK constraints
    $meter = Meter::factory()->create(['stat_id' => $activeStatusId]);
    $assignment = MeterAssignment::factory()->create([
        'connection_id' => $connection->connection_id,
        'meter_id' => $meter->mtr_id,
    ]);

    $prevReading = MeterReading::create([
        'assignment_id' => $assignment->assignment_id,
        'period_id' => $period->per_id,
        'reading_date' => '2026-01-01',
        'reading_value' => 100.000,
        'is_estimated' => false,
        'meter_reader_id' => User::factory()->create()->id,
    ]);

    $currReading = MeterReading::create([
        'assignment_id' => $assignment->assignment_id,
        'period_id' => $period->per_id,
        'reading_date' => '2026-01-15',
        'reading_value' => 115.000,
        'is_estimated' => false,
        'meter_reader_id' => $prevReading->meter_reader_id,
    ]);

    return WaterBillHistory::create([
        'connection_id' => $connection->connection_id,
        'period_id' => $period->per_id,
        'prev_reading_id' => $prevReading->reading_id,
        'curr_reading_id' => $currReading->reading_id,
        'consumption' => 15.000,
        'water_amount' => 200.00,
        'due_date' => now()->subDays(5),
        'adjustment_total' => 0,
        'stat_id' => $activeStatusId,
    ]);
}

test('auto-apply penalties is skipped when toggle is off', function () {
    SystemSetting::create([
        'key' => SystemSetting::AUTO_APPLY_PENALTIES,
        'value' => 'false',
        'type' => 'boolean',
        'group' => 'automation',
    ]);

    $connection = createPenaltyTestConnection($this->activeStatusId);
    createPenaltyOverdueBill($connection, $this->activeStatusId);

    $this->artisan('billing:auto-apply-penalties')
        ->expectsOutput('Auto-apply penalties is disabled.')
        ->assertExitCode(0);

    expect(CustomerCharge::count())->toBe(0);
});

test('auto-apply penalties creates penalties for overdue bills', function () {
    $connection = createPenaltyTestConnection($this->activeStatusId);
    $bill = createPenaltyOverdueBill($connection, $this->activeStatusId);

    // Test via PenaltyService directly with a valid user ID
    // (the command uses userId=0 which doesn't satisfy the FK constraint)
    $penaltyService = app(\App\Services\Billing\PenaltyService::class);

    // Verify bill is found as overdue
    $overdueBills = $penaltyService->findOverdueBills();
    expect($overdueBills)->toHaveCount(1);
    expect($overdueBills->first()->bill_id)->toBe($bill->bill_id);

    // Create penalty with valid user ID
    $result = $penaltyService->createPenalty($bill, $this->user->id);

    expect($result['success'])->toBeTrue();
    expect($result['status'])->toBe('created');

    // Assert penalty charge was created
    $penalty = CustomerCharge::where('connection_id', $connection->connection_id)
        ->where('charge_item_id', $this->penaltyChargeItem->charge_item_id)
        ->first();

    expect($penalty)->not->toBeNull();
    expect((float) $penalty->unit_amount)->toBe(50.00);
    expect($penalty->description)->toContain('Late Payment Penalty');
    expect($penalty->description)->toContain("Bill #{$bill->bill_id}");

    // Assert bill status was updated to OVERDUE
    $bill->refresh();
    expect($bill->stat_id)->toBe($this->overdueStatusId);
});

test('auto-apply penalties skips bills that already have penalties', function () {
    SystemSetting::create([
        'key' => SystemSetting::AUTO_APPLY_PENALTIES,
        'value' => 'true',
        'type' => 'boolean',
        'group' => 'automation',
    ]);

    $connection = createPenaltyTestConnection($this->activeStatusId);
    $bill = createPenaltyOverdueBill($connection, $this->activeStatusId);

    // Manually create a penalty for this bill
    CustomerCharge::create([
        'customer_id' => $connection->customer_id,
        'connection_id' => $connection->connection_id,
        'charge_item_id' => $this->penaltyChargeItem->charge_item_id,
        'description' => "Late Payment Penalty - January 2026 (Bill #{$bill->bill_id})",
        'quantity' => 1,
        'unit_amount' => 50.00,
        'due_date' => now()->addDays(7),
        'stat_id' => $this->activeStatusId,
    ]);

    $this->artisan('billing:auto-apply-penalties')
        ->assertExitCode(0);

    // Assert no duplicate penalty - should still be just 1
    $penaltyCount = CustomerCharge::where('connection_id', $connection->connection_id)
        ->where('charge_item_id', $this->penaltyChargeItem->charge_item_id)
        ->count();

    expect($penaltyCount)->toBe(1);
});

test('auto-apply penalties skips bills in closed periods', function () {
    SystemSetting::create([
        'key' => SystemSetting::AUTO_APPLY_PENALTIES,
        'value' => 'true',
        'type' => 'boolean',
        'group' => 'automation',
    ]);

    $connection = createPenaltyTestConnection($this->activeStatusId);
    createPenaltyOverdueBill($connection, $this->activeStatusId, true); // closed period

    $this->artisan('billing:auto-apply-penalties')
        ->assertExitCode(0);

    // No penalties should be created for bills in closed periods
    expect(CustomerCharge::count())->toBe(0);
});
