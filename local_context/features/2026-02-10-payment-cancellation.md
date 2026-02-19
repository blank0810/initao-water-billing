# Payment Cancellation Feature — Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Allow ADMIN/SUPER_ADMIN staff to cancel a payment, reversing all ledger entries and making the associated bills/charges available for payment again.

**Architecture:** Full payment cancellation with hybrid ledger approach — mark existing PAYMENT ledger entries as CANCELLED, create new REVERSAL DEBIT entries to maintain balance integrity. Payment model gets cancellation metadata fields. Cashier daily reports show cancelled items with visual indicator and adjusted totals.

**Tech Stack:** Laravel 12, PHP 8.2+, MySQL 8, Pest PHP, Alpine.js, Tailwind CSS 3, Flowbite

---

## Key Design Decisions (from brainstorming)

| Decision | Choice |
|----------|--------|
| Ledger approach | Hybrid: Mark existing as CANCELLED + create REVERSAL DEBIT entries |
| Cashier report | Show cancelled items with strikethrough, adjusted totals |
| Period closure | Allow cancellation regardless of period status |
| Authorization | ADMIN + SUPER_ADMIN only (existing `PAYMENTS_VOID` permission) |
| Reason field | Required (mandatory justification) |
| UI placement | Customer ledger view + Cashier transactions tab |
| Scope | Full cancellation only (all allocations reversed) |

## Reference Files

| File | Purpose | Key Lines |
|------|---------|-----------|
| `app/Models/Payment.php` | Payment model | fillable:19-26, relations:36-59 |
| `app/Models/PaymentAllocation.php` | Allocation model | fillable:19-26, no stat_id yet |
| `app/Models/CustomerLedger.php` | Ledger model | fillable:19-33, source_type enum |
| `app/Models/CustomerCharge.php` | Charge model | getPaidAmountAttribute:111-114 |
| `app/Models/WaterBillHistory.php` | Bill model | fillable:22-36 |
| `app/Models/Status.php` | Status constants | CANCELLED:36 |
| `app/Models/Permission.php` | Permissions | PAYMENTS_VOID:52 |
| `app/Services/Payment/PaymentService.php` | Payment processing | processWaterBillPayment:332-444 |
| `app/Services/Ledger/LedgerService.php` | Ledger entries | recordPaymentAllocation:54-76 |
| `app/Services/Payment/PaymentManagementService.php` | Cashier transactions | getCashierTransactions:285-337, getStatistics:221-268 |
| `app/Http/Controllers/Payment/PaymentController.php` | Controller | constructor:18-22 |
| `app/Services/Billing/BillAdjustmentService.php` | Void pattern reference | voidAdjustment:334-428 |
| `routes/web.php` | Payment routes | payments.view:193-205, payments.process:208-220 |
| `resources/views/pages/payment/partials/my-transactions-tab.blade.php` | Cashier TX UI | table:137-187, actions:169-183 |
| `resources/views/pages/customer/tabs/ledger-tab.blade.php` | Ledger UI | table:55-76, actions col:65 |

---

## Task 1: Database Migration — Add Cancellation Columns

**Files:**
- Create: `database/migrations/YYYY_MM_DD_HHMMSS_add_cancellation_fields_to_payment_tables.php`

**Step 1: Create migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add cancellation metadata to Payment table
        Schema::table('Payment', function (Blueprint $table) {
            $table->timestamp('cancelled_at')->nullable()->after('stat_id');
            $table->unsignedBigInteger('cancelled_by')->nullable()->after('cancelled_at');
            $table->text('cancellation_reason')->nullable()->after('cancelled_by');

            $table->foreign('cancelled_by')->references('id')->on('users')->nullOnDelete();
            $table->index('cancelled_at', 'payment_cancelled_at_index');
        });

        // Add stat_id to PaymentAllocation table
        Schema::table('PaymentAllocation', function (Blueprint $table) {
            $activeStatusId = \App\Models\Status::getIdByDescription(\App\Models\Status::ACTIVE);

            $table->unsignedBigInteger('stat_id')->default($activeStatusId)->after('connection_id');

            $table->foreign('stat_id')->references('stat_id')->on('statuses');
        });
    }

    public function down(): void
    {
        Schema::table('Payment', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);
            $table->dropIndex('payment_cancelled_at_index');
            $table->dropColumn(['cancelled_at', 'cancelled_by', 'cancellation_reason']);
        });

        Schema::table('PaymentAllocation', function (Blueprint $table) {
            $table->dropForeign(['stat_id']);
            $table->dropColumn('stat_id');
        });
    }
};
```

**Step 2: Run migration**

```bash
php artisan migrate
```

Expected: Migration runs successfully, adds 3 columns to Payment and 1 column to PaymentAllocation.

**Step 3: Commit**

```bash
git add database/migrations/*_add_cancellation_fields_to_payment_tables.php
git commit -m "feat(payment): add cancellation fields to Payment and PaymentAllocation tables"
```

---

## Task 2: Update Payment and PaymentAllocation Models

**Files:**
- Modify: `app/Models/Payment.php` (fillable, casts, relationships)
- Modify: `app/Models/PaymentAllocation.php` (fillable, casts, relationship)

**Step 1: Update Payment model**

In `app/Models/Payment.php`:

Add to `$fillable` array (line 19-26): add `'cancelled_at'`, `'cancelled_by'`, `'cancellation_reason'`

```php
protected $fillable = [
    'receipt_no',
    'payer_id',
    'payment_date',
    'amount_received',
    'user_id',
    'stat_id',
    'cancelled_at',
    'cancelled_by',
    'cancellation_reason',
];
```

Add to `$casts` array (line 28-31): add cancelled_at cast

```php
protected $casts = [
    'payment_date' => 'date',
    'amount_received' => 'decimal:2',
    'cancelled_at' => 'datetime',
];
```

Add new relationship after `paymentAllocations()` (after line 59):

```php
/**
 * Get the user who cancelled this payment
 */
public function cancelledBy()
{
    return $this->belongsTo(User::class, 'cancelled_by', 'id');
}
```

**Step 2: Update PaymentAllocation model**

In `app/Models/PaymentAllocation.php`:

Add `'stat_id'` to `$fillable` array (line 19-26):

```php
protected $fillable = [
    'payment_id',
    'target_type',
    'target_id',
    'amount_applied',
    'period_id',
    'connection_id',
    'stat_id',
];
```

Add status relationship after `serviceConnection()` (after line 50):

```php
/**
 * Get the status of the allocation
 */
public function status()
{
    return $this->belongsTo(Status::class, 'stat_id', 'stat_id');
}
```

**Step 3: Commit**

```bash
git add app/Models/Payment.php app/Models/PaymentAllocation.php
git commit -m "feat(payment): add cancellation fields and relationships to Payment and PaymentAllocation models"
```

---

## Task 3: Update CustomerCharge — Filter Cancelled Allocations

**Files:**
- Modify: `app/Models/CustomerCharge.php:111-114` (getPaidAmountAttribute)

**Step 1: Update getPaidAmountAttribute**

In `app/Models/CustomerCharge.php`, replace `getPaidAmountAttribute()` at line 111-114:

```php
/**
 * Accessor for paid amount (sum of active allocations only)
 */
public function getPaidAmountAttribute(): float
{
    $cancelledStatusId = Status::getIdByDescription(Status::CANCELLED);

    $query = $this->paymentAllocations();

    if ($cancelledStatusId) {
        $query->where('stat_id', '!=', $cancelledStatusId);
    }

    return (float) $query->sum('amount_applied');
}
```

**Step 2: Commit**

```bash
git add app/Models/CustomerCharge.php
git commit -m "fix(charge): exclude cancelled allocations from paid amount calculation"
```

---

## Task 4: Add `recordPaymentReversal()` to LedgerService

**Files:**
- Modify: `app/Services/Ledger/LedgerService.php` (add new method after line 76)

**Step 1: Add recordPaymentReversal method**

Add after `recordPaymentAllocation()` (after line 76):

```php
/**
 * Record a payment reversal as a DEBIT entry in the ledger
 *
 * Called when a payment is cancelled. Creates a REVERSAL entry
 * that offsets the original PAYMENT CREDIT entry.
 */
public function recordPaymentReversal(
    PaymentAllocation $allocation,
    Payment $payment,
    string $description,
    int $userId
): CustomerLedger {
    $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

    return CustomerLedger::create([
        'customer_id' => $payment->payer_id,
        'connection_id' => $allocation->connection_id,
        'period_id' => $allocation->period_id,
        'txn_date' => now()->toDateString(),
        'post_ts' => now(),
        'source_type' => 'REVERSAL',
        'source_id' => $payment->payment_id,
        'source_line_no' => $allocation->payment_allocation_id,
        'description' => $description,
        'debit' => $allocation->amount_applied,
        'credit' => 0,
        'user_id' => $userId,
        'stat_id' => $activeStatusId,
    ]);
}
```

**Step 2: Commit**

```bash
git add app/Services/Ledger/LedgerService.php
git commit -m "feat(ledger): add recordPaymentReversal method for payment cancellation"
```

---

## Task 5: Add `cancelPayment()` to PaymentService

**Files:**
- Modify: `app/Services/Payment/PaymentService.php` (add new method at end of class, before closing `}`)

**Step 1: Add cancelPayment method**

Add at the end of the `PaymentService` class (before final `}`):

```php
/**
 * Cancel a payment and reverse all ledger entries
 *
 * - Marks Payment as CANCELLED with metadata
 * - Marks all PaymentAllocations as CANCELLED
 * - Marks original PAYMENT ledger entries as CANCELLED
 * - Creates REVERSAL DEBIT entries in CustomerLedger
 * - Reverts bill/charge statuses to ACTIVE or OVERDUE
 *
 * @param  int  $paymentId  The payment to cancel
 * @param  string  $reason  Required cancellation reason
 * @param  int  $userId  The admin/staff performing the cancellation
 * @return array Contains 'success', 'message', 'data'
 *
 * @throws \Exception
 */
public function cancelPayment(int $paymentId, string $reason, int $userId): array
{
    $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
    $cancelledStatusId = Status::getIdByDescription(Status::CANCELLED);
    $overdueStatusId = Status::getIdByDescription(Status::OVERDUE);

    return DB::transaction(function () use (
        $paymentId, $reason, $userId,
        $activeStatusId, $cancelledStatusId, $overdueStatusId
    ) {
        // Lock the payment to prevent concurrent operations
        $payment = Payment::with('paymentAllocations')
            ->where('payment_id', $paymentId)
            ->lockForUpdate()
            ->first();

        if (! $payment) {
            throw new \Exception('Payment not found.');
        }

        if ($payment->stat_id === $cancelledStatusId) {
            throw new \Exception('Payment is already cancelled.');
        }

        if ($payment->stat_id !== $activeStatusId) {
            throw new \Exception('Only active payments can be cancelled.');
        }

        // 1. Mark Payment as CANCELLED
        $payment->update([
            'stat_id' => $cancelledStatusId,
            'cancelled_at' => now(),
            'cancelled_by' => $userId,
            'cancellation_reason' => $reason,
        ]);

        // 2. Process each allocation
        foreach ($payment->paymentAllocations as $allocation) {
            // Mark allocation as CANCELLED
            $allocation->update(['stat_id' => $cancelledStatusId]);

            // Find and mark original PAYMENT ledger entry as CANCELLED
            CustomerLedger::where('source_type', 'PAYMENT')
                ->where('source_id', $payment->payment_id)
                ->where('source_line_no', $allocation->payment_allocation_id)
                ->update(['stat_id' => $cancelledStatusId]);

            // Create REVERSAL DEBIT entry
            $description = 'CANCELLED: '.$this->getReversalDescription($allocation);
            if ($reason) {
                $description .= ' (Reason: '.$reason.')';
            }

            $this->ledgerService->recordPaymentReversal(
                $allocation,
                $payment,
                $description,
                $userId
            );

            // Revert target bill/charge status
            $this->revertTargetStatus($allocation, $activeStatusId, $overdueStatusId);
        }

        // 3. Handle application payment link (if this was an application payment)
        $this->revertApplicationPayment($payment, $cancelledStatusId, $activeStatusId);

        return [
            'success' => true,
            'message' => 'Payment cancelled successfully.',
            'data' => [
                'payment_id' => $paymentId,
                'receipt_no' => $payment->receipt_no,
                'amount' => $payment->amount_received,
                'allocations_reversed' => $payment->paymentAllocations->count(),
                'cancelled_by' => $userId,
                'cancelled_at' => $payment->cancelled_at,
            ],
        ];
    });
}

/**
 * Build reversal description from allocation target
 */
protected function getReversalDescription(PaymentAllocation $allocation): string
{
    if ($allocation->target_type === 'BILL') {
        $bill = WaterBillHistory::with('period')->find($allocation->target_id);

        return 'Water Bill - '.($bill?->period?->per_name ?? 'Unknown Period');
    }

    if ($allocation->target_type === 'CHARGE') {
        $charge = CustomerCharge::find($allocation->target_id);

        return $charge?->description ?? 'Charge';
    }

    return 'Payment';
}

/**
 * Revert bill or charge status after cancellation
 */
protected function revertTargetStatus(
    PaymentAllocation $allocation,
    int $activeStatusId,
    int $overdueStatusId
): void {
    if ($allocation->target_type === 'BILL') {
        $bill = WaterBillHistory::find($allocation->target_id);
        if ($bill) {
            // Determine if bill should be OVERDUE based on due_date
            $newStatusId = ($bill->due_date && $bill->due_date->isPast())
                ? $overdueStatusId
                : $activeStatusId;
            $bill->update(['stat_id' => $newStatusId]);
        }
    } elseif ($allocation->target_type === 'CHARGE') {
        CustomerCharge::where('charge_id', $allocation->target_id)
            ->update(['stat_id' => $activeStatusId]);
    }
}

/**
 * If this payment was linked to a ServiceApplication, revert the application status
 */
protected function revertApplicationPayment(
    Payment $payment,
    int $cancelledStatusId,
    int $activeStatusId
): void {
    $application = ServiceApplication::where('payment_id', $payment->payment_id)->first();

    if ($application) {
        // Revert application from PAID back to VERIFIED
        $verifiedStatusId = Status::getIdByDescription(Status::VERIFIED);
        $application->update([
            'stat_id' => $verifiedStatusId,
            'paid_at' => null,
            'payment_id' => null,
        ]);

        // Revert charges back to ACTIVE
        CustomerCharge::where('application_id', $application->application_id)
            ->where('stat_id', Status::getIdByDescription(Status::PAID))
            ->update(['stat_id' => $activeStatusId]);
    }
}
```

**Step 2: Add the ServiceApplication import at the top of the file**

Add to imports (around line 9): `use App\Models\ServiceApplication;`

Note: `ServiceApplication` is already imported at line 9. Verify this before adding.

**Step 3: Commit**

```bash
git add app/Services/Payment/PaymentService.php
git commit -m "feat(payment): add cancelPayment method with full ledger reversal and status reversion"
```

---

## Task 6: Write Tests for cancelPayment

**Files:**
- Create: `tests/Feature/Services/Payment/PaymentCancellationTest.php`

**Step 1: Write the test file**

```php
<?php

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
    $this->paymentService = app(PaymentService::class);
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('cancels an active payment and marks it as cancelled', function () {
    $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
    $cancelledStatusId = Status::getIdByDescription(Status::CANCELLED);

    $customer = Customer::factory()->create();
    $payment = Payment::create([
        'receipt_no' => 'OR-2026-00001',
        'payer_id' => $customer->cust_id,
        'payment_date' => now()->toDateString(),
        'amount_received' => 500.00,
        'user_id' => $this->user->id,
        'stat_id' => $activeStatusId,
    ]);

    $result = $this->paymentService->cancelPayment(
        $payment->payment_id,
        'Customer paid wrong billing period',
        $this->user->id
    );

    expect($result['success'])->toBeTrue();

    $payment->refresh();
    expect($payment->stat_id)->toBe($cancelledStatusId);
    expect($payment->cancelled_by)->toBe($this->user->id);
    expect($payment->cancellation_reason)->toBe('Customer paid wrong billing period');
    expect($payment->cancelled_at)->not->toBeNull();
});

it('throws exception when cancelling already cancelled payment', function () {
    $cancelledStatusId = Status::getIdByDescription(Status::CANCELLED);

    $customer = Customer::factory()->create();
    $payment = Payment::create([
        'receipt_no' => 'OR-2026-00002',
        'payer_id' => $customer->cust_id,
        'payment_date' => now()->toDateString(),
        'amount_received' => 500.00,
        'user_id' => $this->user->id,
        'stat_id' => $cancelledStatusId,
    ]);

    $this->paymentService->cancelPayment(
        $payment->payment_id,
        'Duplicate cancellation',
        $this->user->id
    );
})->throws(\Exception::class, 'Payment is already cancelled.');

it('creates reversal debit entries in customer ledger', function () {
    $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

    $customer = Customer::factory()->create();
    $connection = ServiceConnection::factory()->create();
    $period = Period::factory()->create();

    $bill = WaterBillHistory::create([
        'connection_id' => $connection->connection_id,
        'period_id' => $period->per_id,
        'consumption' => 10.000,
        'water_amount' => 300.00,
        'due_date' => now()->addDays(15),
        'adjustment_total' => 0,
        'stat_id' => Status::getIdByDescription(Status::PAID),
    ]);

    $payment = Payment::create([
        'receipt_no' => 'OR-2026-00003',
        'payer_id' => $customer->cust_id,
        'payment_date' => now()->toDateString(),
        'amount_received' => 300.00,
        'user_id' => $this->user->id,
        'stat_id' => $activeStatusId,
    ]);

    $allocation = PaymentAllocation::create([
        'payment_id' => $payment->payment_id,
        'target_type' => 'BILL',
        'target_id' => $bill->bill_id,
        'amount_applied' => 300.00,
        'period_id' => $period->per_id,
        'connection_id' => $connection->connection_id,
        'stat_id' => $activeStatusId,
    ]);

    // Create original PAYMENT ledger CREDIT entry
    CustomerLedger::create([
        'customer_id' => $customer->cust_id,
        'connection_id' => $connection->connection_id,
        'period_id' => $period->per_id,
        'txn_date' => now()->toDateString(),
        'post_ts' => now(),
        'source_type' => 'PAYMENT',
        'source_id' => $payment->payment_id,
        'source_line_no' => $allocation->payment_allocation_id,
        'description' => 'Payment for Water Bill',
        'debit' => 0,
        'credit' => 300.00,
        'user_id' => $this->user->id,
        'stat_id' => $activeStatusId,
    ]);

    $this->paymentService->cancelPayment(
        $payment->payment_id,
        'Wrong period',
        $this->user->id
    );

    // Check reversal entry was created
    $reversalEntry = CustomerLedger::where('source_type', 'REVERSAL')
        ->where('source_id', $payment->payment_id)
        ->first();

    expect($reversalEntry)->not->toBeNull();
    expect((float) $reversalEntry->debit)->toBe(300.00);
    expect((float) $reversalEntry->credit)->toBe(0.00);
    expect($reversalEntry->stat_id)->toBe($activeStatusId);

    // Check original PAYMENT entry was marked CANCELLED
    $cancelledStatusId = Status::getIdByDescription(Status::CANCELLED);
    $originalEntry = CustomerLedger::where('source_type', 'PAYMENT')
        ->where('source_id', $payment->payment_id)
        ->first();

    expect($originalEntry->stat_id)->toBe($cancelledStatusId);
});

it('reverts bill status from PAID back to ACTIVE or OVERDUE', function () {
    $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
    $paidStatusId = Status::getIdByDescription(Status::PAID);
    $overdueStatusId = Status::getIdByDescription(Status::OVERDUE);

    $customer = Customer::factory()->create();
    $connection = ServiceConnection::factory()->create();
    $period = Period::factory()->create();

    // Bill with future due_date → should revert to ACTIVE
    $futureBill = WaterBillHistory::create([
        'connection_id' => $connection->connection_id,
        'period_id' => $period->per_id,
        'consumption' => 10.000,
        'water_amount' => 300.00,
        'due_date' => now()->addDays(15),
        'adjustment_total' => 0,
        'stat_id' => $paidStatusId,
    ]);

    $payment = Payment::create([
        'receipt_no' => 'OR-2026-00004',
        'payer_id' => $customer->cust_id,
        'payment_date' => now()->toDateString(),
        'amount_received' => 300.00,
        'user_id' => $this->user->id,
        'stat_id' => $activeStatusId,
    ]);

    PaymentAllocation::create([
        'payment_id' => $payment->payment_id,
        'target_type' => 'BILL',
        'target_id' => $futureBill->bill_id,
        'amount_applied' => 300.00,
        'period_id' => $period->per_id,
        'connection_id' => $connection->connection_id,
        'stat_id' => $activeStatusId,
    ]);

    $this->paymentService->cancelPayment(
        $payment->payment_id,
        'Test revert',
        $this->user->id
    );

    $futureBill->refresh();
    expect($futureBill->stat_id)->toBe($activeStatusId);
});

it('marks allocations as cancelled', function () {
    $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
    $cancelledStatusId = Status::getIdByDescription(Status::CANCELLED);

    $customer = Customer::factory()->create();
    $connection = ServiceConnection::factory()->create();
    $period = Period::factory()->create();

    $bill = WaterBillHistory::create([
        'connection_id' => $connection->connection_id,
        'period_id' => $period->per_id,
        'consumption' => 10.000,
        'water_amount' => 200.00,
        'due_date' => now()->addDays(15),
        'adjustment_total' => 0,
        'stat_id' => Status::getIdByDescription(Status::PAID),
    ]);

    $payment = Payment::create([
        'receipt_no' => 'OR-2026-00005',
        'payer_id' => $customer->cust_id,
        'payment_date' => now()->toDateString(),
        'amount_received' => 200.00,
        'user_id' => $this->user->id,
        'stat_id' => $activeStatusId,
    ]);

    $allocation = PaymentAllocation::create([
        'payment_id' => $payment->payment_id,
        'target_type' => 'BILL',
        'target_id' => $bill->bill_id,
        'amount_applied' => 200.00,
        'period_id' => $period->per_id,
        'connection_id' => $connection->connection_id,
        'stat_id' => $activeStatusId,
    ]);

    $this->paymentService->cancelPayment(
        $payment->payment_id,
        'Test allocation cancel',
        $this->user->id
    );

    $allocation->refresh();
    expect($allocation->stat_id)->toBe($cancelledStatusId);
});
```

**Step 2: Run tests to verify they pass**

```bash
php artisan test --filter=PaymentCancellationTest
```

Expected: All tests pass. If any factories are missing (Customer, ServiceConnection, Period), they may need to be created or the tests adjusted to use direct DB inserts.

**Step 3: Commit**

```bash
git add tests/Feature/Services/Payment/PaymentCancellationTest.php
git commit -m "test(payment): add payment cancellation service tests"
```

---

## Task 7: Add Controller Method and Route

**Files:**
- Modify: `app/Http/Controllers/Payment/PaymentController.php` (add `cancelPayment` method)
- Modify: `routes/web.php` (add route)

**Step 1: Add controller method**

Add to `PaymentController` (after `exportMyTransactionsPdf` method, before closing `}`):

```php
/**
 * Cancel a payment (ADMIN/SUPER_ADMIN only)
 */
public function cancelPayment(int $paymentId, Request $request): JsonResponse
{
    $request->validate([
        'reason' => 'required|string|max:500',
    ]);

    try {
        $result = $this->paymentService->cancelPayment(
            $paymentId,
            $request->input('reason'),
            auth()->id()
        );

        return response()->json($result);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 422);
    }
}
```

**Step 2: Add route**

In `routes/web.php`, add a new route group after the `payments.process` group (after line 220):

```php
// Payment Cancellation - Void (payments.void permission)
Route::middleware(['permission:payments.void'])->group(function () {
    Route::post('/payment/{id}/cancel', [PaymentController::class, 'cancelPayment'])->name('payment.cancel');
});
```

**Step 3: Commit**

```bash
git add app/Http/Controllers/Payment/PaymentController.php routes/web.php
git commit -m "feat(payment): add cancel payment controller method and route"
```

---

## Task 8: Update getCashierTransactions to Show Cancelled Payments

**Files:**
- Modify: `app/Services/Payment/PaymentManagementService.php`

**Step 1: Update getCashierTransactions method**

Replace the `getCashierTransactions` method (lines 285-337) with updated version that:
- Includes cancelled payments in the transaction list
- Adds `is_cancelled` flag and cancellation info to each transaction
- Adjusts summary totals to exclude cancelled payments
- Adds cancellation summary

```php
/**
 * Get transactions processed by a specific cashier for a given date
 * Includes cancelled payments with visual distinction
 */
public function getCashierTransactions(int $userId, ?string $date = null): array
{
    $targetDate = $date ? \Carbon\Carbon::parse($date) : today();
    $cancelledStatusId = Status::getIdByDescription(Status::CANCELLED);

    $payments = Payment::with(['payer', 'status', 'cancelledBy'])
        ->where('user_id', $userId)
        ->whereDate('payment_date', $targetDate)
        ->orderBy('created_at', 'desc')
        ->get();

    // Pre-fetch all application payment IDs in one query to avoid N+1
    $applicationPaymentIds = ServiceApplication::whereIn(
        'payment_id',
        $payments->pluck('payment_id')
    )->pluck('payment_id')->flip();

    // Split active and cancelled payments for summary
    $activePayments = $payments->filter(fn ($p) => $p->stat_id !== $cancelledStatusId);
    $cancelledPayments = $payments->filter(fn ($p) => $p->stat_id === $cancelledStatusId);

    // Calculate summary statistics (exclude cancelled)
    $totalCollected = $activePayments->sum('amount_received');
    $transactionCount = $payments->count();
    $cancelledAmount = $cancelledPayments->sum('amount_received');
    $cancelledCount = $cancelledPayments->count();

    // Group by payment type (active only)
    $byType = $this->groupPaymentsByType($activePayments, $applicationPaymentIds);

    // Format transactions for display
    $transactions = $payments->map(function ($payment) use ($applicationPaymentIds, $cancelledStatusId) {
        $paymentType = $this->getPaymentType($payment, $applicationPaymentIds);
        $isCancelled = $payment->stat_id === $cancelledStatusId;

        $tx = [
            'payment_id' => $payment->payment_id,
            'receipt_no' => $payment->receipt_no,
            'customer_name' => $this->formatCustomerName($payment->payer),
            'customer_code' => $payment->payer->resolution_no ?? '-',
            'payment_type' => $paymentType,
            'payment_type_label' => $this->getPaymentTypeLabelFromType($paymentType),
            'amount' => $payment->amount_received,
            'amount_formatted' => '₱ '.number_format($payment->amount_received, 2),
            'time' => $payment->created_at->format('g:i A'),
            'receipt_url' => route('payment.receipt', $payment->payment_id),
            'is_cancelled' => $isCancelled,
            'status' => $isCancelled ? 'CANCELLED' : 'ACTIVE',
        ];

        if ($isCancelled) {
            $tx['cancelled_at'] = $payment->cancelled_at?->format('M d, Y g:i A');
            $tx['cancelled_by_name'] = $payment->cancelledBy?->name ?? 'Unknown';
            $tx['cancellation_reason'] = $payment->cancellation_reason;
        }

        return $tx;
    });

    return [
        'date' => $targetDate->format('Y-m-d'),
        'date_display' => $targetDate->format('F j, Y'),
        'summary' => [
            'total_collected' => $totalCollected,
            'total_collected_formatted' => '₱ '.number_format($totalCollected, 2),
            'transaction_count' => $transactionCount,
            'cancelled_amount' => $cancelledAmount,
            'cancelled_amount_formatted' => '₱ '.number_format($cancelledAmount, 2),
            'cancelled_count' => $cancelledCount,
            'by_type' => $byType,
        ],
        'transactions' => $transactions,
    ];
}
```

**Step 2: Add ServiceApplication import if not present**

Check top of file — `ServiceApplication` is already imported at line 8.

**Step 3: Update getStatistics to exclude cancelled payments**

In the `getStatistics` method (lines 221-268), update the today/month collection queries to exclude cancelled payments:

Replace lines 248-256:

```php
// Today's collections (exclude cancelled)
$cancelledStatusId = Status::getIdByDescription(Status::CANCELLED);
$todayPayments = Payment::whereDate('payment_date', today())
    ->where('stat_id', '!=', $cancelledStatusId)
    ->get();
$todayCollection = $todayPayments->sum('amount_received');
$todayCount = $todayPayments->count();

// This month's collections (exclude cancelled)
$monthPayments = Payment::whereMonth('payment_date', now()->month)
    ->whereYear('payment_date', now()->year)
    ->where('stat_id', '!=', $cancelledStatusId)
    ->get();
$monthCollection = $monthPayments->sum('amount_received');
```

**Step 4: Commit**

```bash
git add app/Services/Payment/PaymentManagementService.php
git commit -m "feat(payment): update cashier transactions and statistics to handle cancelled payments"
```

---

## Task 9: Update My Transactions Tab UI — Show Cancelled Items

**Files:**
- Modify: `resources/views/pages/payment/partials/my-transactions-tab.blade.php`

**Step 1: Update summary cards to show cancellation info**

After the "Breakdown by Type" card (after line 103), add a cancellation summary that only shows when there are cancelled items. Replace the entire grid (lines 54-103) with:

Keep existing 3 cards, but update the "Total Collected" card to show net amount, and add conditional cancelled info inside the breakdown card.

In the "Total Collected" card (line 63), change the displayed value to show net:

```html
<p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="myTransactions.summary?.total_collected_formatted || '₱ 0.00'"></p>
<template x-if="myTransactions.summary?.cancelled_count > 0">
    <p class="text-xs text-red-500 mt-1">
        <i class="fas fa-ban mr-1"></i>
        <span x-text="myTransactions.summary?.cancelled_count"></span> cancelled
        (<span x-text="myTransactions.summary?.cancelled_amount_formatted"></span>)
    </p>
</template>
```

**Step 2: Update transaction table rows to show cancelled styling**

Replace the table row template (lines 149-185) with:

```html
<template x-for="tx in filteredMyTransactions" :key="tx.payment_id">
    <tr class="transition-colors"
        :class="tx.is_cancelled ? 'bg-red-50/50 dark:bg-red-900/10 opacity-75' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50'">
        <td class="px-6 py-4">
            <span class="text-sm font-mono font-medium"
                  :class="tx.is_cancelled ? 'text-gray-400 dark:text-gray-500 line-through' : 'text-gray-900 dark:text-white'"
                  x-text="tx.receipt_no"></span>
        </td>
        <td class="px-6 py-4">
            <p class="text-sm font-medium"
               :class="tx.is_cancelled ? 'text-gray-400 dark:text-gray-500 line-through' : 'text-gray-900 dark:text-white'"
               x-text="tx.customer_name"></p>
        </td>
        <td class="px-6 py-4">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :class="tx.is_cancelled
                          ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
                          : (tx.payment_type === 'APPLICATION_FEE'
                              ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'
                              : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400')"
                      x-text="tx.is_cancelled ? 'CANCELLED' : tx.payment_type_label">
                </span>
            </div>
        </td>
        <td class="px-6 py-4 text-right">
            <span class="text-sm font-bold"
                  :class="tx.is_cancelled ? 'text-red-400 dark:text-red-500 line-through' : 'text-gray-900 dark:text-white'"
                  x-text="tx.amount_formatted"></span>
        </td>
        <td class="px-6 py-4">
            <span class="text-sm text-gray-700 dark:text-gray-300" x-text="tx.time"></span>
        </td>
        <td class="px-6 py-4">
            <div class="flex items-center justify-center gap-2">
                <template x-if="!tx.is_cancelled">
                    <div class="flex items-center gap-2">
                        <button @click="viewTransaction(tx)"
                                class="p-2 text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors"
                                title="View Details">
                            <i class="fas fa-eye"></i>
                        </button>
                        <a :href="tx.receipt_url"
                           target="_blank"
                           class="p-2 text-gray-600 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400 transition-colors"
                           title="Print Receipt">
                            <i class="fas fa-print"></i>
                        </a>
                        @can('payments.void')
                        <button @click="openCancelPaymentModal(tx)"
                                class="p-2 text-gray-600 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors"
                                title="Cancel Payment">
                            <i class="fas fa-ban"></i>
                        </button>
                        @endcan
                    </div>
                </template>
                <template x-if="tx.is_cancelled">
                    <div class="text-xs text-red-500 dark:text-red-400 text-center">
                        <p x-text="'By: ' + tx.cancelled_by_name"></p>
                        <p x-text="tx.cancelled_at" class="text-gray-400"></p>
                    </div>
                </template>
            </div>
        </td>
    </tr>
</template>
```

**Step 3: Commit**

```bash
git add resources/views/pages/payment/partials/my-transactions-tab.blade.php
git commit -m "feat(ui): show cancelled payments with visual distinction in cashier transactions"
```

---

## Task 10: Create Cancellation Confirmation Modal

**Files:**
- Create: `resources/views/pages/payment/partials/cancel-payment-modal.blade.php`

**Step 1: Create the modal partial**

```html
<!-- Cancel Payment Confirmation Modal -->
<div x-show="showCancelPaymentModal"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50" @click="showCancelPaymentModal = false"></div>

    <!-- Modal -->
    <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md z-10"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform scale-95 opacity-0"
         x-transition:enter-end="transform scale-100 opacity-100">

        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-ban text-red-600 dark:text-red-400"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cancel Payment</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">This action cannot be undone</p>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="px-6 py-4 space-y-4">
            <!-- Payment Info -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Receipt #</span>
                    <span class="font-mono font-medium text-gray-900 dark:text-white" x-text="cancelPaymentData?.receipt_no"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Customer</span>
                    <span class="font-medium text-gray-900 dark:text-white" x-text="cancelPaymentData?.customer_name"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500 dark:text-gray-400">Amount</span>
                    <span class="font-bold text-red-600 dark:text-red-400" x-text="cancelPaymentData?.amount_formatted"></span>
                </div>
            </div>

            <!-- Reason Input -->
            <div>
                <label for="cancel-reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Reason for Cancellation <span class="text-red-500">*</span>
                </label>
                <textarea id="cancel-reason"
                          x-model="cancelPaymentReason"
                          rows="3"
                          maxlength="500"
                          placeholder="Explain why this payment is being cancelled..."
                          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-red-500 focus:border-red-500"
                          :class="cancelPaymentError ? 'border-red-500' : ''"></textarea>
                <div class="flex justify-between mt-1">
                    <p x-show="cancelPaymentError" class="text-xs text-red-500" x-text="cancelPaymentError"></p>
                    <p class="text-xs text-gray-400 ml-auto" x-text="(cancelPaymentReason?.length || 0) + '/500'"></p>
                </div>
            </div>

            <!-- Warning -->
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-3">
                <div class="flex gap-2">
                    <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                    <div class="text-xs text-amber-700 dark:text-amber-300">
                        <p class="font-medium mb-1">This will:</p>
                        <ul class="list-disc ml-4 space-y-0.5">
                            <li>Cancel this payment and all its allocations</li>
                            <li>Create reversal entries in the customer ledger</li>
                            <li>Make the associated bills/charges available for payment again</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
            <button @click="showCancelPaymentModal = false; cancelPaymentReason = ''; cancelPaymentError = '';"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                Keep Payment
            </button>
            <button @click="confirmCancelPayment()"
                    :disabled="cancelPaymentLoading"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition-colors flex items-center gap-2">
                <i x-show="cancelPaymentLoading" class="fas fa-spinner fa-spin"></i>
                <span x-text="cancelPaymentLoading ? 'Cancelling...' : 'Cancel Payment'"></span>
            </button>
        </div>
    </div>
</div>
```

**Step 2: Include the modal in the payment management page**

Find the payment management Blade view that includes `my-transactions-tab.blade.php`. It should be `resources/views/pages/payment/payment-management.blade.php`. Add the include for the cancel modal:

```blade
@include('pages.payment.partials.cancel-payment-modal')
```

**Step 3: Commit**

```bash
git add resources/views/pages/payment/partials/cancel-payment-modal.blade.php
git add resources/views/pages/payment/payment-management.blade.php
git commit -m "feat(ui): add payment cancellation confirmation modal"
```

---

## Task 11: Add Alpine.js Cancel Payment Logic

**Files:**
- Modify: The Alpine.js component that manages the payment management page (find the `x-data` block that contains `myTransactions`, `loadMyTransactions`, etc.)

**Step 1: Find the Alpine component**

Search for the Alpine.js `x-data` that contains `myTransactions` — it should be in `resources/views/pages/payment/payment-management.blade.php` or a JS file.

**Step 2: Add cancel payment state and methods**

Add to the Alpine data object:

```javascript
// Cancel payment state
showCancelPaymentModal: false,
cancelPaymentData: null,
cancelPaymentReason: '',
cancelPaymentError: '',
cancelPaymentLoading: false,

// Open cancel payment modal
openCancelPaymentModal(tx) {
    this.cancelPaymentData = tx;
    this.cancelPaymentReason = '';
    this.cancelPaymentError = '';
    this.showCancelPaymentModal = true;
},

// Confirm cancel payment
async confirmCancelPayment() {
    if (!this.cancelPaymentReason.trim()) {
        this.cancelPaymentError = 'Please provide a reason for cancellation.';
        return;
    }

    this.cancelPaymentLoading = true;
    this.cancelPaymentError = '';

    try {
        const response = await fetch(`/payment/${this.cancelPaymentData.payment_id}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                reason: this.cancelPaymentReason.trim(),
            }),
        });

        const result = await response.json();

        if (result.success) {
            this.showCancelPaymentModal = false;
            this.cancelPaymentReason = '';
            // Reload transactions to reflect cancellation
            this.loadMyTransactions(this.selectedDate);
            // Show success notification (use existing notification system if available)
            alert('Payment cancelled successfully.');
        } else {
            this.cancelPaymentError = result.message || 'Failed to cancel payment.';
        }
    } catch (error) {
        this.cancelPaymentError = 'Network error. Please try again.';
    } finally {
        this.cancelPaymentLoading = false;
    }
},
```

**Step 3: Commit**

```bash
git add resources/views/pages/payment/payment-management.blade.php
git commit -m "feat(ui): add Alpine.js cancel payment logic with modal interaction"
```

---

## Task 12: Add Cancel Action to Customer Ledger View

**Files:**
- Modify: `resources/views/pages/customer/tabs/ledger-tab.blade.php` (or the JS that renders ledger rows)
- The ledger tab renders rows dynamically via JavaScript. Find the JS that builds ledger table rows.

**Step 1: Find the ledger rendering JS**

Search for the function that populates `#ledger-tbody`. It's likely in a `<script>` block in the customer details page or a separate JS file. Look for `renderLedgerRow` or similar.

**Step 2: Add cancel button to PAYMENT-type ledger rows**

In the Actions column for ledger rows where `source_type === 'PAYMENT'`, add a cancel button visible only to users with `payments.void` permission:

```javascript
// Inside the ledger row rendering function, in the Actions cell:
if (entry.source_type === 'PAYMENT' && entry.status?.stat_desc !== 'CANCELLED') {
    actionsHtml += `
        <button onclick="openCancelFromLedger(${entry.source_id})"
                class="p-1.5 text-gray-600 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors"
                title="Cancel Payment">
            <i class="fas fa-ban text-xs"></i>
        </button>
    `;
}
```

Note: The `@can('payments.void')` Blade directive should wrap this in the template, or you can pass the user's permission as a JS variable from the controller and check it client-side.

**Step 3: Add openCancelFromLedger function**

```javascript
async function openCancelFromLedger(paymentId) {
    // Fetch payment details, then open cancellation modal
    // This depends on the page's Alpine or vanilla JS architecture
    // Implementation will vary based on how the customer details page works
}
```

This task is intentionally lighter on specifics because it depends on the existing ledger rendering architecture (vanilla JS with `fetch` vs Alpine.js). The implementer should inspect `resources/views/pages/customer/customer-details.blade.php` and its associated JavaScript to determine the exact integration point.

**Step 4: Commit**

```bash
git add resources/views/pages/customer/tabs/ledger-tab.blade.php
git commit -m "feat(ui): add cancel payment action to customer ledger view"
```

---

## Task 13: Update CSV/PDF Exports for Cancelled Payments

**Files:**
- Modify: `app/Http/Controllers/Payment/PaymentController.php` (exportMyTransactionsCsv, exportMyTransactionsPdf)

**Step 1: Update CSV export**

In `exportMyTransactionsCsv` (lines 240-289), add a "Status" column:

Update header row (line 254-261) to include Status:

```php
fputcsv($handle, [
    'Receipt #',
    'Customer Name',
    'Customer Code',
    'Payment Type',
    'Amount',
    'Status',
    'Time',
]);
```

Update data rows (lines 264-273) to include status:

```php
foreach ($data['transactions'] as $tx) {
    fputcsv($handle, [
        $tx['receipt_no'],
        $tx['customer_name'],
        $tx['customer_code'],
        $tx['payment_type_label'],
        $tx['amount'],
        $tx['is_cancelled'] ? 'CANCELLED' : 'ACTIVE',
        $tx['time'],
    ]);
}
```

Update summary (lines 276-283) to include cancellation info:

```php
fputcsv($handle, []);
fputcsv($handle, ['Summary']);
fputcsv($handle, ['Net Collected', $data['summary']['total_collected']]);
fputcsv($handle, ['Transaction Count', $data['summary']['transaction_count']]);
if ($data['summary']['cancelled_count'] > 0) {
    fputcsv($handle, ['Cancelled Amount', $data['summary']['cancelled_amount']]);
    fputcsv($handle, ['Cancelled Count', $data['summary']['cancelled_count']]);
}

foreach ($data['summary']['by_type'] as $type) {
    fputcsv($handle, [$type['type'], $type['amount']]);
}
```

**Step 2: The PDF report template also needs updating**

In `resources/views/pages/payment/my-transactions-report.blade.php`, add cancelled styling and summary. This is the printable report. Add conditional cancelled row styling and a cancellation summary section.

**Step 3: Commit**

```bash
git add app/Http/Controllers/Payment/PaymentController.php
git add resources/views/pages/payment/my-transactions-report.blade.php
git commit -m "feat(reports): include cancellation status and totals in cashier transaction exports"
```

---

## Task 14: Update LedgerService Balance Queries (Optional Safety)

**Files:**
- Modify: `app/Services/Ledger/LedgerService.php` (getCustomerBalance)

**Step 1: Verify balance calculation**

The current `getCustomerBalance` (line 81-87) sums ALL debits and credits without filtering by status:

```php
public function getCustomerBalance(int $customerId): float
{
    $totals = CustomerLedger::where('customer_id', $customerId)
        ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
        ->first();

    return (float) (($totals->total_debit ?? 0) - ($totals->total_credit ?? 0));
}
```

Because we use the hybrid approach (CANCELLED status + REVERSAL DEBIT entries), the math already works:
- Original CREDIT of 500 (CANCELLED status — still counted in SUM)
- REVERSAL DEBIT of 500 (ACTIVE status — counted in SUM)
- Net effect: +500 debit, +500 credit → balances out

**However**, if we want to be extra safe and not count CANCELLED entries at all (belt-and-suspenders), we could filter. But since REVERSAL entries already offset them, this is **NOT required**.

**Decision: Skip this task** — the hybrid approach makes balance math self-correcting. No changes needed to `getCustomerBalance`.

**Step 2: Commit**

No commit needed for this task.

---

## Task 15: Run Full Test Suite and Lint

**Step 1: Run all tests**

```bash
php artisan test
```

Expected: All tests pass including new cancellation tests.

**Step 2: Run code linter**

```bash
./vendor/bin/pint
```

Expected: Code formatted to PSR-12.

**Step 3: Commit any lint fixes**

```bash
git add -A
git commit -m "chore: apply PSR-12 formatting via Pint"
```

---

## Task 16: Final Integration Test

**Step 1: Manual testing checklist**

- [ ] As CASHIER: Process a water bill payment → verify receipt and ledger entries
- [ ] As ADMIN: Go to cashier transactions tab → find the payment → click cancel
- [ ] Verify cancellation modal appears with payment info
- [ ] Enter reason and confirm cancellation
- [ ] Verify payment shows as CANCELLED with strikethrough in transactions
- [ ] Verify summary totals exclude cancelled payment
- [ ] Check customer ledger → verify REVERSAL DEBIT entry exists
- [ ] Check original PAYMENT ledger entry is marked CANCELLED
- [ ] Verify the bill is now ACTIVE/OVERDUE again in the billing queue
- [ ] Process the bill payment again → verify it works
- [ ] Export CSV → verify cancelled status column
- [ ] Export PDF → verify cancelled styling
- [ ] As CASHIER (no void permission): verify cancel button is NOT visible

**Step 2: Final commit**

```bash
git add -A
git commit -m "feat(payment): complete payment cancellation feature with ledger reversal and UI"
```

---

## Dependency Graph

```
Task 1 (Migration) ─────────────────────────────────────────────┐
    │                                                            │
Task 2 (Models) ─────────────────────────────────────────────────┤
    │                                                            │
Task 3 (CustomerCharge fix) ─────────────────────────────────────┤
    │                                                            │
Task 4 (LedgerService) ─────────────────────────┐               │
    │                                            │               │
Task 5 (PaymentService.cancelPayment) ←──────────┘               │
    │                                                            │
Task 6 (Tests) ←─────────────────────────────────────────────────┘
    │
Task 7 (Controller + Route)
    │
Task 8 (ManagementService updates)
    │
Task 9 (My Transactions UI) ──────┐
    │                              │
Task 10 (Cancel Modal) ←──────────┘
    │
Task 11 (Alpine.js logic)
    │
Task 12 (Ledger view cancel action)
    │
Task 13 (CSV/PDF exports)
    │
Task 14 (Balance verification — SKIP)
    │
Task 15 (Tests + Lint)
    │
Task 16 (Integration test)
```

Tasks 1-5 are backend foundations (sequential).
Tasks 6 validates backend.
Tasks 7-8 are controller/service layer.
Tasks 9-13 are frontend/UI (can be partially parallelized).
Tasks 15-16 are final validation.
