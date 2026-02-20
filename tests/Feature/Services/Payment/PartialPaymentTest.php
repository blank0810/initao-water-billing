<?php

namespace Tests\Feature\Services\Payment;

use App\Models\AccountType;
use App\Models\Area;
use App\Models\ChargeItem;
use App\Models\Customer;
use App\Models\CustomerCharge;
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
    if (! \DB::table('statuses')->where('stat_desc', 'OVERDUE')->exists()) {
        \DB::table('statuses')->insert(['stat_desc' => 'OVERDUE']);
    }

    $this->activeStatusId = Status::getIdByDescription(Status::ACTIVE);
    $this->cancelledStatusId = Status::getIdByDescription(Status::CANCELLED);
    $this->paidStatusId = Status::getIdByDescription(Status::PAID);
    $this->overdueStatusId = Status::getIdByDescription(Status::OVERDUE);
    $this->pendingStatusId = Status::getIdByDescription(Status::PENDING);

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
        'per_name' => 'Test Period',
        'per_code' => '2026-TEST',
        'start_date' => '2026-01-01',
        'end_date' => '2026-01-31',
        'grace_period' => 15,
        'is_closed' => false,
        'stat_id' => $this->activeStatusId,
    ]);

    $meterId = \DB::table('meter')->insertGetId([
        'mtr_serial' => 'MTR-PARTIAL-'.uniqid(),
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
        'stat_id' => $this->activeStatusId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $prevReadingId = \DB::table('MeterReading')->insertGetId([
        'assignment_id' => $assignmentId,
        'period_id' => $this->period->per_id,
        'reading_value' => 100.000,
        'reading_date' => now()->subMonth(),
        'stat_id' => $this->activeStatusId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $currReadingId = \DB::table('MeterReading')->insertGetId([
        'assignment_id' => $assignmentId,
        'period_id' => $this->period->per_id,
        'reading_value' => 120.000,
        'reading_date' => now(),
        'stat_id' => $this->activeStatusId,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Create bill: water_amount = 100, adjustment_total = 0 → total = 100
    $this->bill = WaterBillHistory::create([
        'connection_id' => $this->connection->connection_id,
        'period_id' => $this->period->per_id,
        'prev_reading_id' => $prevReadingId,
        'curr_reading_id' => $currReadingId,
        'consumption' => 20.000,
        'water_amount' => 100.00,
        'due_date' => now()->addDays(15),
        'adjustment_total' => 0.00,
        'stat_id' => $this->activeStatusId,
    ]);

    // Create BILL ledger entry
    CustomerLedger::create([
        'customer_id' => $this->customer->cust_id,
        'connection_id' => $this->connection->connection_id,
        'period_id' => $this->period->per_id,
        'txn_date' => now()->toDateString(),
        'post_ts' => now(),
        'source_type' => 'BILL',
        'source_id' => $this->bill->bill_id,
        'source_line_no' => 1,
        'description' => 'Water Bill - Test Period',
        'debit' => 100.00,
        'credit' => 0,
        'user_id' => $this->user->id,
        'stat_id' => $this->activeStatusId,
    ]);

    $this->paymentService = app(PaymentService::class);
});

// Helper to create a penalty charge linked to the bill's period
function createPenaltyCharge($test, float $amount): CustomerCharge
{
    $chargeItem = ChargeItem::firstOrCreate(
        ['ci_code' => 'LATE_PENALTY'],
        [
            'ci_desc' => 'Late Payment Penalty',
            'ci_default_amount' => 0,
            'stat_id' => $test->activeStatusId,
        ]
    );

    $charge = CustomerCharge::create([
        'customer_id' => $test->customer->cust_id,
        'connection_id' => $test->connection->connection_id,
        'charge_item_id' => $chargeItem->charge_item_id,
        'description' => "Late Payment Penalty (10%) - Test Period (Bill #{$test->bill->bill_id})",
        'quantity' => 1,
        'unit_amount' => $amount,
        'due_date' => now()->addDays(7),
        'stat_id' => $test->activeStatusId,
    ]);

    CustomerLedger::create([
        'customer_id' => $test->customer->cust_id,
        'connection_id' => $test->connection->connection_id,
        'period_id' => $test->period->per_id,
        'txn_date' => now()->toDateString(),
        'post_ts' => now(),
        'source_type' => 'CHARGE',
        'source_id' => $charge->charge_id,
        'source_line_no' => 1,
        'description' => $charge->description,
        'debit' => $amount,
        'credit' => 0,
        'user_id' => $test->user->id,
        'stat_id' => $test->activeStatusId,
    ]);

    return $charge;
}

// --- Test: Partial payment with penalty precedence ---
it('allocates to penalty first, then bill on partial payment', function () {
    $penalty = createPenaltyCharge($this, 50.00);
    // Total due = 100 (bill) + 50 (penalty) = 150

    $result = $this->paymentService->processWaterBillPayment(
        $this->bill->bill_id,
        80.00,
        $this->user->id
    );

    // Penalty should be fully paid (50), bill gets remainder (30)
    expect($result['total_paid'])->toBe(80.0);
    expect($result['remaining_balance'])->toBe(70.0);
    expect($result['change'])->toBe(0.0);

    // Verify penalty is fully paid
    $penalty->refresh();
    expect($penalty->stat_id)->toBe($this->paidStatusId);
    expect($penalty->remaining_amount)->toBe(0.0);

    // Verify bill is NOT paid (only 30 of 100 applied)
    $this->bill->refresh();
    expect($this->bill->stat_id)->toBe($this->activeStatusId);
    expect($this->bill->paid_amount)->toBe(30.0);
    expect($this->bill->remaining_amount)->toBe(70.0);

    // Verify allocations
    $allocations = PaymentAllocation::where('payment_id', $result['payment']->payment_id)->get();
    expect($allocations)->toHaveCount(2);

    $chargeAlloc = $allocations->firstWhere('target_type', 'CHARGE');
    $billAlloc = $allocations->firstWhere('target_type', 'BILL');

    expect((float) $chargeAlloc->amount_applied)->toBe(50.0);
    expect((float) $billAlloc->amount_applied)->toBe(30.0);
});

// --- Test: Full payment still works ---
it('processes full payment correctly with penalty precedence', function () {
    $penalty = createPenaltyCharge($this, 50.00);

    $result = $this->paymentService->processWaterBillPayment(
        $this->bill->bill_id,
        150.00,
        $this->user->id
    );

    expect($result['total_paid'])->toBe(150.0);
    expect($result['remaining_balance'])->toBe(0.0);
    expect($result['change'])->toBe(0.0);

    $penalty->refresh();
    expect($penalty->stat_id)->toBe($this->paidStatusId);

    $this->bill->refresh();
    expect($this->bill->stat_id)->toBe($this->paidStatusId);
});

// --- Test: Overpayment returns change ---
it('returns correct change on overpayment', function () {
    $penalty = createPenaltyCharge($this, 50.00);

    $result = $this->paymentService->processWaterBillPayment(
        $this->bill->bill_id,
        200.00,
        $this->user->id
    );

    expect($result['total_paid'])->toBe(150.0);
    expect($result['change'])->toBe(50.0);
    expect($result['remaining_balance'])->toBe(0.0);
});

// --- Test: Multiple partial payments until paid ---
it('allows multiple partial payments until bill is fully paid', function () {
    // First partial payment: 60 of 100
    $result1 = $this->paymentService->processWaterBillPayment(
        $this->bill->bill_id,
        60.00,
        $this->user->id
    );

    expect($result1['total_paid'])->toBe(60.0);
    expect($result1['remaining_balance'])->toBe(40.0);

    $this->bill->refresh();
    expect($this->bill->stat_id)->toBe($this->activeStatusId);
    expect($this->bill->remaining_amount)->toBe(40.0);

    // Second partial payment: 40 of 40 remaining → fully paid
    $result2 = $this->paymentService->processWaterBillPayment(
        $this->bill->bill_id,
        40.00,
        $this->user->id
    );

    expect($result2['total_paid'])->toBe(40.0);
    expect($result2['remaining_balance'])->toBe(0.0);

    $this->bill->refresh();
    expect($this->bill->stat_id)->toBe($this->paidStatusId);
});

// --- Test: Bill-only partial payment (no penalty) ---
it('handles partial payment on bill with no charges', function () {
    $result = $this->paymentService->processWaterBillPayment(
        $this->bill->bill_id,
        40.00,
        $this->user->id
    );

    expect($result['total_paid'])->toBe(40.0);
    expect($result['remaining_balance'])->toBe(60.0);
    expect($result['change'])->toBe(0.0);

    $this->bill->refresh();
    expect($this->bill->stat_id)->toBe($this->activeStatusId);
    expect($this->bill->remaining_amount)->toBe(60.0);
});

// --- Test: Partial payment only covers part of penalty ---
it('partially covers penalty when payment is less than penalty amount', function () {
    $penalty = createPenaltyCharge($this, 50.00);

    $result = $this->paymentService->processWaterBillPayment(
        $this->bill->bill_id,
        30.00,
        $this->user->id
    );

    // Only 30 applied to penalty, nothing to bill
    expect($result['total_paid'])->toBe(30.0);
    expect($result['remaining_balance'])->toBe(120.0);

    $penalty->refresh();
    expect($penalty->stat_id)->toBe($this->activeStatusId); // Not fully paid
    expect($penalty->remaining_amount)->toBe(20.0);

    $this->bill->refresh();
    expect($this->bill->stat_id)->toBe($this->activeStatusId);
    expect($this->bill->remaining_amount)->toBe(100.0); // No payment reached the bill

    // Verify only one allocation (to charge, not bill)
    $allocations = PaymentAllocation::where('payment_id', $result['payment']->payment_id)->get();
    expect($allocations)->toHaveCount(1);
    expect($allocations->first()->target_type)->toBe('CHARGE');
});

// --- Test: WaterBillHistory model accessors ---
it('correctly computes paid_amount and remaining_amount on WaterBillHistory', function () {
    expect($this->bill->total_amount)->toBe(100.0);
    expect($this->bill->paid_amount)->toBe(0.0);
    expect($this->bill->remaining_amount)->toBe(100.0);
    expect($this->bill->isPaid())->toBeFalse();
    expect($this->bill->isPartiallyPaid())->toBeFalse();

    // Create a partial allocation
    PaymentAllocation::create([
        'payment_id' => Payment::create([
            'receipt_no' => 'OR-2026-99999',
            'payer_id' => $this->customer->cust_id,
            'payment_date' => now()->toDateString(),
            'amount_received' => 40.00,
            'user_id' => $this->user->id,
            'stat_id' => $this->activeStatusId,
        ])->payment_id,
        'target_type' => 'BILL',
        'target_id' => $this->bill->bill_id,
        'amount_applied' => 40.00,
        'period_id' => $this->period->per_id,
        'connection_id' => $this->connection->connection_id,
    ]);

    // Refresh to re-compute accessors
    $this->bill->refresh();
    expect($this->bill->paid_amount)->toBe(40.0);
    expect($this->bill->remaining_amount)->toBe(60.0);
    expect($this->bill->isPaid())->toBeFalse();
    expect($this->bill->isPartiallyPaid())->toBeTrue();
});
