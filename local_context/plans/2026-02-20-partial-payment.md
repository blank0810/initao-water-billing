# Partial Payment for Water Bills - Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Allow customers to make partial payments on water bills, with allocation precedence: penalties (charges) first, then bill amount. Multiple partial payments are supported until the balance reaches zero.

**Architecture:** Add payment-tracking accessors to `WaterBillHistory` (mirroring the existing `CustomerCharge` pattern), rewrite the allocation logic in `PaymentService::processWaterBillPayment()` to distribute funds charges-first with partial amounts, update `getBillOutstandingItems()` to use remaining amounts, update the payment queue to show remaining balances, and update the frontend to allow any amount > 0.

**Tech Stack:** Laravel 12, PHP 8.2, MySQL 8, Alpine.js, Blade, Tailwind CSS

---

### Task 1: Add payment tracking to WaterBillHistory model

**Files:**
- Modify: `app/Models/WaterBillHistory.php`

**Step 1: Add the `HasMany` import and `paymentAllocations` relationship**

Add the import at the top of the file alongside existing imports, then add the relationship method after `billAdjustments()`. Also add `$appends` for JSON serialization and the three accessors/helpers mirroring `CustomerCharge`.

The complete additions to the model:

```php
// Add import at top (line 6, after the Model import)
use Illuminate\Database\Eloquent\Relations\HasMany;

// Add $appends property after $casts (after line 47)
protected $appends = [
    'total_amount',
    'paid_amount',
    'remaining_amount',
];

// Add after billAdjustments() method (after line 95)

/**
 * Get payment allocations for this bill (polymorphic)
 */
public function paymentAllocations(): HasMany
{
    return $this->hasMany(PaymentAllocation::class, 'target_id', 'bill_id')
        ->where('target_type', 'BILL');
}

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

/**
 * Accessor for remaining unpaid amount
 */
public function getRemainingAmountAttribute(): float
{
    return (float) max(0, $this->total_amount - $this->paid_amount);
}

/**
 * Check if the bill is fully paid via allocations
 */
public function isPaid(): bool
{
    return $this->remaining_amount <= 0;
}

/**
 * Check if the bill is partially paid
 */
public function isPartiallyPaid(): bool
{
    return $this->paid_amount > 0 && ! $this->isPaid();
}
```

**Step 2: Verify the model compiles**

Run: `docker compose exec water_billing_app php artisan tinker --execute="new App\Models\WaterBillHistory();"`
Expected: No errors

**Step 3: Commit**

```bash
git add app/Models/WaterBillHistory.php
git commit -m "feat(billing): add payment tracking accessors to WaterBillHistory model"
```

---

### Task 2: Rewrite processWaterBillPayment for partial payment support

**Files:**
- Modify: `app/Services/Payment/PaymentService.php:364-510`

**Step 1: Replace the `processWaterBillPayment` method**

Replace the entire method body (lines 364-510) with the new implementation. Key changes:
1. Remove the full-payment validation (`if ($amountReceived < $totalDue)` throw)
2. Use `$bill->remaining_amount` instead of raw `$bill->water_amount + $bill->adjustment_total`
3. Reverse allocation order: charges (penalties) FIRST, then bill
4. Allocate `min($remainingPayment, item.outstanding)` per item
5. Only mark items as PAID when fully covered
6. Track `total_applied` (actual amount distributed) separately from `amount_received`

```php
public function processWaterBillPayment(int $billId, float $amountReceived, int $userId): array
{
    $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
    $overdueStatusId = Status::getIdByDescription(Status::OVERDUE);
    $paidStatusId = Status::getIdByDescription(Status::PAID);

    $customerForNotification = null;

    $result = DB::transaction(function () use (
        $billId, $amountReceived, $userId, $activeStatusId, $overdueStatusId, $paidStatusId, &$customerForNotification
    ) {
        $allocations = collect();

        // Find the bill with lock to prevent concurrent payments
        $bill = WaterBillHistory::with(['serviceConnection.customer', 'period'])
            ->where('bill_id', $billId)
            ->whereIn('stat_id', array_filter([$activeStatusId, $overdueStatusId]))
            ->lockForUpdate()
            ->first();

        if (! $bill) {
            throw new \Exception('Bill not found or already paid.');
        }

        if ($bill->period && $bill->period->is_closed) {
            throw new \Exception(
                'Cannot process payment: The billing period "'
                .($bill->period->per_name ?? 'Unknown')
                .'" has been closed.'
            );
        }

        $connection = $bill->serviceConnection;
        $customer = $connection->customer;
        $customerForNotification = $customer;

        if (! $customer) {
            throw new \Exception('No customer associated with this connection.');
        }

        // Lock period-matched charges for this bill
        $charges = CustomerCharge::select('CustomerCharge.*', 'CustomerLedger.period_id as ledger_period_id')
            ->leftJoin('CustomerLedger', function ($join) {
                $join->on('CustomerCharge.charge_id', '=', 'CustomerLedger.source_id')
                    ->where('CustomerLedger.source_type', '=', 'CHARGE');
            })
            ->where('CustomerCharge.connection_id', $connection->connection_id)
            ->whereNull('CustomerCharge.application_id')
            ->where('CustomerCharge.stat_id', $activeStatusId)
            ->where('CustomerLedger.period_id', $bill->period_id)
            ->lockForUpdate()
            ->get()
            ->unique('charge_id')
            ->filter(fn ($c) => $c->remaining_amount > 0);

        $billRemaining = $bill->remaining_amount;
        $chargesAmount = $charges->sum(fn ($c) => $c->remaining_amount);
        $totalDue = $billRemaining + $chargesAmount;

        // Track how much of the payment is left to allocate
        $remainingPayment = min($amountReceived, $totalDue);
        $change = max(0, $amountReceived - $totalDue);

        // Create Payment record
        $payment = Payment::create([
            'receipt_no' => $this->generateReceiptNumber(),
            'payer_id' => $customer->cust_id,
            'payment_date' => now()->toDateString(),
            'amount_received' => $amountReceived,
            'user_id' => $userId,
            'stat_id' => $activeStatusId,
        ]);

        // === ALLOCATION PHASE: Charges (penalties) FIRST, then bill ===

        // 1. Allocate to period-matched charges first
        foreach ($charges as $charge) {
            if ($remainingPayment <= 0) {
                break;
            }

            $chargeRemaining = $charge->remaining_amount;
            $applyAmount = min($remainingPayment, $chargeRemaining);

            $chargeAllocation = PaymentAllocation::create([
                'payment_id' => $payment->payment_id,
                'target_type' => 'CHARGE',
                'target_id' => $charge->charge_id,
                'amount_applied' => $applyAmount,
                'period_id' => $bill->period_id,
                'connection_id' => $connection->connection_id,
            ]);
            $allocations->push($chargeAllocation);

            $this->ledgerService->recordPaymentAllocation(
                $chargeAllocation,
                $payment,
                'Payment for: '.$charge->description,
                $userId
            );

            // Mark charge as PAID only if fully covered
            if ($applyAmount >= $chargeRemaining) {
                $charge->update(['stat_id' => $paidStatusId]);

                CustomerLedger::where('source_type', 'CHARGE')
                    ->where('source_id', $charge->charge_id)
                    ->update(['stat_id' => $paidStatusId]);
            }

            $remainingPayment -= $applyAmount;
        }

        // 2. Allocate remainder to the bill
        if ($remainingPayment > 0) {
            $applyToBill = min($remainingPayment, $billRemaining);

            $billAllocation = PaymentAllocation::create([
                'payment_id' => $payment->payment_id,
                'target_type' => 'BILL',
                'target_id' => $bill->bill_id,
                'amount_applied' => $applyToBill,
                'period_id' => $bill->period_id,
                'connection_id' => $connection->connection_id,
            ]);
            $allocations->push($billAllocation);

            $this->ledgerService->recordPaymentAllocation(
                $billAllocation,
                $payment,
                'Payment for Water Bill - '.($bill->period?->per_name ?? 'Unknown'),
                $userId
            );

            // Mark bill as PAID only if fully covered
            if ($applyToBill >= $billRemaining) {
                $bill->update(['stat_id' => $paidStatusId]);

                CustomerLedger::where('source_type', 'BILL')
                    ->where('source_id', $bill->bill_id)
                    ->update(['stat_id' => $paidStatusId]);
            }

            $remainingPayment -= $applyToBill;
        }

        $totalApplied = $allocations->sum('amount_applied');

        return [
            'payment' => $payment->fresh(),
            'allocations' => $allocations,
            'total_paid' => $totalApplied,
            'total_due' => $totalDue,
            'remaining_balance' => $totalDue - $totalApplied,
            'amount_received' => $amountReceived,
            'change' => $change,
        ];
    });

    $customerName = $customerForNotification?->fullName ?? 'Unknown Customer';
    $amountFormatted = '₱'.number_format($result['total_paid'], 2);
    $this->notificationService->notifyPaymentProcessed($customerName, $amountFormatted, $result['payment']->receipt_no, $userId);

    return $result;
}
```

**Step 2: Verify syntax**

Run: `docker compose exec water_billing_app php artisan tinker --execute="app(App\Services\Payment\PaymentService::class);"`
Expected: No errors

**Step 3: Commit**

```bash
git add app/Services/Payment/PaymentService.php
git commit -m "feat(payments): rewrite processWaterBillPayment for partial payment support"
```

---

### Task 3: Update getBillOutstandingItems to use remaining amounts

**Files:**
- Modify: `app/Services/Payment/PaymentService.php:216-282`

**Step 1: Update the bill amount mapping to use `remaining_amount`**

Replace the `$billMapped` block (lines 236-245) and the return totals to use `remaining_amount` for bills. Also add `paid_amount` and `original_amount` fields for the frontend to display payment progress.

Replace lines 236-245 with:

```php
$billMapped = collect([[
    'id' => $bill->bill_id,
    'type' => 'BILL',
    'description' => 'Water Bill - '.($bill->period?->per_name ?? 'Unknown Period'),
    'period_name' => $bill->period?->per_name ?? 'Unknown',
    'amount' => (float) $bill->remaining_amount,
    'original_amount' => (float) $bill->total_amount,
    'paid_amount' => (float) $bill->paid_amount,
    'due_date' => $bill->due_date?->format('M d, Y'),
    'is_overdue' => $bill->stat_id === $overdueStatusId,
    'is_partially_paid' => $bill->isPartiallyPaid(),
    'period_id' => $bill->period_id,
]]);
```

**Step 2: Verify syntax**

Run: `docker compose exec water_billing_app php artisan tinker --execute="app(App\Services\Payment\PaymentService::class);"`
Expected: No errors

**Step 3: Commit**

```bash
git add app/Services/Payment/PaymentService.php
git commit -m "feat(payments): use remaining amounts in getBillOutstandingItems"
```

---

### Task 4: Update PaymentManagementService payment queue for partial payments

**Files:**
- Modify: `app/Services/Payment/PaymentManagementService.php:172-215`

**Step 1: Update the bill mapping in `getPendingWaterBills`**

In the `return $bills->map(...)` closure (line 172-215), replace line 175 and add partial payment tracking fields to the returned array. Change:

```php
// line 175 — replace this:
$totalAmount = $bill->water_amount + $bill->adjustment_total;
```

with:

```php
$totalAmount = $bill->remaining_amount;
$originalAmount = $bill->total_amount;
$isPartiallyPaid = $bill->isPartiallyPaid();
```

Then in the returned array (lines 193-214), replace the `amount` and `amount_formatted` entries and add the new fields:

```php
return [
    'id' => $bill->bill_id,
    'type' => self::TYPE_WATER_BILL,
    'type_label' => 'Water Bill',
    'reference_number' => $bill->period?->per_name ?? 'Unknown Period',
    'customer_id' => $customer?->cust_id,
    'customer_name' => $this->formatCustomerName($customer),
    'customer_code' => $customer?->resolution_no ?? '-',
    'address' => $this->formatAddress($connection?->address),
    'amount' => $totalAmount,
    'amount_formatted' => '₱ '.number_format($totalAmount, 2),
    'original_amount' => $originalAmount,
    'original_amount_formatted' => '₱ '.number_format($originalAmount, 2),
    'is_partially_paid' => $isPartiallyPaid,
    'charges_count' => $connCharges['count'],
    'charges_total' => $connCharges['total'],
    'charges_total_formatted' => $connCharges['total'] > 0 ? '₱ '.number_format($connCharges['total'], 2) : null,
    'date' => $bill->due_date,
    'date_formatted' => $bill->due_date?->format('M d, Y'),
    'status' => $isPartiallyPaid ? 'Partially Paid' : ($isOverdue ? 'Overdue' : 'Pending Payment'),
    'status_color' => $isPartiallyPaid ? 'blue' : ($isOverdue ? 'red' : 'yellow'),
    'action_url' => route('payment.process.bill', $bill->bill_id),
    'process_url' => route('payment.process.bill', $bill->bill_id),
    'print_url' => null,
];
```

**Step 2: Update `getStatistics` to use remaining amounts**

In `getStatistics()` (line 240), replace:

```php
$totalPendingBills = $pendingBills->sum(fn ($b) => $b->water_amount + $b->adjustment_total);
```

with:

```php
$totalPendingBills = $pendingBills->sum(fn ($b) => $b->remaining_amount);
```

**Step 3: Verify syntax**

Run: `docker compose exec water_billing_app php artisan tinker --execute="app(App\Services\Payment\PaymentManagementService::class);"`
Expected: No errors

**Step 4: Commit**

```bash
git add app/Services/Payment/PaymentManagementService.php
git commit -m "feat(payments): show remaining balance and partial payment status in queue"
```

---

### Task 5: Update the controller response for partial payments

**Files:**
- Modify: `app/Http/Controllers/Payment/PaymentController.php:163-216`

**Step 1: Add `remaining_balance` and `total_due` to JSON response**

In `storeWaterBillPayment()`, update the JSON success response (lines 187-197) to include partial payment data:

Replace lines 187-197 with:

```php
if ($request->expectsJson()) {
    return response()->json([
        'success' => true,
        'message' => 'Payment processed successfully',
        'data' => [
            'payment' => $result['payment'],
            'receipt_no' => $result['payment']->receipt_no,
            'total_paid' => $result['total_paid'],
            'total_due' => $result['total_due'],
            'remaining_balance' => $result['remaining_balance'],
            'amount_received' => $result['amount_received'],
            'change' => $result['change'],
        ],
    ]);
}
```

**Step 2: Commit**

```bash
git add app/Http/Controllers/Payment/PaymentController.php
git commit -m "feat(payments): include remaining balance in payment response"
```

---

### Task 6: Update the payment frontend for partial payments

**Files:**
- Modify: `resources/views/pages/payment/process-water-bill.blade.php`

This task makes several UI changes:
1. Remove "Full payment required" badge and messaging
2. Change `canProcess` to allow any amount > 0
3. Replace "Insufficient Amount" error with "Partial Payment" info
4. Update change/remaining balance display
5. Show partial payment indicator on bills
6. Update success card to show remaining balance

**Step 1: Remove "Full payment required" badge (line 95)**

Replace line 95:
```html
<span class="px-2.5 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">Full payment required</span>
```
with:
```html
<span x-show="totalDue > 0" class="px-2.5 py-1 text-xs font-medium rounded-full bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-400">Partial payment accepted</span>
```

**Step 2: Update the payment form header (line 203)**

Replace line 203:
```html
<p class="text-xs text-gray-500 dark:text-gray-400">Full payment required per municipal ordinance</p>
```
with:
```html
<p class="text-xs text-gray-500 dark:text-gray-400">Full or partial payment accepted</p>
```

**Step 3: Add partial payment indicator on bills (after line 124)**

After the bill amount display (line 124), add a partial payment badge. Replace line 124:
```html
<p class="text-sm font-bold text-gray-900 dark:text-white" x-text="formatCurrency(billGroup.amount)"></p>
```
with:
```html
<div class="text-right">
    <p class="text-sm font-bold text-gray-900 dark:text-white" x-text="formatCurrency(billGroup.amount)"></p>
    <p x-show="billGroup.is_partially_paid" class="text-[10px] text-blue-600 dark:text-blue-400">
        Paid: <span x-text="formatCurrency(billGroup.paid_amount)"></span> of <span x-text="formatCurrency(billGroup.original_amount)"></span>
    </p>
</div>
```

**Step 4: Replace the change/error display area (lines 248-268)**

Replace the Change Display (lines 248-256) and Error Message (lines 258-268) blocks with a unified display that handles both full and partial payment scenarios:

```html
<!-- Change / Remaining Balance Display -->
<div class="rounded-xl p-4" x-show="amountReceived > 0 && totalItems > 0"
    :class="amountReceived >= totalDue ? 'bg-green-50 dark:bg-green-900/20' : 'bg-amber-50 dark:bg-amber-900/20'">
    <!-- Full payment or overpayment: show change -->
    <div x-show="amountReceived >= totalDue">
        <div class="flex justify-between items-center">
            <span class="text-sm text-green-700 dark:text-green-400">Change</span>
            <span class="text-xl font-bold text-green-600 dark:text-green-400"
                x-text="formatCurrency(amountReceived - totalDue)"></span>
        </div>
    </div>
    <!-- Partial payment: show remaining balance -->
    <div x-show="amountReceived < totalDue">
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-medium text-amber-800 dark:text-amber-200">Partial Payment</span>
            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Remaining balance</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm text-amber-700 dark:text-amber-300">Balance after payment</span>
            <span class="text-xl font-bold text-amber-600 dark:text-amber-400"
                x-text="formatCurrency(totalDue - amountReceived)"></span>
        </div>
    </div>
</div>
```

**Step 5: Update the Alpine.js `canProcess` getter (lines 418-423)**

Replace:
```javascript
get canProcess() {
    return this.data.selected_bill_id
        && this.totalItems > 0
        && this.amountReceived
        && this.amountReceived >= this.totalDue;
},
```
with:
```javascript
get canProcess() {
    return this.data.selected_bill_id
        && this.totalItems > 0
        && this.amountReceived
        && this.amountReceived > 0;
},
```

**Step 6: Remove pre-fill of total amount in `init()` (lines 380-386)**

Replace:
```javascript
init() {
    // Pre-fill amount with total due
    this.$nextTick(() => {
        this.amountReceived = this.totalDue;
        this.calculateChange();
    });
},
```
with:
```javascript
init() {
    // No pre-fill — let cashier enter actual amount received
},
```

**Step 7: Update success card to show remaining balance (lines 328-346)**

Replace the success card details section (lines 328-346) with:

```html
<div class="p-6 space-y-4">
    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 space-y-3">
        <div class="flex justify-between">
            <span class="text-sm text-gray-500 dark:text-gray-400">Receipt No.</span>
            <span class="font-mono font-semibold text-gray-900 dark:text-white" x-text="receipt.receipt_no"></span>
        </div>
        <div class="flex justify-between">
            <span class="text-sm text-gray-500 dark:text-gray-400">Total Applied</span>
            <span class="font-semibold text-gray-900 dark:text-white" x-text="formatCurrency(receipt.total_paid)"></span>
        </div>
        <div class="flex justify-between">
            <span class="text-sm text-gray-500 dark:text-gray-400">Amount Received</span>
            <span class="font-semibold text-gray-900 dark:text-white" x-text="formatCurrency(receipt.amount_received)"></span>
        </div>
        <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600" x-show="receipt.change > 0">
            <span class="text-sm text-gray-500 dark:text-gray-400">Change</span>
            <span class="font-bold text-green-600 dark:text-green-400" x-text="formatCurrency(receipt.change)"></span>
        </div>
        <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600" x-show="receipt.remaining_balance > 0">
            <span class="text-sm text-gray-500 dark:text-gray-400">Remaining Balance</span>
            <span class="font-bold text-amber-600 dark:text-amber-400" x-text="formatCurrency(receipt.remaining_balance)"></span>
        </div>
    </div>
```

**Step 8: Commit**

```bash
git add resources/views/pages/payment/process-water-bill.blade.php
git commit -m "feat(payments): update payment UI for partial payment support"
```

---

### Task 7: Write tests for partial payment scenarios

**Files:**
- Create: `tests/Feature/Services/Payment/PartialPaymentTest.php`

**Step 1: Write the test file**

This test file covers the core partial payment scenarios. It reuses the same `beforeEach` setup pattern from the existing `PaymentCancellationTest.php`.

```php
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
```

**Step 2: Run the tests to verify they pass**

Run: `docker compose exec water_billing_app php artisan test --filter=PartialPayment -v`
Expected: All 7 tests PASS

**Step 3: Commit**

```bash
git add tests/Feature/Services/Payment/PartialPaymentTest.php
git commit -m "test(payments): add partial payment test coverage"
```

---

### Task 8: Manual verification and final commit

**Step 1: Run the full test suite**

Run: `docker compose exec water_billing_app php artisan test`
Expected: All tests pass (existing + new)

**Step 2: Run Pint for code formatting**

Run: `docker compose exec water_billing_app ./vendor/bin/pint`
Expected: Files formatted per PSR-12

**Step 3: Verify in browser**

1. Navigate to Payment Management → click a water bill
2. Enter an amount less than total due → confirm button should be enabled
3. Process the partial payment → verify success card shows remaining balance
4. Go back to payment queue → bill should still show with remaining amount
5. Click the same bill again → amount should reflect remaining balance
6. Pay the rest → bill should disappear from queue (status = PAID)

**Step 4: Final commit**

```bash
git add -A
git commit -m "chore: format code with Pint after partial payment implementation"
```
