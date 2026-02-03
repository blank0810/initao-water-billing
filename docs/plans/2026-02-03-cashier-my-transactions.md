# Cashier "My Transactions" Tab Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add a "My Transactions" tab to the Payment Management page so cashiers can view and reprint their own processed transactions for daily reconciliation.

**Architecture:** Tab-based UI within existing payment-management page. New API endpoint filters payments by `user_id` (cashier) and date. Reuses existing receipt printing route. Service layer handles data aggregation with type breakdown for reconciliation.

**Tech Stack:** Laravel 12, Alpine.js, Tailwind CSS, Flowbite components

---

## Task 1: Add Service Method for Cashier Transactions

**Files:**
- Modify: `app/Services/Payment/PaymentManagementService.php`

**Step 1: Add the getCashierTransactions method**

Add this method to `PaymentManagementService.php` after the `getStatistics()` method (around line 140):

```php
/**
 * Get transactions processed by a specific cashier for a given date
 */
public function getCashierTransactions(int $userId, ?string $date = null): array
{
    $targetDate = $date ? \Carbon\Carbon::parse($date) : today();

    $payments = Payment::with(['payer', 'status'])
        ->where('user_id', $userId)
        ->whereDate('payment_date', $targetDate)
        ->orderBy('created_at', 'desc')
        ->get();

    // Calculate summary statistics
    $totalCollected = $payments->sum('amount_received');
    $transactionCount = $payments->count();

    // Group by payment type (derive from related data)
    $byType = $this->groupPaymentsByType($payments);

    // Format transactions for display
    $transactions = $payments->map(function ($payment) {
        return [
            'payment_id' => $payment->payment_id,
            'receipt_no' => $payment->receipt_no,
            'customer_name' => $this->formatCustomerName($payment->payer),
            'customer_code' => $payment->payer->resolution_no ?? '-',
            'payment_type' => $this->getPaymentType($payment),
            'payment_type_label' => $this->getPaymentTypeLabel($payment),
            'amount' => $payment->amount_received,
            'amount_formatted' => '₱ ' . number_format($payment->amount_received, 2),
            'time' => $payment->created_at->format('g:i A'),
            'receipt_url' => route('payment.receipt', $payment->payment_id),
        ];
    });

    return [
        'date' => $targetDate->format('Y-m-d'),
        'date_display' => $targetDate->format('F j, Y'),
        'summary' => [
            'total_collected' => $totalCollected,
            'total_collected_formatted' => '₱ ' . number_format($totalCollected, 2),
            'transaction_count' => $transactionCount,
            'by_type' => $byType,
        ],
        'transactions' => $transactions,
    ];
}

/**
 * Group payments by type for summary breakdown
 */
protected function groupPaymentsByType($payments): array
{
    $grouped = [];

    foreach ($payments as $payment) {
        $type = $this->getPaymentTypeLabel($payment);
        if (!isset($grouped[$type])) {
            $grouped[$type] = 0;
        }
        $grouped[$type] += $payment->amount_received;
    }

    // Format for display
    $result = [];
    foreach ($grouped as $type => $amount) {
        $result[] = [
            'type' => $type,
            'amount' => $amount,
            'amount_formatted' => '₱ ' . number_format($amount, 2),
        ];
    }

    return $result;
}

/**
 * Determine payment type from payment record
 */
protected function getPaymentType(Payment $payment): string
{
    // Check if payment is linked to a service application
    $application = \App\Models\ServiceApplication::where('payment_id', $payment->payment_id)->first();
    if ($application) {
        return self::TYPE_APPLICATION_FEE;
    }

    // Future: Check for water bill payments
    // Future: Check for other charge payments

    return self::TYPE_OTHER_CHARGE;
}

/**
 * Get human-readable payment type label
 */
protected function getPaymentTypeLabel(Payment $payment): string
{
    $type = $this->getPaymentType($payment);

    return match ($type) {
        self::TYPE_APPLICATION_FEE => 'Application Fee',
        self::TYPE_WATER_BILL => 'Water Bill',
        self::TYPE_OTHER_CHARGE => 'Other Charges',
        default => 'Other',
    };
}
```

**Step 2: Add Payment import at top of file**

Ensure `Payment` model is imported (add if missing around line 5):

```php
use App\Models\Payment;
```

**Step 3: Verify the changes compile**

Run: `php artisan route:list --path=payment`
Expected: No errors, existing routes still listed

**Step 4: Commit**

```bash
git add app/Services/Payment/PaymentManagementService.php
git commit -m "feat(payment): add getCashierTransactions service method

Adds method to retrieve cashier's own transactions filtered by date
with summary stats and type breakdown for daily reconciliation."
```

---

## Task 2: Add Controller Method and API Route

**Files:**
- Modify: `app/Http/Controllers/Payment/PaymentController.php`
- Modify: `routes/web.php`

**Step 1: Add getMyTransactions method to PaymentController**

Add this method after `getStatistics()` (around line 61) in `PaymentController.php`:

```php
/**
 * Get current cashier's transactions for a specific date
 */
public function getMyTransactions(Request $request): JsonResponse
{
    $date = $request->input('date');
    $search = $request->input('search');

    $data = $this->paymentManagementService->getCashierTransactions(
        auth()->id(),
        $date
    );

    // Apply client-side search filter if provided
    if ($search) {
        $searchLower = strtolower($search);
        $data['transactions'] = $data['transactions']->filter(function ($tx) use ($searchLower) {
            return str_contains(strtolower($tx['receipt_no']), $searchLower)
                || str_contains(strtolower($tx['customer_name']), $searchLower);
        })->values();
    }

    return response()->json([
        'success' => true,
        'data' => $data,
    ]);
}
```

**Step 2: Add the API route**

In `routes/web.php`, find the payment routes section (around line 193-199) and add the new route:

```php
// Payment Processing - View (payments.view permission)
Route::middleware(['permission:payments.view'])->group(function () {
    Route::get('/customer/payment-management', [PaymentController::class, 'index'])->name('payment.management');
    Route::get('/api/payments/pending', [PaymentController::class, 'getPendingPayments'])->name('api.payments.pending');
    Route::get('/api/payments/statistics', [PaymentController::class, 'getStatistics'])->name('api.payments.statistics');
    Route::get('/api/payments/my-transactions', [PaymentController::class, 'getMyTransactions'])->name('api.payments.my-transactions');
    Route::get('/payment/process/application/{id}', [PaymentController::class, 'processApplicationPayment'])->name('payment.process.application');
    Route::get('/payment/receipt/{id}', [PaymentController::class, 'showReceipt'])->name('payment.receipt');
});
```

**Step 3: Verify route is registered**

Run: `php artisan route:list --path=my-transactions`
Expected: Shows `GET api/payments/my-transactions` route

**Step 4: Commit**

```bash
git add app/Http/Controllers/Payment/PaymentController.php routes/web.php
git commit -m "feat(payment): add API endpoint for cashier's own transactions

Adds GET /api/payments/my-transactions endpoint that returns
the current user's processed payments filtered by date."
```

---

## Task 3: Create Transaction Detail Modal Component

**Files:**
- Create: `resources/views/components/ui/payment/transaction-detail-modal.blade.php`

**Step 1: Create the modal component**

Create file `resources/views/components/ui/payment/transaction-detail-modal.blade.php`:

```blade
<div x-show="showDetailModal"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
         @click="showDetailModal = false"
         x-show="showDetailModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <!-- Modal -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-lg w-full"
             x-show="showDetailModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             @click.away="showDetailModal = false">

            <!-- Header -->
            <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                        <i class="fas fa-receipt text-blue-600 dark:text-blue-400 text-lg"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Transaction Details</h3>
                </div>
                <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Content -->
            <div class="p-5 space-y-4">
                <!-- Receipt Info -->
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Receipt #</span>
                            <p class="font-semibold text-gray-900 dark:text-white font-mono" x-text="selectedTransaction?.receipt_no"></p>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Time</span>
                            <p class="font-semibold text-gray-900 dark:text-white" x-text="selectedTransaction?.time"></p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500 dark:text-gray-400">Processed by</span>
                            <p class="font-semibold text-gray-900 dark:text-white">You ({{ auth()->user()->name }})</p>
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Customer</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Name</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="selectedTransaction?.customer_name"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Reference #</span>
                            <span class="font-medium text-gray-900 dark:text-white font-mono" x-text="selectedTransaction?.customer_code"></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Payment</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Type</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="selectedTransaction?.payment_type_label"></span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600">
                            <span class="font-semibold text-gray-900 dark:text-white">Amount Paid</span>
                            <span class="font-bold text-lg text-green-600 dark:text-green-400" x-text="selectedTransaction?.amount_formatted"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex gap-3 p-5 border-t border-gray-200 dark:border-gray-700">
                <button @click="showDetailModal = false"
                        class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-medium transition-colors">
                    Close
                </button>
                <a :href="selectedTransaction?.receipt_url"
                   target="_blank"
                   class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors text-center">
                    <i class="fas fa-print mr-2"></i>Print Receipt
                </a>
            </div>
        </div>
    </div>
</div>
```

**Step 2: Verify file exists**

Run: `ls -la resources/views/components/ui/payment/transaction-detail-modal.blade.php`
Expected: File exists with correct permissions

**Step 3: Commit**

```bash
git add resources/views/components/ui/payment/transaction-detail-modal.blade.php
git commit -m "feat(payment): add transaction detail modal component

Creates reusable modal for viewing transaction details with
print receipt action for cashier's own transactions."
```

---

## Task 4: Create My Transactions Tab Partial

**Files:**
- Create: `resources/views/pages/payment/partials/my-transactions-tab.blade.php`

**Step 1: Create the tab partial**

Create file `resources/views/pages/payment/partials/my-transactions-tab.blade.php`:

```blade
<div x-show="activeTab === 'my-transactions'" x-cloak>
    <!-- Date Selector & Summary Cards -->
    <div class="mb-6">
        <!-- Date Navigation -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <i class="fas fa-calendar-day text-gray-500 dark:text-gray-400"></i>
                <span class="text-lg font-semibold text-gray-900 dark:text-white" x-text="myTransactions.date_display || 'Today'"></span>
            </div>
            <div class="flex items-center gap-2">
                <button @click="loadMyTransactions()"
                        class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg font-medium transition-colors">
                    Today
                </button>
                <input type="date"
                       x-model="selectedDate"
                       @change="loadMyTransactions(selectedDate)"
                       class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Total Collected -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <i class="fas fa-coins text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Collected</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="myTransactions.summary?.total_collected_formatted || '₱ 0.00'"></p>
                    </div>
                </div>
            </div>

            <!-- Transaction Count -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <i class="fas fa-receipt text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Transactions</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="myTransactions.summary?.transaction_count || 0"></p>
                    </div>
                </div>
            </div>

            <!-- Breakdown by Type -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                <div class="flex items-start">
                    <div class="flex-shrink-0 p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                        <i class="fas fa-chart-pie text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Breakdown by Type</p>
                        <div class="space-y-1">
                            <template x-for="item in (myTransactions.summary?.by_type || [])" :key="item.type">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400" x-text="item.type"></span>
                                    <span class="font-medium text-gray-900 dark:text-white" x-text="item.amount_formatted"></span>
                                </div>
                            </template>
                            <template x-if="!myTransactions.summary?.by_type?.length">
                                <p class="text-sm text-gray-400 dark:text-gray-500">No transactions</p>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text"
                   x-model="myTransactionsSearch"
                   @input.debounce.300ms="filterMyTransactions()"
                   placeholder="Search receipt or customer..."
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
    </div>

    <!-- Transaction List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Loading State -->
        <div x-show="myTransactionsLoading" class="p-8 text-center">
            <i class="fas fa-spinner fa-spin text-3xl text-blue-600 dark:text-blue-400 mb-4"></i>
            <p class="text-gray-600 dark:text-gray-400">Loading transactions...</p>
        </div>

        <!-- Empty State -->
        <div x-show="!myTransactionsLoading && filteredMyTransactions.length === 0" class="p-8 text-center">
            <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-inbox text-2xl text-gray-400 dark:text-gray-500"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No transactions found</h3>
            <p class="text-gray-600 dark:text-gray-400">You haven't processed any payments for this date.</p>
        </div>

        <!-- Table -->
        <div x-show="!myTransactionsLoading && filteredMyTransactions.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Receipt #</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="tx in filteredMyTransactions" :key="tx.payment_id">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-sm font-mono font-medium text-gray-900 dark:text-white" x-text="tx.receipt_no"></span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="tx.customer_name"></p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                      :class="tx.payment_type === 'APPLICATION_FEE' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400'"
                                      x-text="tx.payment_type_label">
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-gray-900 dark:text-white" x-text="tx.amount_formatted"></span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-700 dark:text-gray-300" x-text="tx.time"></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
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
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div x-show="!myTransactionsLoading && filteredMyTransactions.length > 0"
             class="px-6 py-3 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Showing <span class="font-medium" x-text="filteredMyTransactions.length"></span> transaction(s)
            </p>
        </div>
    </div>
</div>
```

**Step 2: Verify file exists**

Run: `ls -la resources/views/pages/payment/partials/my-transactions-tab.blade.php`
Expected: File exists

**Step 3: Commit**

```bash
git add resources/views/pages/payment/partials/my-transactions-tab.blade.php
git commit -m "feat(payment): add my-transactions tab partial view

Creates tab content for cashier's transaction list with summary
cards, date picker, search, and transaction table."
```

---

## Task 5: Update Payment Management Page with Tabs

**Files:**
- Modify: `resources/views/pages/payment/payment-management.blade.php`

**Step 1: Update the Alpine.js component with tab support**

Replace the entire content of `payment-management.blade.php` with the tabbed version:

```blade
<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900" x-data="paymentManagement()">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <!-- Page Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Payment Management</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Process payments and view your transactions</p>
            </div>

            <!-- Tab Navigation -->
            <div class="mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex gap-4">
                        <button @click="activeTab = 'pending'"
                                :class="activeTab === 'pending'
                                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors">
                            <i class="fas fa-clock mr-2"></i>Pending Payments
                            <span x-show="stats.pending_count > 0"
                                  class="ml-2 px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400"
                                  x-text="stats.pending_count"></span>
                        </button>
                        <button @click="activeTab = 'my-transactions'; loadMyTransactions()"
                                :class="activeTab === 'my-transactions'
                                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'"
                                class="py-3 px-1 border-b-2 font-medium text-sm transition-colors">
                            <i class="fas fa-user-check mr-2"></i>My Transactions
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Pending Payments Tab -->
            <div x-show="activeTab === 'pending'">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                                <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Payments</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.pending_amount_formatted">₱ 0.00</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><span x-text="stats.pending_count">0</span> items</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Today's Collection</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.today_collection_formatted">₱ 0.00</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400"><span x-text="stats.today_count">0</span> transactions</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                <i class="fas fa-calendar-alt text-blue-600 dark:text-blue-400 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">This Month</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="stats.month_collection_formatted">₱ 0.00</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ now()->format('F Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                <i class="fas fa-receipt text-purple-600 dark:text-purple-400 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Queue</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white" x-text="filteredPayments.length">0</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">in current view</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 mb-6">
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Search -->
                        <div class="flex-1 min-w-[200px]">
                            <div class="relative">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" x-model="searchQuery" @input.debounce.300ms="filterPayments()"
                                    placeholder="Search by name, application #, or resolution #..."
                                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>

                        <!-- Type Filter -->
                        <div class="w-48">
                            <select x-model="typeFilter" @change="filterPayments()"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @foreach($paymentTypes as $type)
                                    <option value="{{ $type['value'] }}">{{ $type['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Refresh Button -->
                        <button @click="loadPayments()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors">
                            <i class="fas fa-sync-alt" :class="isLoading && 'fa-spin'"></i>
                        </button>
                    </div>
                </div>

                <!-- Payment Queue Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Loading State -->
                    <div x-show="isLoading" class="p-8 text-center">
                        <i class="fas fa-spinner fa-spin text-3xl text-blue-600 dark:text-blue-400 mb-4"></i>
                        <p class="text-gray-600 dark:text-gray-400">Loading payments...</p>
                    </div>

                    <!-- Empty State -->
                    <div x-show="!isLoading && filteredPayments.length === 0" class="p-8 text-center">
                        <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-inbox text-2xl text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No pending payments</h3>
                        <p class="text-gray-600 dark:text-gray-400">All payments have been processed or no applications match your search.</p>
                    </div>

                    <!-- Table -->
                    <div x-show="!isLoading && filteredPayments.length > 0" class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Address</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="payment in filteredPayments" :key="payment.id + '-' + payment.type">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="payment.customer_name"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 font-mono" x-text="payment.reference_number"></p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                :class="payment.type === 'APPLICATION_FEE' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400'">
                                                <span x-text="payment.type_label"></span>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-700 dark:text-gray-300" x-text="payment.address"></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-700 dark:text-gray-300" x-text="payment.date_formatted"></p>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <p class="text-sm font-bold text-gray-900 dark:text-white" x-text="payment.amount_formatted"></p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center gap-2">
                                                <a :href="payment.print_url" target="_blank"
                                                    class="p-2 text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors"
                                                    title="Print Order of Payment">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <a :href="payment.action_url"
                                                    class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg font-medium transition-colors">
                                                    <i class="fas fa-money-bill-wave mr-1"></i>Process
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- My Transactions Tab -->
            @include('pages.payment.partials.my-transactions-tab')

        </div>

        <!-- Transaction Detail Modal -->
        @include('components.ui.payment.transaction-detail-modal')
    </div>

    <script>
    function paymentManagement() {
        return {
            // Tab state
            activeTab: 'pending',

            // Pending payments state
            payments: [],
            filteredPayments: [],
            stats: @json($stats),
            searchQuery: '',
            typeFilter: '',
            isLoading: true,

            // My transactions state
            myTransactions: {},
            filteredMyTransactions: [],
            myTransactionsSearch: '',
            myTransactionsLoading: false,
            selectedDate: '',
            selectedTransaction: null,
            showDetailModal: false,

            async init() {
                await this.loadPayments();

                // Check URL for tab param
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('tab') === 'my-transactions') {
                    this.activeTab = 'my-transactions';
                    await this.loadMyTransactions();
                }
            },

            async loadPayments() {
                this.isLoading = true;

                try {
                    const params = new URLSearchParams();
                    if (this.typeFilter) params.append('type', this.typeFilter);
                    if (this.searchQuery) params.append('search', this.searchQuery);

                    const response = await fetch(`/api/payments/pending?${params.toString()}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.payments = result.data;
                        this.filteredPayments = result.data;
                    }

                    // Also refresh stats
                    await this.loadStats();
                } catch (error) {
                    console.error('Failed to load payments:', error);
                } finally {
                    this.isLoading = false;
                }
            },

            async loadStats() {
                try {
                    const response = await fetch('/api/payments/statistics', {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.stats = result.data;
                    }
                } catch (error) {
                    console.error('Failed to load stats:', error);
                }
            },

            filterPayments() {
                this.loadPayments();
            },

            async loadMyTransactions(date = null) {
                this.myTransactionsLoading = true;

                try {
                    const params = new URLSearchParams();
                    if (date) params.append('date', date);

                    const response = await fetch(`/api/payments/my-transactions?${params.toString()}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.myTransactions = result.data;
                        this.filteredMyTransactions = result.data.transactions || [];
                        this.selectedDate = result.data.date || '';
                    }
                } catch (error) {
                    console.error('Failed to load my transactions:', error);
                } finally {
                    this.myTransactionsLoading = false;
                }
            },

            filterMyTransactions() {
                if (!this.myTransactionsSearch) {
                    this.filteredMyTransactions = this.myTransactions.transactions || [];
                    return;
                }

                const search = this.myTransactionsSearch.toLowerCase();
                this.filteredMyTransactions = (this.myTransactions.transactions || []).filter(tx =>
                    tx.receipt_no.toLowerCase().includes(search) ||
                    tx.customer_name.toLowerCase().includes(search)
                );
            },

            viewTransaction(tx) {
                this.selectedTransaction = tx;
                this.showDetailModal = true;
            }
        };
    }
    </script>
</x-app-layout>
```

**Step 2: Verify the page loads**

Run: `php artisan serve` (in another terminal)
Visit: `http://localhost:8000/customer/payment-management`
Expected: Page loads with two tabs: "Pending Payments" and "My Transactions"

**Step 3: Commit**

```bash
git add resources/views/pages/payment/payment-management.blade.php
git commit -m "feat(payment): integrate tabbed UI for pending payments and my transactions

Adds tab navigation to payment management page with:
- Pending Payments tab (existing functionality)
- My Transactions tab (cashier's own processed payments)
- Transaction detail modal integration
- Date picker and search for transaction filtering"
```

---

## Task 6: Test End-to-End Functionality

**Files:**
- None (testing only)

**Step 1: Test the API endpoint**

Run: `php artisan tinker`

```php
// Simulate API call
$userId = \App\Models\User::first()->id;
$service = new \App\Services\Payment\PaymentManagementService();
$result = $service->getCashierTransactions($userId);
dd($result);
```

Expected: Returns array with `date`, `date_display`, `summary`, and `transactions` keys

**Step 2: Test via browser**

1. Login as a cashier user
2. Navigate to `/customer/payment-management`
3. Click "My Transactions" tab
4. Verify:
   - Summary cards show correct totals
   - Transaction list displays (if any payments exist for today)
   - Date picker changes the displayed date
   - Search filters the list
   - "View" button opens modal with transaction details
   - "Print" button opens receipt in new tab

**Step 3: Test with different dates**

1. Select a past date using the date picker
2. Verify transactions for that date are displayed
3. Click "Today" button to return to current date

**Step 4: Final commit**

```bash
git add -A
git commit -m "chore(payment): complete cashier my-transactions feature

Feature complete with:
- Tabbed payment management UI
- Cashier-specific transaction filtering by date
- Summary statistics with type breakdown
- Transaction detail modal
- Receipt reprinting capability"
```

---

## Summary

| Task | Description | Files |
|------|-------------|-------|
| 1 | Service method for cashier transactions | `PaymentManagementService.php` |
| 2 | Controller method and API route | `PaymentController.php`, `web.php` |
| 3 | Transaction detail modal component | `transaction-detail-modal.blade.php` |
| 4 | My transactions tab partial | `my-transactions-tab.blade.php` |
| 5 | Integrate tabs into payment management | `payment-management.blade.php` |
| 6 | End-to-end testing | None |

**Total new files:** 2
**Total modified files:** 4
**Estimated commits:** 5
