# Water Bill Payment Integration - Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add water bill payments to the Payment Management page, allowing cashiers to process water bill payments alongside application fees.

**Architecture:** Extend existing `PaymentManagementService` to fetch unpaid `WaterBillHistory` records, extend `PaymentService` with a new `processWaterBillPayment()` method, and create a dedicated payment processing view. Reuses existing `Payment`, `PaymentAllocation`, and `CustomerLedger` infrastructure.

**Tech Stack:** Laravel 12, PHP 8.2, Blade templates, Alpine.js, Tailwind CSS

---

## Prerequisites

Before starting, ensure:
- Database has `WaterBillHistory` records with `stat_id = ACTIVE` (unpaid bills)
- `Status` table has ACTIVE, OVERDUE, and PAID statuses seeded
- Development server is running (`composer dev`)

---

## Task 1: Add getPendingWaterBills() to PaymentManagementService

**Files:**
- Modify: `app/Services/Payment/PaymentManagementService.php`

**Step 1: Add the WaterBillHistory import**

At the top of the file, add the import:

```php
use App\Models\WaterBillHistory;
```

**Step 2: Add getPendingWaterBills() method**

Add this protected method after `getPendingApplicationFees()`:

```php
/**
 * Get pending water bills (ACTIVE or OVERDUE status)
 */
protected function getPendingWaterBills(?string $search = null): Collection
{
    $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
    $overdueStatusId = Status::getIdByDescription('OVERDUE');

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

    return $query->orderBy('due_date', 'asc')->get()->map(function ($bill) use ($overdueId) {
        $connection = $bill->serviceConnection;
        $customer = $connection?->customer;
        $totalAmount = $bill->water_amount + $bill->adjustment_total;
        $isOverdue = $bill->stat_id === $overdueId;

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
            'date' => $bill->due_date,
            'date_formatted' => $bill->due_date?->format('M d, Y'),
            'status' => $isOverdue ? 'Overdue' : 'Pending Payment',
            'status_color' => $isOverdue ? 'red' : 'yellow',
            'process_url' => route('payment.process.bill', $bill->bill_id),
        ];
    });
}
```

**Step 3: Verify file saves without syntax errors**

Run: `php artisan tinker --execute="new App\Services\Payment\PaymentManagementService()"`

Expected: No errors

**Step 4: Commit**

```bash
git add app/Services/Payment/PaymentManagementService.php
git commit -m "feat(payment): add getPendingWaterBills method to PaymentManagementService"
```

---

## Task 2: Update getPendingPayments() to Include Water Bills

**Files:**
- Modify: `app/Services/Payment/PaymentManagementService.php`

**Step 1: Update getPendingPayments() method**

Replace the existing `getPendingPayments()` method:

```php
/**
 * Get all pending payments across different sources
 *
 * Returns a unified list of items awaiting payment
 */
public function getPendingPayments(?string $type = null, ?string $search = null): Collection
{
    $payments = collect();

    // Get pending application fees (VERIFIED status, not yet paid)
    if (! $type || $type === self::TYPE_APPLICATION_FEE) {
        $applicationPayments = $this->getPendingApplicationFees($search);
        $payments = $payments->merge($applicationPayments);
    }

    // Get pending water bills (ACTIVE or OVERDUE status)
    if (! $type || $type === self::TYPE_WATER_BILL) {
        $billPayments = $this->getPendingWaterBills($search);
        $payments = $payments->merge($billPayments);
    }

    // Sort by date (oldest first)
    return $payments->sortBy('date')->values();
}
```

**Step 2: Update getPaymentTypes() method**

Replace the existing `getPaymentTypes()` method:

```php
/**
 * Get payment type options for filter dropdown
 */
public function getPaymentTypes(): array
{
    return [
        ['value' => '', 'label' => 'All Types'],
        ['value' => self::TYPE_APPLICATION_FEE, 'label' => 'Application Fee'],
        ['value' => self::TYPE_WATER_BILL, 'label' => 'Water Bill'],
    ];
}
```

**Step 3: Verify changes work**

Run: `php artisan tinker --execute="app(App\Services\Payment\PaymentManagementService::class)->getPaymentTypes()"`

Expected: Array with 3 items including 'Water Bill'

**Step 4: Commit**

```bash
git add app/Services/Payment/PaymentManagementService.php
git commit -m "feat(payment): include water bills in getPendingPayments"
```

---

## Task 3: Update Statistics to Include Water Bills

**Files:**
- Modify: `app/Services/Payment/PaymentManagementService.php`

**Step 1: Update getStatistics() method**

Replace the existing `getStatistics()` method:

```php
/**
 * Get payment queue statistics
 */
public function getStatistics(): array
{
    $verifiedStatusId = Status::getIdByDescription(Status::VERIFIED);
    $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
    $overdueStatusId = Status::getIdByDescription('OVERDUE');

    // Pending application fees
    $pendingApplications = ServiceApplication::with('customerCharges')
        ->where('stat_id', $verifiedStatusId)
        ->whereNull('payment_id')
        ->get();

    $totalPendingApps = $pendingApplications->sum(function ($app) {
        return $app->customerCharges->sum(fn ($c) => $c->total_amount);
    });
    $pendingAppCount = $pendingApplications->count();

    // Pending water bills
    $pendingBills = WaterBillHistory::whereIn('stat_id', array_filter([$activeStatusId, $overdueStatusId]))->get();
    $totalPendingBills = $pendingBills->sum(fn ($b) => $b->water_amount + $b->adjustment_total);
    $pendingBillCount = $pendingBills->count();

    // Combined totals
    $totalPending = $totalPendingApps + $totalPendingBills;
    $pendingCount = $pendingAppCount + $pendingBillCount;

    // Today's collections
    $todayPayments = Payment::whereDate('payment_date', today())->get();
    $todayCollection = $todayPayments->sum('amount_received');
    $todayCount = $todayPayments->count();

    // This month's collections
    $monthPayments = Payment::whereMonth('payment_date', now()->month)
        ->whereYear('payment_date', now()->year)
        ->get();
    $monthCollection = $monthPayments->sum('amount_received');

    return [
        'pending_amount' => $totalPending,
        'pending_amount_formatted' => '₱ ' . number_format($totalPending, 2),
        'pending_count' => $pendingCount,
        'today_collection' => $todayCollection,
        'today_collection_formatted' => '₱ ' . number_format($todayCollection, 2),
        'today_count' => $todayCount,
        'month_collection' => $monthCollection,
        'month_collection_formatted' => '₱ ' . number_format($monthCollection, 2),
    ];
}
```

**Step 2: Verify statistics include water bills**

Run: `php artisan tinker --execute="app(App\Services\Payment\PaymentManagementService::class)->getStatistics()"`

Expected: Array with combined pending counts

**Step 3: Commit**

```bash
git add app/Services/Payment/PaymentManagementService.php
git commit -m "feat(payment): include water bills in payment statistics"
```

---

## Task 4: Add processWaterBillPayment() to PaymentService

**Files:**
- Modify: `app/Services/Payment/PaymentService.php`

**Step 1: Add WaterBillHistory import**

At the top of the file, add:

```php
use App\Models\WaterBillHistory;
```

**Step 2: Add getWaterBillDetails() method**

Add this method after `getCustomerPayments()`:

```php
/**
 * Get water bill details for payment processing
 */
public function getWaterBillDetails(int $billId): ?WaterBillHistory
{
    $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
    $overdueStatusId = Status::getIdByDescription('OVERDUE');

    return WaterBillHistory::with([
        'serviceConnection.customer',
        'serviceConnection.address.purok',
        'serviceConnection.address.barangay',
        'serviceConnection.accountType',
        'period',
        'previousReading',
        'currentReading',
        'status',
    ])
        ->where('bill_id', $billId)
        ->whereIn('stat_id', array_filter([$activeStatusId, $overdueStatusId]))
        ->first();
}
```

**Step 3: Add processWaterBillPayment() method**

Add this method after `getWaterBillDetails()`:

```php
/**
 * Process payment for a water bill
 *
 * Creates Payment, PaymentAllocation, CustomerLedger entry
 * Updates WaterBillHistory status to PAID
 *
 * @param  int  $billId  The water bill to pay
 * @param  float  $amountReceived  Amount received from customer
 * @param  int  $userId  The cashier processing the payment
 * @return array Contains 'payment', 'allocation', 'change'
 *
 * @throws \Exception
 */
public function processWaterBillPayment(int $billId, float $amountReceived, int $userId): array
{
    $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
    $overdueStatusId = Status::getIdByDescription('OVERDUE');
    $paidStatusId = Status::getIdByDescription(Status::PAID);

    // Find the bill and validate it's unpaid
    $bill = WaterBillHistory::with(['serviceConnection.customer', 'period'])
        ->where('bill_id', $billId)
        ->whereIn('stat_id', array_filter([$activeStatusId, $overdueStatusId]))
        ->lockForUpdate()
        ->first();

    if (! $bill) {
        throw new \Exception('Bill not found or already paid.');
    }

    $totalDue = $bill->water_amount + $bill->adjustment_total;

    // Validate payment amount (full payment required)
    if ($amountReceived < $totalDue) {
        throw new \Exception(
            'Full payment required. Amount due: ₱' . number_format($totalDue, 2) .
            '. Received: ₱' . number_format($amountReceived, 2)
        );
    }

    $change = $amountReceived - $totalDue;
    $connection = $bill->serviceConnection;
    $customer = $connection->customer;

    return DB::transaction(function () use (
        $bill, $connection, $customer, $amountReceived, $totalDue, $change, $userId, $paidStatusId
    ) {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

        // 1. Create Payment record
        $payment = Payment::create([
            'receipt_no' => $this->generateReceiptNumber(),
            'payer_id' => $customer->cust_id,
            'payment_date' => now()->toDateString(),
            'amount_received' => $amountReceived,
            'user_id' => $userId,
            'stat_id' => $activeStatusId,
        ]);

        // 2. Create PaymentAllocation (target_type = 'BILL')
        $allocation = PaymentAllocation::create([
            'payment_id' => $payment->payment_id,
            'target_type' => 'BILL',
            'target_id' => $bill->bill_id,
            'amount_applied' => $totalDue,
            'period_id' => $bill->period_id,
            'connection_id' => $connection->connection_id,
        ]);

        // 3. Create CustomerLedger CREDIT entry
        $this->ledgerService->recordPaymentAllocation(
            $allocation,
            $payment,
            'Payment for Water Bill - ' . ($bill->period?->per_name ?? 'Unknown'),
            $userId
        );

        // 4. Update bill status to PAID
        $bill->update([
            'stat_id' => $paidStatusId,
        ]);

        return [
            'payment' => $payment->fresh(),
            'allocation' => $allocation,
            'total_paid' => $totalDue,
            'amount_received' => $amountReceived,
            'change' => $change,
        ];
    });
}
```

**Step 4: Verify file saves without syntax errors**

Run: `php artisan tinker --execute="new App\Services\Payment\PaymentService(app(App\Services\Charge\ApplicationChargeService::class), app(App\Services\Ledger\LedgerService::class))"`

Expected: No errors

**Step 5: Commit**

```bash
git add app/Services/Payment/PaymentService.php
git commit -m "feat(payment): add processWaterBillPayment method to PaymentService"
```

---

## Task 5: Add Routes for Water Bill Payment

**Files:**
- Modify: `routes/web.php`

**Step 1: Add water bill payment routes**

Find the section with payment routes (around line 197-210) and add these routes inside the `payments.view` permission group:

```php
// Water Bill Payment Processing
Route::get('/payment/process/bill/{id}', [PaymentController::class, 'processWaterBillPayment'])
    ->name('payment.process.bill');
Route::post('/payment/bill/{id}/process', [PaymentController::class, 'storeWaterBillPayment'])
    ->name('payment.bill.store');
```

**Step 2: Verify routes are registered**

Run: `php artisan route:list --name=payment.process.bill`

Expected: Shows the GET route

Run: `php artisan route:list --name=payment.bill.store`

Expected: Shows the POST route

**Step 3: Commit**

```bash
git add routes/web.php
git commit -m "feat(payment): add routes for water bill payment processing"
```

---

## Task 6: Add Controller Methods for Water Bill Payment

**Files:**
- Modify: `app/Http/Controllers/Payment/PaymentController.php`

**Step 1: Add processWaterBillPayment() method**

Add after the existing `processApplicationPayment()` method:

```php
/**
 * Show water bill payment processing form
 */
public function processWaterBillPayment(int $id)
{
    $bill = $this->paymentService->getWaterBillDetails($id);

    if (! $bill) {
        abort(404, 'Bill not found or already paid.');
    }

    $totalAmount = $bill->water_amount + $bill->adjustment_total;

    return view('pages.payment.process-water-bill', [
        'bill' => $bill,
        'totalAmount' => $totalAmount,
    ]);
}
```

**Step 2: Add storeWaterBillPayment() method**

Add after `processWaterBillPayment()`:

```php
/**
 * Process water bill payment
 */
public function storeWaterBillPayment(int $id, Request $request)
{
    $request->validate([
        'amount_received' => 'required|numeric|min:0',
    ]);

    try {
        $result = $this->paymentService->processWaterBillPayment(
            $id,
            (float) $request->amount_received,
            auth()->id()
        );

        return redirect()
            ->route('payment.receipt', $result['payment']->payment_id)
            ->with('success', 'Payment processed successfully. Change: ₱' . number_format($result['change'], 2));

    } catch (\Exception $e) {
        return back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}
```

**Step 3: Verify file saves without syntax errors**

Run: `php artisan tinker --execute="new App\Http\Controllers\Payment\PaymentController(app(App\Services\Payment\PaymentManagementService::class), app(App\Services\Payment\PaymentService::class))"`

Expected: No errors

**Step 4: Commit**

```bash
git add app/Http/Controllers/Payment/PaymentController.php
git commit -m "feat(payment): add controller methods for water bill payment"
```

---

## Task 7: Create Water Bill Payment View

**Files:**
- Create: `resources/views/pages/payment/process-water-bill.blade.php`

**Step 1: Create the view file**

Create `resources/views/pages/payment/process-water-bill.blade.php` with the following content:

```blade
<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">

            {{-- Back Button --}}
            <div class="mb-6">
                <a href="{{ route('payment.management') }}"
                    class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Payment Management
                </a>
            </div>

            {{-- Header --}}
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white mb-4 shadow-lg">
                    <i class="fas fa-tint text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Process Water Bill Payment</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Complete the payment transaction below</p>
            </div>

            {{-- Error Message --}}
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                        <p class="text-red-700 dark:text-red-300">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="space-y-6">
                {{-- Customer Information Card --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                        <h2 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-user-circle mr-3"></i>
                            Customer Information
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer Name</label>
                                <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $bill->serviceConnection->customer->cust_first_name }}
                                    {{ $bill->serviceConnection->customer->cust_middle_name ? substr($bill->serviceConnection->customer->cust_middle_name, 0, 1) . '.' : '' }}
                                    {{ $bill->serviceConnection->customer->cust_last_name }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Account Number</label>
                                <p class="mt-1 text-lg font-mono font-semibold text-gray-900 dark:text-white">
                                    {{ $bill->serviceConnection->account_no }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Address</label>
                                <p class="mt-1 text-gray-700 dark:text-gray-300">
                                    {{ $bill->serviceConnection->address->purok->purok_name ?? '' }}{{ $bill->serviceConnection->address->purok ? ',' : '' }}
                                    {{ $bill->serviceConnection->address->barangay->b_name ?? 'N/A' }}, Initao
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Account Type</label>
                                <p class="mt-1 text-gray-700 dark:text-gray-300">
                                    {{ $bill->serviceConnection->accountType->at_desc ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Bill Details Card --}}
                <div
                    class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4">
                        <h2 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-file-invoice-dollar mr-3"></i>
                            Bill Details
                        </h2>
                    </div>
                    <div class="p-6">
                        {{-- Period and Due Date --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Billing Period</label>
                                <p class="mt-1 text-xl font-bold text-gray-900 dark:text-white">
                                    {{ $bill->period->per_name ?? 'Unknown Period' }}
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Due Date</label>
                                <div class="mt-1 flex items-center gap-3">
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $bill->due_date?->format('F d, Y') ?? 'N/A' }}
                                    </p>
                                    @if ($bill->due_date && $bill->due_date->isPast())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            <i class="fas fa-exclamation-circle mr-1"></i>Overdue
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Meter Readings --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="text-center">
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Previous Reading</label>
                                <p class="mt-1 text-lg font-mono font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($bill->previousReading->reading_value ?? 0, 3) }} m³
                                </p>
                            </div>
                            <div class="text-center">
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Current Reading</label>
                                <p class="mt-1 text-lg font-mono font-semibold text-gray-900 dark:text-white">
                                    {{ number_format($bill->currentReading->reading_value ?? 0, 3) }} m³
                                </p>
                            </div>
                            <div class="text-center">
                                <label class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Consumption</label>
                                <p class="mt-1 text-lg font-mono font-bold text-blue-600 dark:text-blue-400">
                                    {{ number_format($bill->consumption, 3) }} m³
                                </p>
                            </div>
                        </div>

                        {{-- Amount Breakdown --}}
                        <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                            <div class="space-y-3">
                                <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                    <span>Water Charge</span>
                                    <span class="font-mono">₱ {{ number_format($bill->water_amount, 2) }}</span>
                                </div>
                                @if ($bill->adjustment_total != 0)
                                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                        <span>Adjustments</span>
                                        <span class="font-mono {{ $bill->adjustment_total < 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $bill->adjustment_total < 0 ? '-' : '' }}₱ {{ number_format(abs($bill->adjustment_total), 2) }}
                                        </span>
                                    </div>
                                @endif
                                <div class="flex justify-between text-xl font-bold text-gray-900 dark:text-white border-t border-gray-200 dark:border-gray-600 pt-3">
                                    <span>Total Amount Due</span>
                                    <span class="font-mono text-blue-600 dark:text-blue-400">₱ {{ number_format($totalAmount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment Form Card --}}
                <form action="{{ route('payment.bill.store', $bill->bill_id) }}" method="POST"
                    x-data="{
                        totalDue: {{ $totalAmount }},
                        amountReceived: {{ $totalAmount }},
                        get change() {
                            return Math.max(0, this.amountReceived - this.totalDue);
                        },
                        get isValid() {
                            return this.amountReceived >= this.totalDue;
                        }
                    }">
                    @csrf

                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4">
                            <h2 class="text-lg font-semibold text-white flex items-center">
                                <i class="fas fa-cash-register mr-3"></i>
                                Payment
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Amount Received --}}
                                <div>
                                    <label for="amount_received"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Amount Received <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span
                                            class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500 dark:text-gray-400 font-semibold">₱</span>
                                        <input type="number" name="amount_received" id="amount_received"
                                            x-model.number="amountReceived" step="0.01" min="{{ $totalAmount }}"
                                            required
                                            class="w-full pl-10 pr-4 py-3 text-xl font-mono font-bold border-2 rounded-xl
                                                   focus:ring-2 focus:ring-purple-500 focus:border-purple-500
                                                   dark:bg-gray-700 dark:border-gray-600 dark:text-white
                                                   transition-all duration-200"
                                            :class="isValid ? 'border-green-300 bg-green-50 dark:bg-green-900/20' :
                                                'border-red-300 bg-red-50 dark:bg-red-900/20'">
                                    </div>
                                    <p class="mt-2 text-sm" :class="isValid ? 'text-green-600' : 'text-red-600'">
                                        <i class="fas" :class="isValid ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
                                        <span x-text="isValid ? 'Amount is sufficient' : 'Amount must be at least ₱' + totalDue.toFixed(2)"></span>
                                    </p>
                                </div>

                                {{-- Change --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Change
                                    </label>
                                    <div
                                        class="w-full px-4 py-3 text-xl font-mono font-bold bg-gradient-to-r from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 border-2 border-green-300 dark:border-green-700 rounded-xl text-green-700 dark:text-green-300">
                                        ₱ <span x-text="change.toFixed(2)">0.00</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <a href="{{ route('payment.management') }}"
                                    class="px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                                    <i class="fas fa-times mr-2"></i>Cancel
                                </a>
                                <button type="submit" :disabled="!isValid"
                                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-lg">
                                    <i class="fas fa-check-circle mr-2"></i>Process Payment
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
```

**Step 2: Verify view renders without errors**

Open browser and navigate to a water bill payment URL (assuming you have a bill with ID 1):
`/payment/process/bill/1`

Expected: Page renders with bill details

**Step 3: Commit**

```bash
git add resources/views/pages/payment/process-water-bill.blade.php
git commit -m "feat(payment): add water bill payment processing view"
```

---

## Task 8: Update getPaymentType Helper for Transaction History

**Files:**
- Modify: `app/Services/Payment/PaymentManagementService.php`

**Step 1: Update getPaymentType() method**

Find the `getPaymentType()` method and update it to detect water bill payments:

```php
/**
 * Determine payment type from payment record using pre-fetched lookup
 */
protected function getPaymentType(Payment $payment, $applicationPaymentIds): string
{
    // Check if payment is linked to a service application (using in-memory lookup)
    if (isset($applicationPaymentIds[$payment->payment_id])) {
        return self::TYPE_APPLICATION_FEE;
    }

    // Check if payment has allocations to water bills
    $hasBillAllocation = $payment->paymentAllocations()
        ->where('target_type', 'BILL')
        ->exists();

    if ($hasBillAllocation) {
        return self::TYPE_WATER_BILL;
    }

    return self::TYPE_OTHER_CHARGE;
}
```

**Step 2: Commit**

```bash
git add app/Services/Payment/PaymentManagementService.php
git commit -m "feat(payment): update getPaymentType to detect water bill payments"
```

---

## Task 9: Integration Testing

**Step 1: Test pending payments API**

1. Open browser to `/customer/payment-management`
2. Verify water bills appear in the pending payments list
3. Verify type filter dropdown shows "Water Bill" option
4. Filter by "Water Bill" type - only water bills should show
5. Statistics should include water bill counts

**Step 2: Test payment processing**

1. Click "Process" on a water bill
2. Verify payment form shows correct bill details
3. Enter amount less than total - verify error
4. Enter amount equal to or greater than total
5. Click "Process Payment"
6. Verify redirect to receipt page
7. Verify bill no longer appears in pending list

**Step 3: Test transaction history**

1. Go to "My Transactions" tab
2. Verify water bill payment appears with "Water Bill" type label

**Step 4: Final commit**

```bash
git add -A
git commit -m "feat(payment): complete water bill payment integration

- Add getPendingWaterBills() to fetch unpaid bills
- Include water bills in pending payments and statistics
- Add processWaterBillPayment() for payment processing
- Create dedicated payment processing view
- Update transaction history to show water bill payments

Closes #XX"
```

---

## Summary

| Task | Description | Files |
|------|-------------|-------|
| 1 | Add getPendingWaterBills() method | PaymentManagementService.php |
| 2 | Update getPendingPayments() to include water bills | PaymentManagementService.php |
| 3 | Update statistics to include water bills | PaymentManagementService.php |
| 4 | Add processWaterBillPayment() method | PaymentService.php |
| 5 | Add routes for water bill payment | routes/web.php |
| 6 | Add controller methods | PaymentController.php |
| 7 | Create payment processing view | process-water-bill.blade.php |
| 8 | Update transaction history type detection | PaymentManagementService.php |
| 9 | Integration testing | - |

**Total: 9 tasks, 5 files modified/created**

---

## Rollback Plan

If issues arise, revert commits:

```bash
git log --oneline -10  # Find commits to revert
git revert <commit-hash>
```

Or reset to before the feature:

```bash
git reset --hard <commit-before-feature>
```

---

*End of Implementation Plan*
