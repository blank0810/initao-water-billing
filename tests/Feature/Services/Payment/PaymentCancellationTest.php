<?php

namespace Tests\Feature\Services\Payment;

use App\Models\AccountType;
use App\Models\Area;
use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\Period;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Models\User;
use App\Models\WaterBillHistory;
use App\Services\Payment\PaymentService;

beforeEach(function () {
    // Ensure OVERDUE status exists (not seeded during migration, only by StatusSeeder)
    if (! \DB::table('statuses')->where('stat_desc', 'OVERDUE')->exists()) {
        \DB::table('statuses')->insert(['stat_desc' => 'OVERDUE']);
    }

    // Cache status IDs for use in tests and helpers
    $this->activeStatusId = Status::getIdByDescription(Status::ACTIVE);
    $this->cancelledStatusId = Status::getIdByDescription(Status::CANCELLED);
    $this->paidStatusId = Status::getIdByDescription(Status::PAID);
    $this->overdueStatusId = Status::getIdByDescription(Status::OVERDUE);
    $this->pendingStatusId = Status::getIdByDescription(Status::PENDING);

    // Create required user type for UserFactory (u_type = 3 => ADMIN)
    if (! \DB::table('user_types')->where('ut_id', 3)->exists()) {
        \DB::table('user_types')->insert([
            'ut_id' => 3,
            'ut_desc' => 'ADMIN',
            'stat_id' => $this->activeStatusId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    $this->user = User::factory()->create(['stat_id' => $this->activeStatusId]);
    $this->customer = Customer::factory()->create(['stat_id' => $this->activeStatusId]);

    // Create supporting records for ServiceConnection
    $this->accountType = AccountType::factory()->create(['stat_id' => $this->activeStatusId]);
    $this->area = Area::factory()->create(['stat_id' => $this->activeStatusId]);

    $this->connection = ServiceConnection::factory()->create([
        'customer_id' => $this->customer->cust_id,
        'address_id' => $this->customer->ca_id,
        'account_type_id' => $this->accountType->at_id,
        'area_id' => $this->area->a_id,
        'stat_id' => $this->activeStatusId,
    ]);

    $this->period = Period::create([
        'per_name' => 'January 2026',
        'per_code' => '2026-01',
        'start_date' => '2026-01-01',
        'end_date' => '2026-01-31',
        'grace_period' => 15,
        'is_closed' => false,
        'stat_id' => $this->activeStatusId,
    ]);

    // Create meter and readings needed for WaterBillHistory FK constraints
    $meterId = \DB::table('meter')->insertGetId([
        'mtr_serial' => 'MTR-TEST-001',
        'mtr_brand' => 'TestBrand',
        'stat_id' => $this->activeStatusId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $assignmentId = \DB::table('MeterAssignment')->insertGetId([
        'connection_id' => $this->connection->connection_id,
        'meter_id' => $meterId,
        'installed_at' => now()->subYear(),
        'install_read' => 0.000,
    ]);

    $this->prevReadingId = \DB::table('MeterReading')->insertGetId([
        'assignment_id' => $assignmentId,
        'period_id' => $this->period->per_id,
        'reading_date' => '2025-12-15',
        'reading_value' => 100.000,
        'is_estimated' => false,
        'meter_reader_id' => $this->user->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->currReadingId = \DB::table('MeterReading')->insertGetId([
        'assignment_id' => $assignmentId,
        'period_id' => $this->period->per_id,
        'reading_date' => '2026-01-15',
        'reading_value' => 110.000,
        'is_estimated' => false,
        'meter_reader_id' => $this->user->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Resolve the PaymentService from the container
    $this->paymentService = app(PaymentService::class);
});

/**
 * Helper: create a WaterBillHistory record.
 */
function createBill(array $overrides = []): WaterBillHistory
{
    $t = test();

    return WaterBillHistory::create(array_merge([
        'connection_id' => $t->connection->connection_id,
        'period_id' => $t->period->per_id,
        'prev_reading_id' => $t->prevReadingId,
        'curr_reading_id' => $t->currReadingId,
        'consumption' => 10.000,
        'water_amount' => 250.00,
        'due_date' => now()->addDays(30),
        'adjustment_total' => 0.00,
        'stat_id' => $t->paidStatusId,
    ], $overrides));
}

/**
 * Helper: create a Payment with allocation and ledger entry for a BILL.
 */
function createPaymentWithBillAllocation(array $billOverrides = []): array
{
    $t = test();

    $bill = createBill($billOverrides);

    $payment = Payment::create([
        'receipt_no' => 'OR-2026-'.fake()->unique()->numerify('#####'),
        'payer_id' => $t->customer->cust_id,
        'payment_date' => now(),
        'amount_received' => $bill->water_amount,
        'user_id' => $t->user->id,
        'stat_id' => $t->activeStatusId,
    ]);

    $allocation = PaymentAllocation::create([
        'payment_id' => $payment->payment_id,
        'target_type' => 'BILL',
        'target_id' => $bill->bill_id,
        'amount_applied' => $bill->water_amount,
        'period_id' => $t->period->per_id,
        'connection_id' => $t->connection->connection_id,
        'stat_id' => $t->activeStatusId,
    ]);

    // Create original PAYMENT ledger entry (credit)
    $ledger = CustomerLedger::create([
        'customer_id' => $t->customer->cust_id,
        'connection_id' => $t->connection->connection_id,
        'period_id' => $t->period->per_id,
        'txn_date' => now()->toDateString(),
        'post_ts' => now(),
        'source_type' => 'PAYMENT',
        'source_id' => $payment->payment_id,
        'source_line_no' => $allocation->payment_allocation_id,
        'description' => 'Payment for Water Bill - January 2026',
        'debit' => 0,
        'credit' => $bill->water_amount,
        'user_id' => $t->user->id,
        'stat_id' => $t->activeStatusId,
    ]);

    return compact('bill', 'payment', 'allocation', 'ledger');
}

// ---------------------------------------------------------------------------
// Tests
// ---------------------------------------------------------------------------

test('it cancels an active payment and marks it as cancelled', function () {
    $payment = Payment::create([
        'receipt_no' => 'OR-2026-00001',
        'payer_id' => $this->customer->cust_id,
        'payment_date' => now(),
        'amount_received' => 250.00,
        'user_id' => $this->user->id,
        'stat_id' => $this->activeStatusId,
    ]);

    $result = $this->paymentService->cancelPayment(
        $payment->payment_id,
        'Duplicate payment entry',
        $this->user->id
    );

    expect($result['success'])->toBeTrue();
    expect($result['message'])->toBe('Payment cancelled successfully.');
    expect($result['data']['payment_id'])->toBe($payment->payment_id);

    $payment->refresh();
    expect($payment->stat_id)->toBe($this->cancelledStatusId);
    expect($payment->cancelled_at)->not->toBeNull();
    expect($payment->cancelled_by)->toBe($this->user->id);
    expect($payment->cancellation_reason)->toBe('Duplicate payment entry');
});

test('it throws exception when cancelling already cancelled payment', function () {
    $payment = Payment::create([
        'receipt_no' => 'OR-2026-00002',
        'payer_id' => $this->customer->cust_id,
        'payment_date' => now(),
        'amount_received' => 250.00,
        'user_id' => $this->user->id,
        'stat_id' => $this->cancelledStatusId,
    ]);

    $this->paymentService->cancelPayment(
        $payment->payment_id,
        'Test reason',
        $this->user->id
    );
})->throws(\Exception::class, 'Payment is already cancelled.');

test('it throws exception for non-active payment', function () {
    $payment = Payment::create([
        'receipt_no' => 'OR-2026-00003',
        'payer_id' => $this->customer->cust_id,
        'payment_date' => now(),
        'amount_received' => 250.00,
        'user_id' => $this->user->id,
        'stat_id' => $this->pendingStatusId,
    ]);

    $this->paymentService->cancelPayment(
        $payment->payment_id,
        'Test reason',
        $this->user->id
    );
})->throws(\Exception::class, 'Only active payments can be cancelled.');

test('it creates reversal debit entries in customer ledger', function () {
    ['payment' => $payment, 'allocation' => $allocation, 'ledger' => $originalLedger] =
        createPaymentWithBillAllocation();

    $this->paymentService->cancelPayment(
        $payment->payment_id,
        'Incorrect amount',
        $this->user->id
    );

    // Original PAYMENT ledger entry should be marked CANCELLED
    $originalLedger->refresh();
    expect($originalLedger->stat_id)->toBe($this->cancelledStatusId);

    // A REVERSAL entry should have been created
    $reversalEntry = CustomerLedger::where('source_type', 'REVERSAL')
        ->where('source_id', $payment->payment_id)
        ->where('source_line_no', $allocation->payment_allocation_id)
        ->first();

    expect($reversalEntry)->not->toBeNull();
    expect((float) $reversalEntry->debit)->toBe((float) $allocation->amount_applied);
    expect((float) $reversalEntry->credit)->toBe(0.00);
    expect($reversalEntry->stat_id)->toBe($this->activeStatusId);
    expect($reversalEntry->customer_id)->toBe($this->customer->cust_id);
    expect($reversalEntry->connection_id)->toBe($this->connection->connection_id);
    expect($reversalEntry->description)->toContain('CANCELLED:');
    expect($reversalEntry->description)->toContain('Reason: Incorrect amount');
});

test('it reverts BILL ledger entry stat_id back to ACTIVE on cancellation', function () {
    ['bill' => $bill, 'payment' => $payment] = createPaymentWithBillAllocation();

    // Create the BILL ledger entry marked as PAID (as payment processing would do)
    $billLedger = CustomerLedger::create([
        'customer_id' => $this->customer->cust_id,
        'connection_id' => $this->connection->connection_id,
        'period_id' => $this->period->per_id,
        'txn_date' => now()->toDateString(),
        'post_ts' => now(),
        'source_type' => 'BILL',
        'source_id' => $bill->bill_id,
        'description' => 'Water Bill - January 2026',
        'debit' => $bill->water_amount,
        'credit' => 0,
        'user_id' => $this->user->id,
        'stat_id' => $this->paidStatusId,
    ]);

    $this->paymentService->cancelPayment(
        $payment->payment_id,
        'Reverting bill ledger test',
        $this->user->id
    );

    $billLedger->refresh();
    expect($billLedger->stat_id)->toBe($this->activeStatusId);
});

test('it reverts bill status from PAID back to ACTIVE when due date is in future', function () {
    ['bill' => $bill, 'payment' => $payment] = createPaymentWithBillAllocation([
        'due_date' => now()->addDays(30),
        'stat_id' => $this->paidStatusId,
    ]);

    $this->paymentService->cancelPayment(
        $payment->payment_id,
        'Customer requested cancellation',
        $this->user->id
    );

    $bill->refresh();
    expect($bill->stat_id)->toBe($this->activeStatusId);
});

test('it reverts bill status to OVERDUE when due date is past', function () {
    // Need a different period to avoid unique constraint on connection_id + period_id
    $pastPeriod = Period::create([
        'per_name' => 'November 2025',
        'per_code' => '2025-11',
        'start_date' => '2025-11-01',
        'end_date' => '2025-11-30',
        'grace_period' => 15,
        'is_closed' => false,
        'stat_id' => $this->activeStatusId,
    ]);

    // Need readings in the past period
    $meterId = \DB::table('meter')->insertGetId([
        'mtr_serial' => 'MTR-TEST-002',
        'mtr_brand' => 'TestBrand',
        'stat_id' => $this->activeStatusId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $assignmentId = \DB::table('MeterAssignment')->insertGetId([
        'connection_id' => $this->connection->connection_id,
        'meter_id' => $meterId,
        'installed_at' => now()->subYears(2),
        'install_read' => 0.000,
    ]);

    $prevReadingId = \DB::table('MeterReading')->insertGetId([
        'assignment_id' => $assignmentId,
        'period_id' => $pastPeriod->per_id,
        'reading_date' => '2025-10-15',
        'reading_value' => 80.000,
        'is_estimated' => false,
        'meter_reader_id' => $this->user->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $currReadingId = \DB::table('MeterReading')->insertGetId([
        'assignment_id' => $assignmentId,
        'period_id' => $pastPeriod->per_id,
        'reading_date' => '2025-11-15',
        'reading_value' => 90.000,
        'is_estimated' => false,
        'meter_reader_id' => $this->user->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $bill = WaterBillHistory::create([
        'connection_id' => $this->connection->connection_id,
        'period_id' => $pastPeriod->per_id,
        'prev_reading_id' => $prevReadingId,
        'curr_reading_id' => $currReadingId,
        'consumption' => 10.000,
        'water_amount' => 250.00,
        'due_date' => now()->subDays(30), // Past due date
        'adjustment_total' => 0.00,
        'stat_id' => $this->paidStatusId,
    ]);

    $payment = Payment::create([
        'receipt_no' => 'OR-2026-'.fake()->unique()->numerify('#####'),
        'payer_id' => $this->customer->cust_id,
        'payment_date' => now(),
        'amount_received' => 250.00,
        'user_id' => $this->user->id,
        'stat_id' => $this->activeStatusId,
    ]);

    $allocation = PaymentAllocation::create([
        'payment_id' => $payment->payment_id,
        'target_type' => 'BILL',
        'target_id' => $bill->bill_id,
        'amount_applied' => 250.00,
        'period_id' => $pastPeriod->per_id,
        'connection_id' => $this->connection->connection_id,
        'stat_id' => $this->activeStatusId,
    ]);

    // Create PAYMENT ledger entry
    CustomerLedger::create([
        'customer_id' => $this->customer->cust_id,
        'connection_id' => $this->connection->connection_id,
        'period_id' => $pastPeriod->per_id,
        'txn_date' => now()->toDateString(),
        'post_ts' => now(),
        'source_type' => 'PAYMENT',
        'source_id' => $payment->payment_id,
        'source_line_no' => $allocation->payment_allocation_id,
        'description' => 'Payment for Water Bill - November 2025',
        'debit' => 0,
        'credit' => 250.00,
        'user_id' => $this->user->id,
        'stat_id' => $this->activeStatusId,
    ]);

    $this->paymentService->cancelPayment(
        $payment->payment_id,
        'Late cancellation test',
        $this->user->id
    );

    $bill->refresh();
    expect($bill->stat_id)->toBe($this->overdueStatusId);
});

test('it marks all allocations as cancelled', function () {
    ['payment' => $payment, 'allocation' => $allocation] = createPaymentWithBillAllocation();

    $this->paymentService->cancelPayment(
        $payment->payment_id,
        'Cancelling all allocations',
        $this->user->id
    );

    $allocation->refresh();
    expect($allocation->stat_id)->toBe($this->cancelledStatusId);
});

test('it throws exception when allocation belongs to a closed period', function () {
    ['payment' => $payment] = createPaymentWithBillAllocation();

    // Close the period
    $this->period->update(['is_closed' => true, 'closed_at' => now()]);

    $this->paymentService->cancelPayment(
        $payment->payment_id,
        'Should not be allowed',
        $this->user->id
    );
})->throws(\Exception::class, 'Cannot cancel payment: The billing period "January 2026" has been closed.');

test('it throws exception for non-existent payment', function () {
    $this->paymentService->cancelPayment(
        99999,
        'Non-existent payment',
        $this->user->id
    );
})->throws(\Exception::class, 'Payment not found.');
