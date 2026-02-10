# Bulk Connection Payment Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** When a cashier clicks "Process" on any water bill, show ALL outstanding items (bills + charges/penalties) for that connection with checkboxes, allowing bulk payment in a single transaction.

**Architecture:** The existing `Payment` → `PaymentAllocation` model already supports multiple allocations per payment. We add a new service method to fetch all outstanding items for a connection, refactor the payment processing to accept arrays of bill IDs and charge IDs, and rework the Blade/Alpine.js payment page to display checkboxes with dynamic total calculation. No schema changes needed.

**Tech Stack:** Laravel 12, Eloquent, Alpine.js, Tailwind CSS, Blade templates

---

## Context & Key Files

**Models involved:**
- `WaterBillHistory` (table: `water_bill_history`, PK: `bill_id`) — water bills
- `CustomerCharge` (table: `CustomerCharge`, PK: `charge_id`) — penalties, misc charges
- `Payment` (table: `Payment`, PK: `payment_id`) — payment header
- `PaymentAllocation` (table: `PaymentAllocation`, PK: `payment_allocation_id`) — line items per payment
- `CustomerLedger` (table: `CustomerLedger`, PK: `ledger_entry_id`) — debit/credit audit trail
- `Status` — status lookup. Use `Status::getIdByDescription(Status::ACTIVE)` etc.

**Application charges vs connection charges:**
- Application fees have `connection_id = NULL`, `application_id = <id>` — they will NOT appear in our queries
- Penalties/misc charges have `connection_id = <id>`, `application_id = NULL` — these are what we show

**Current payment route flow:**
- `GET /payment/process/bill/{id}` → `PaymentController::processWaterBillPayment()` → renders `process-water-bill.blade.php`
- `POST /payment/bill/{id}/process` → `PaymentController::storeWaterBillPayment()` → calls `PaymentService::processWaterBillPayment()`

---

## Task 1: Add `getConnectionOutstandingItems()` to PaymentService

**Files:**
- Modify: `app/Services/Payment/PaymentService.php:172-190` (after `getWaterBillDetails`)

**Step 1: Add the new method**

Add this method after the existing `getWaterBillDetails()` method (after line 190):

```php
/**
 * Get all outstanding items for a connection (unpaid bills + unpaid charges)
 *
 * Used by the bulk payment page to show everything a customer owes.
 * Application fees are excluded (they have their own payment flow).
 */
public function getConnectionOutstandingItems(int $connectionId): array
{
    $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
    $overdueStatusId = Status::getIdByDescription(Status::OVERDUE);

    // Unpaid water bills for this connection
    $bills = WaterBillHistory::with(['period', 'status'])
        ->where('connection_id', $connectionId)
        ->whereIn('stat_id', array_filter([$activeStatusId, $overdueStatusId]))
        ->orderBy('due_date', 'asc')
        ->get()
        ->map(function ($bill) use ($overdueStatusId) {
            return [
                'id' => $bill->bill_id,
                'type' => 'BILL',
                'description' => 'Water Bill - ' . ($bill->period?->per_name ?? 'Unknown Period'),
                'period_name' => $bill->period?->per_name ?? 'Unknown',
                'amount' => (float) ($bill->water_amount + $bill->adjustment_total),
                'due_date' => $bill->due_date?->format('M d, Y'),
                'is_overdue' => $bill->stat_id === $overdueStatusId,
                'period_id' => $bill->period_id,
            ];
        });

    // Unpaid charges for this connection (penalties, misc — NOT application fees)
    $charges = CustomerCharge::with(['chargeItem', 'status'])
        ->where('connection_id', $connectionId)
        ->whereNull('application_id')
        ->where('stat_id', $activeStatusId)
        ->orderBy('due_date', 'asc')
        ->get()
        ->filter(fn ($charge) => $charge->remaining_amount > 0)
        ->map(function ($charge) {
            return [
                'id' => $charge->charge_id,
                'type' => 'CHARGE',
                'description' => $charge->description,
                'period_name' => null,
                'amount' => (float) $charge->remaining_amount,
                'due_date' => $charge->due_date?->format('M d, Y'),
                'is_overdue' => $charge->due_date?->isPast() ?? false,
                'period_id' => null,
            ];
        })
        ->values();

    return [
        'bills' => $bills,
        'charges' => $charges,
        'bills_total' => $bills->sum('amount'),
        'charges_total' => $charges->sum('amount'),
        'grand_total' => $bills->sum('amount') + $charges->sum('amount'),
    ];
}
```

**Step 2: Add missing import**

At the top of `PaymentService.php`, add this import if not already present:

```php
use App\Models\CustomerCharge;
```

**Step 3: Verify**

Run: `docker compose exec water_billing_app php artisan tinker --execute="app(App\Services\Payment\PaymentService::class)->getConnectionOutstandingItems(1);"`

Expected: Returns array with `bills`, `charges`, totals (may be empty if connection 1 has no outstanding items — that's fine, no errors means it works).

**Step 4: Commit**

```bash
git add app/Services/Payment/PaymentService.php
git commit -m "feat(payment): add getConnectionOutstandingItems method for bulk payment"
```

---

## Task 2: Refactor `processWaterBillPayment()` for Bulk Payment

**Files:**
- Modify: `app/Services/Payment/PaymentService.php:205-290`

**Step 1: Add the new bulk method below the existing `processWaterBillPayment` method**

Add this new method after line 290 (after the existing `processWaterBillPayment` method). We keep the old method intact for now and add the new one:

```php
/**
 * Process bulk payment for a connection — multiple bills and/or charges
 *
 * Creates one Payment, multiple PaymentAllocations, multiple ledger CREDIT entries.
 * Marks each paid bill as PAID and each paid charge as PAID.
 *
 * @param  int  $connectionId  The service connection
 * @param  float  $amountReceived  Amount received from customer
 * @param  int  $userId  The cashier processing the payment
 * @param  array  $billIds  Array of bill_id values to pay
 * @param  array  $chargeIds  Array of charge_id values to pay
 * @return array  Contains 'payment', 'allocations', 'total_paid', 'amount_received', 'change'
 *
 * @throws \Exception
 */
public function processConnectionPayment(
    int $connectionId,
    float $amountReceived,
    int $userId,
    array $billIds = [],
    array $chargeIds = []
): array {
    if (empty($billIds) && empty($chargeIds)) {
        throw new \Exception('At least one bill or charge must be selected for payment.');
    }

    $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
    $overdueStatusId = Status::getIdByDescription(Status::OVERDUE);
    $paidStatusId = Status::getIdByDescription(Status::PAID);

    return DB::transaction(function () use (
        $connectionId, $amountReceived, $userId, $billIds, $chargeIds,
        $activeStatusId, $overdueStatusId, $paidStatusId
    ) {
        $totalDue = 0;
        $allocations = collect();

        // --- Lock and validate bills ---
        $bills = collect();
        if (! empty($billIds)) {
            $bills = WaterBillHistory::with(['serviceConnection.customer', 'period'])
                ->where('connection_id', $connectionId)
                ->whereIn('bill_id', $billIds)
                ->whereIn('stat_id', array_filter([$activeStatusId, $overdueStatusId]))
                ->lockForUpdate()
                ->get();

            if ($bills->count() !== count($billIds)) {
                throw new \Exception('One or more selected bills are not found, already paid, or do not belong to this connection.');
            }

            // Check no closed periods
            foreach ($bills as $bill) {
                if ($bill->period && $bill->period->is_closed) {
                    throw new \Exception(
                        'Cannot process payment: The billing period "'
                        . ($bill->period->per_name ?? 'Unknown')
                        . '" has been closed.'
                    );
                }
            }

            $totalDue += $bills->sum(fn ($b) => $b->water_amount + $b->adjustment_total);
        }

        // --- Lock and validate charges ---
        $charges = collect();
        if (! empty($chargeIds)) {
            $charges = CustomerCharge::where('connection_id', $connectionId)
                ->whereNull('application_id')
                ->whereIn('charge_id', $chargeIds)
                ->where('stat_id', $activeStatusId)
                ->lockForUpdate()
                ->get();

            if ($charges->count() !== count($chargeIds)) {
                throw new \Exception('One or more selected charges are not found, already paid, or do not belong to this connection.');
            }

            $totalDue += $charges->sum(fn ($c) => $c->remaining_amount);
        }

        // --- Validate payment amount ---
        if ($amountReceived < $totalDue) {
            throw new \Exception(
                'Insufficient payment. Total due: ₱' . number_format($totalDue, 2)
                . '. Received: ₱' . number_format($amountReceived, 2)
            );
        }

        $change = $amountReceived - $totalDue;

        // Get customer from connection
        $connection = $bills->first()?->serviceConnection
            ?? ServiceConnection::with('customer')->findOrFail($connectionId);
        $customer = $connection->customer;

        // --- Create Payment record ---
        $payment = Payment::create([
            'receipt_no' => $this->generateReceiptNumber(),
            'payer_id' => $customer->cust_id,
            'payment_date' => now()->toDateString(),
            'amount_received' => $amountReceived,
            'user_id' => $userId,
            'stat_id' => $activeStatusId,
        ]);

        // --- Create allocations for bills ---
        foreach ($bills as $bill) {
            $billAmount = $bill->water_amount + $bill->adjustment_total;

            $allocation = PaymentAllocation::create([
                'payment_id' => $payment->payment_id,
                'target_type' => 'BILL',
                'target_id' => $bill->bill_id,
                'amount_applied' => $billAmount,
                'period_id' => $bill->period_id,
                'connection_id' => $connectionId,
            ]);

            $allocations->push($allocation);

            $this->ledgerService->recordPaymentAllocation(
                $allocation,
                $payment,
                'Payment for Water Bill - ' . ($bill->period?->per_name ?? 'Unknown'),
                $userId
            );

            $bill->update(['stat_id' => $paidStatusId]);
        }

        // --- Create allocations for charges ---
        foreach ($charges as $charge) {
            $chargeAmount = $charge->remaining_amount;

            $allocation = PaymentAllocation::create([
                'payment_id' => $payment->payment_id,
                'target_type' => 'CHARGE',
                'target_id' => $charge->charge_id,
                'amount_applied' => $chargeAmount,
                'period_id' => null,
                'connection_id' => $connectionId,
            ]);

            $allocations->push($allocation);

            $this->ledgerService->recordPaymentAllocation(
                $allocation,
                $payment,
                'Payment for: ' . $charge->description,
                $userId
            );

            $charge->update(['stat_id' => $paidStatusId]);
        }

        return [
            'payment' => $payment->fresh(),
            'allocations' => $allocations,
            'total_paid' => $totalDue,
            'amount_received' => $amountReceived,
            'change' => $change,
        ];
    });
}
```

**Step 2: Add missing import if needed**

At the top of `PaymentService.php`, ensure this import exists:

```php
use App\Models\ServiceConnection;
```

**Step 3: Verify**

Run: `docker compose exec water_billing_app php artisan tinker --execute="echo 'PaymentService loaded OK';"` — ensure no syntax errors.

**Step 4: Commit**

```bash
git add app/Services/Payment/PaymentService.php
git commit -m "feat(payment): add processConnectionPayment for bulk bill+charge payment"
```

---

## Task 3: Update PaymentController for Bulk Payment

**Files:**
- Modify: `app/Http/Controllers/Payment/PaymentController.php:123-187`

**Step 1: Update `processWaterBillPayment()` (line 123-137)**

Replace the existing method to also fetch outstanding items for the connection:

```php
/**
 * Show water bill payment processing form
 * Now shows ALL outstanding items for the connection (bulk payment)
 */
public function processWaterBillPayment(int $id)
{
    $bill = $this->paymentService->getWaterBillDetails($id);

    if (! $bill) {
        abort(404, 'Bill not found or already paid.');
    }

    $connection = $bill->serviceConnection;
    $outstandingItems = $this->paymentService->getConnectionOutstandingItems($connection->connection_id);

    return view('pages.payment.process-water-bill', [
        'bill' => $bill,
        'connection' => $connection,
        'outstandingItems' => $outstandingItems,
        'selectedBillId' => $bill->bill_id,
    ]);
}
```

**Step 2: Update `storeWaterBillPayment()` (line 143-187)**

Replace the existing method to accept bulk payment data. The route parameter `{id}` now represents the connection_id context, but we use the posted bill_ids/charge_ids:

```php
/**
 * Process bulk water bill + charges payment
 * Supports both AJAX (JSON) and form submission
 */
public function storeWaterBillPayment(int $id, Request $request)
{
    $request->validate([
        'amount_received' => 'required|numeric|min:0.01',
        'connection_id' => 'required|integer|exists:ServiceConnection,connection_id',
        'bill_ids' => 'nullable|array',
        'bill_ids.*' => 'integer',
        'charge_ids' => 'nullable|array',
        'charge_ids.*' => 'integer',
    ]);

    $billIds = $request->input('bill_ids', []);
    $chargeIds = $request->input('charge_ids', []);

    if (empty($billIds) && empty($chargeIds)) {
        $error = 'At least one bill or charge must be selected.';
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $error], 422);
        }
        return back()->withInput()->with('error', $error);
    }

    try {
        $result = $this->paymentService->processConnectionPayment(
            (int) $request->connection_id,
            (float) $request->amount_received,
            auth()->id(),
            $billIds,
            $chargeIds
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => [
                    'payment' => $result['payment'],
                    'receipt_no' => $result['payment']->receipt_no,
                    'total_paid' => $result['total_paid'],
                    'amount_received' => $result['amount_received'],
                    'change' => $result['change'],
                ],
            ]);
        }

        return redirect()
            ->route('payment.receipt', $result['payment']->payment_id)
            ->with('success', 'Payment processed successfully. Change: ₱' . number_format($result['change'], 2));

    } catch (\Exception $e) {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}
```

**Step 3: Verify**

Run: `docker compose exec water_billing_app php artisan route:clear && docker compose exec water_billing_app php artisan route:list --name=payment`

Expected: Routes still listed, no errors.

**Step 4: Commit**

```bash
git add app/Http/Controllers/Payment/PaymentController.php
git commit -m "feat(payment): update controller for bulk connection payment"
```

---

## Task 4: Update PaymentManagementService Queue with Charges Indicator

**Files:**
- Modify: `app/Services/Payment/PaymentManagementService.php:103-162`

**Step 1: Update `getPendingWaterBills()` to include charges info**

In the `getPendingWaterBills()` method, after the existing eager loads (line 108-114), add an eager load for the connection's unpaid charges. Then in the `->map()` callback, include charge data.

Replace the method from line 103 to line 162 with:

```php
/**
 * Get pending water bills (ACTIVE or OVERDUE status)
 * Includes unpaid charges count/total per connection for queue display
 */
protected function getPendingWaterBills(?string $search = null): Collection
{
    $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
    $overdueStatusId = Status::getIdByDescription(Status::OVERDUE);

    $query = WaterBillHistory::with([
        'serviceConnection.customer',
        'serviceConnection.address.purok',
        'serviceConnection.address.barangay',
        'period',
        'status',
    ])
        ->whereIn('stat_id', array_filter([$activeStatusId, $overdueStatusId]));

    // Apply search filter
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->whereHas('period', function ($pq) use ($search) {
                $pq->where('per_name', 'like', "%{$search}%");
            })
                ->orWhereHas('serviceConnection', function ($cq) use ($search) {
                    $cq->where('account_no', 'like', "%{$search}%");
                })
                ->orWhereHas('serviceConnection.customer', function ($custQ) use ($search) {
                    $custQ->where('cust_first_name', 'like', "%{$search}%")
                        ->orWhere('cust_last_name', 'like', "%{$search}%")
                        ->orWhere('resolution_no', 'like', "%{$search}%");
                });
        });
    }

    $overdueId = $overdueStatusId;

    // Pre-fetch unpaid charges grouped by connection_id to avoid N+1
    $bills = $query->orderBy('due_date', 'asc')->get();
    $connectionIds = $bills->pluck('connection_id')->unique()->values();

    $chargesByConnection = CustomerCharge::where('stat_id', $activeStatusId)
        ->whereNull('application_id')
        ->whereIn('connection_id', $connectionIds)
        ->get()
        ->groupBy('connection_id')
        ->map(function ($charges) {
            $unpaid = $charges->filter(fn ($c) => $c->remaining_amount > 0);
            return [
                'count' => $unpaid->count(),
                'total' => $unpaid->sum(fn ($c) => $c->remaining_amount),
            ];
        });

    return $bills->map(function ($bill) use ($overdueId, $chargesByConnection) {
        $connection = $bill->serviceConnection;
        $customer = $connection?->customer;
        $totalAmount = $bill->water_amount + $bill->adjustment_total;
        $isOverdue = $bill->stat_id === $overdueId;
        $connCharges = $chargesByConnection->get($bill->connection_id, ['count' => 0, 'total' => 0]);

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
            'amount_formatted' => '₱ ' . number_format($totalAmount, 2),
            'charges_count' => $connCharges['count'],
            'charges_total' => $connCharges['total'],
            'charges_total_formatted' => $connCharges['total'] > 0 ? '₱ ' . number_format($connCharges['total'], 2) : null,
            'date' => $bill->due_date,
            'date_formatted' => $bill->due_date?->format('M d, Y'),
            'status' => $isOverdue ? 'Overdue' : 'Pending Payment',
            'status_color' => $isOverdue ? 'red' : 'yellow',
            'action_url' => route('payment.process.bill', $bill->bill_id),
            'process_url' => route('payment.process.bill', $bill->bill_id),
            'print_url' => null,
        ];
    });
}
```

**Step 2: Add import at top of file**

```php
use App\Models\CustomerCharge;
```

**Step 3: Verify**

Run: `docker compose exec water_billing_app php artisan tinker --execute="app(App\Services\Payment\PaymentManagementService::class)->getPendingPayments();"` — should return collection without errors.

**Step 4: Commit**

```bash
git add app/Services/Payment/PaymentManagementService.php
git commit -m "feat(payment): add charges indicator to pending water bills queue"
```

---

## Task 5: Update Payment Queue UI with Charges Badge

**Files:**
- Modify: `resources/views/pages/payment/payment-management.blade.php:140-195`

**Step 1: Add a "Charges" column to the table header**

In the table header (around line 143-151), add a new column after the Amount column:

Find the existing header row and replace it:

```html
<thead class="bg-gray-50 dark:bg-gray-700/50">
    <tr>
        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Customer</th>
        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Type</th>
        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Address</th>
        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Date</th>
        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Amount</th>
        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Other Charges</th>
        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Actions</th>
    </tr>
</thead>
```

**Step 2: Add charges badge cell in the table body**

In the `<template x-for>` loop, add a new `<td>` after the amount cell (after line 175) and before the actions cell:

```html
<td class="px-6 py-4 text-right">
    <template x-if="payment.charges_count > 0">
        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400">
            <i class="fas fa-exclamation-circle text-[10px]"></i>
            <span x-text="payment.charges_total_formatted"></span>
        </span>
    </template>
    <template x-if="!payment.charges_count || payment.charges_count === 0">
        <span class="text-xs text-gray-400">-</span>
    </template>
</td>
```

**Step 3: Verify**

Open the payment management page in the browser at `http://localhost/payment/management`. Overdue bills with penalties should show an orange badge in the "Other Charges" column.

**Step 4: Commit**

```bash
git add resources/views/pages/payment/payment-management.blade.php
git commit -m "feat(payment): add charges badge to payment queue table"
```

---

## Task 6: Rework Payment Processing Page for Bulk Payment

**Files:**
- Modify: `resources/views/pages/payment/process-water-bill.blade.php` (full rework)

This is the largest task. The page currently shows a single bill. We rework it to show all outstanding items for the connection with checkboxes.

**Step 1: Replace the Blade PHP header section (lines 1-36)**

Replace the opening section with:

```blade
<x-app-layout>
    @php
        $billData = $bill ?? null;
        $customer = $billData?->serviceConnection?->customer;
        $connectionData = $connection ?? $billData?->serviceConnection;
        $customerName = $customer
            ? trim(($customer->cust_first_name ?? '') . ' ' . ($customer->cust_middle_name ? $customer->cust_middle_name[0] . '. ' : '') . ($customer->cust_last_name ?? ''))
            : '-';

        $addressParts = array_filter([
            $connectionData?->address?->purok?->purok_name ?? '',
            $connectionData?->address?->barangay?->b_name ?? '',
        ]);
        $fullAddress = count($addressParts) > 0 ? implode(', ', $addressParts) : '';
    @endphp

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800"
        x-data="waterBillPayment(@js([
            'connection_id' => $connectionData?->connection_id,
            'customer_name' => $customerName,
            'account_no' => $connectionData?->account_no,
            'resolution_no' => $customer?->resolution_no,
            'full_address' => $fullAddress,
            'barangay' => $connectionData?->address?->barangay?->b_name,
            'account_type' => $connectionData?->accountType?->at_desc ?? 'N/A',
            'selected_bill_id' => $selectedBillId ?? null,
            'bills' => $outstandingItems['bills'] ?? [],
            'charges' => $outstandingItems['charges'] ?? [],
        ]))">
```

**Step 2: Replace the left column (bill details) with the outstanding items list**

See full blade template in implementation — this includes:
- Connection info card (header with account number, customer name)
- Select All / Deselect All buttons
- Water Bills section with checkboxes (blue theme)
- Other Charges section with checkboxes (orange theme)
- Empty state when no items

**Step 3: Replace the right column (payment form) with bulk-aware form**

See full blade template in implementation — this includes:
- Amount Due summary with item count and breakdown
- Nothing Selected warning (when 0 items checked)
- Amount Received input
- Change display
- Insufficient Amount error
- Payment Method selector
- Process button (disabled when nothing selected)
- Success card with receipt info

**Step 4: Replace the `<script>` section with the new Alpine.js component**

The new component manages:
- `selectedBillIds[]` and `selectedChargeIds[]` arrays
- `toggleBill(id)` / `toggleCharge(id)` methods
- `selectAll()` / `deselectAll()` methods
- Computed `totalDue`, `selectedBillsTotal`, `selectedChargesTotal`, `totalSelectedItems`
- `canProcess` getter: requires at least 1 item selected + sufficient amount
- `processPayment()` sends `bill_ids[]` + `charge_ids[]` + `connection_id`

**Step 5: Verify**

Open `http://localhost/payment/process/bill/{any-bill-id}` in the browser. Should see:
- Connection info at top
- All outstanding bills listed with checkboxes (all pre-checked)
- All outstanding charges listed with checkboxes (all pre-checked)
- Right side shows total with breakdown, amount input, process button
- Unchecking items recalculates total and pre-fills amount
- Button disabled when nothing is selected

**Step 6: Commit**

```bash
git add resources/views/pages/payment/process-water-bill.blade.php
git commit -m "feat(payment): rework payment page for bulk connection payment with checkboxes"
```

---

## Task 7: End-to-End Smoke Test

**Step 1: Verify the full flow in browser**

1. Go to `http://localhost/payment/management`
2. Find an overdue bill that has penalties (orange badge in "Other Charges" column)
3. Click "Process" on that bill
4. Verify: All bills and charges for the connection appear with checkboxes
5. Uncheck one item — verify total recalculates, amount input updates
6. Uncheck all items — verify "Process" button is disabled, yellow warning shows
7. Re-select all items, enter amount >= total
8. Click "Confirm Payment"
9. Verify: Success card appears with receipt number
10. Click "View & Print Receipt" — verify receipt loads

**Step 2: Verify in database**

```bash
docker compose exec water_billing_app php artisan tinker --execute="
    \$p = App\Models\Payment::latest('payment_id')->first();
    echo 'Payment: ' . \$p->receipt_no . ' - ₱' . \$p->amount_received . PHP_EOL;
    echo 'Allocations: ' . \$p->paymentAllocations()->count() . PHP_EOL;
    foreach (\$p->paymentAllocations as \$a) {
        echo '  ' . \$a->target_type . ' #' . \$a->target_id . ' = ₱' . \$a->amount_applied . PHP_EOL;
    }
"
```

Expected: One payment with multiple allocations (BILL + CHARGE entries).

**Step 3: Verify ledger entries**

```bash
docker compose exec water_billing_app php artisan tinker --execute="
    \$entries = App\Models\CustomerLedger::latest('ledger_entry_id')->take(5)->get();
    foreach (\$entries as \$e) {
        echo \$e->source_type . ' | D:' . \$e->debit . ' C:' . \$e->credit . ' | ' . \$e->description . PHP_EOL;
    }
"
```

Expected: PAYMENT credit entries for each allocated item.

**Step 4: Final commit**

```bash
git add -A
git commit -m "feat(payment): complete bulk connection payment implementation"
```

---

## Summary of All Files Changed

| # | File | Change Type |
|---|---|---|
| 1 | `app/Services/Payment/PaymentService.php` | Add `getConnectionOutstandingItems()`, add `processConnectionPayment()` |
| 2 | `app/Http/Controllers/Payment/PaymentController.php` | Update `processWaterBillPayment()` and `storeWaterBillPayment()` for bulk |
| 3 | `app/Services/Payment/PaymentManagementService.php` | Add charges count/total to pending bills queue |
| 4 | `resources/views/pages/payment/payment-management.blade.php` | Add "Other Charges" column with badge |
| 5 | `resources/views/pages/payment/process-water-bill.blade.php` | Full rework for bulk payment with checkboxes |

**No new files. No schema changes. No migrations.**
