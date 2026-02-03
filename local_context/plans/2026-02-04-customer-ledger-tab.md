# Customer Ledger Tab - Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add a fully functional Ledger tab to the Customer Details page (`/customer/details/{id}`) that displays all ledger transactions with filtering, running balance calculation, transaction detail modal, and export functionality.

**Architecture:** Create dedicated API endpoint `/api/customer/{id}/ledger` with `CustomerService` method for data retrieval. Reuses existing `CustomerLedger` model with polymorphic `source_type` tracking. Frontend uses Alpine.js for interactivity with Blade partial for the tab content.

**Tech Stack:** Laravel 12, PHP 8.2, Blade templates, Alpine.js, Tailwind CSS, Flowbite

---

## Prerequisites

Before starting, ensure:
- Docker containers are running (`docker compose up -d`)
- Database has `CustomerLedger` entries (or seed test data)
- `Status` table has ACTIVE status seeded
- Customer exists with ID 9 or adjust test customer ID

---

## Docker Container Access

**All commands must be executed inside the Docker container.**

To find the container name:
```bash
docker compose ps
```

To execute commands inside the container:
```bash
docker compose exec <container_name> <command>

# Example:
docker compose exec water_billing_app php artisan tinker
docker compose exec water_billing_app php artisan test
```

Throughout this plan, when you see a command like `php artisan tinker`, run it as:
```bash
docker compose exec <container_name> php artisan tinker
```

---

## Design Decisions

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Enhancement Level | Full Enhancement | Filters, detail modal, running balance, export |
| Data View | Unified Customer View | All connections in one table with connection column |
| Transaction Types | All Types | BILL, CHARGE, PAYMENT, REFUND, ADJUST, WRITE_OFF with badges |
| Approach | Service-Centric | Dedicated endpoint, clean separation, testable |
| Detail Modal | Full Details | Links to source documents (bill/charge/payment) |

---

## Task 1: Add getLedgerData() to CustomerService

**Files:**
- Modify: `app/Services/Customers/CustomerService.php`

**Step 1: Add required imports**

At the top of the file, ensure these imports exist:

```php
use App\Models\CustomerLedger;
use App\Models\Period;
use Illuminate\Pagination\LengthAwarePaginator;
```

**Step 2: Add getLedgerData() method**

Add this public method after `getCustomerDetails()`:

```php
/**
 * Get customer ledger data with filters and pagination
 *
 * @param int $customerId
 * @param array $filters ['connection_id', 'period_id', 'source_type', 'per_page', 'page']
 * @return array
 */
public function getLedgerData(int $customerId, array $filters = []): array
{
    $perPage = $filters['per_page'] ?? 20;
    $connectionId = $filters['connection_id'] ?? null;
    $periodId = $filters['period_id'] ?? null;
    $sourceType = $filters['source_type'] ?? null;

    // Build query with filters
    $query = CustomerLedger::with([
        'serviceConnection',
        'period',
        'status',
        'user',
    ])
        ->where('customer_id', $customerId);

    if ($connectionId) {
        $query->where('connection_id', $connectionId);
    }

    if ($periodId) {
        $query->where('period_id', $periodId);
    }

    if ($sourceType) {
        $query->where('source_type', $sourceType);
    }

    // Order by date descending for display
    $entries = $query->orderBy('txn_date', 'desc')
        ->orderBy('post_ts', 'desc')
        ->paginate($perPage);

    // Calculate running balance (oldest to newest for calculation)
    $allEntries = CustomerLedger::where('customer_id', $customerId)
        ->orderBy('txn_date', 'asc')
        ->orderBy('post_ts', 'asc')
        ->get();

    $runningBalances = $this->calculateRunningBalances($allEntries);

    // Map entries with running balance
    $entriesWithBalance = $entries->getCollection()->map(function ($entry) use ($runningBalances) {
        return $this->formatLedgerEntry($entry, $runningBalances[$entry->ledger_entry_id] ?? 0);
    });

    // Calculate summary
    $summary = $this->calculateLedgerSummary($customerId);

    // Get filter options
    $filterOptions = $this->getLedgerFilterOptions($customerId);

    return [
        'entries' => $entriesWithBalance,
        'pagination' => [
            'current_page' => $entries->currentPage(),
            'per_page' => $entries->perPage(),
            'total' => $entries->total(),
            'last_page' => $entries->lastPage(),
        ],
        'summary' => $summary,
        'filters' => $filterOptions,
    ];
}
```

**Step 3: Add helper methods for ledger data**

Add these private methods after `getLedgerData()`:

```php
/**
 * Calculate running balances for all entries
 */
private function calculateRunningBalances($entries): array
{
    $balances = [];
    $runningBalance = 0;

    foreach ($entries as $entry) {
        $runningBalance += ($entry->debit - $entry->credit);
        $balances[$entry->ledger_entry_id] = $runningBalance;
    }

    return $balances;
}

/**
 * Format a single ledger entry for API response
 */
private function formatLedgerEntry(CustomerLedger $entry, float $runningBalance): array
{
    return [
        'ledger_entry_id' => $entry->ledger_entry_id,
        'txn_date' => $entry->txn_date->format('Y-m-d'),
        'txn_date_formatted' => $entry->txn_date->format('M d, Y'),
        'post_ts' => $entry->post_ts?->format('Y-m-d H:i:s'),
        'source_type' => $entry->source_type,
        'source_type_label' => $this->getSourceTypeLabel($entry->source_type),
        'source_type_badge' => $this->getSourceTypeBadge($entry->source_type),
        'source_id' => $entry->source_id,
        'description' => $entry->description ?? $this->getDefaultDescription($entry),
        'debit' => (float) $entry->debit,
        'debit_formatted' => $entry->debit > 0 ? '₱' . number_format($entry->debit, 2) : '-',
        'credit' => (float) $entry->credit,
        'credit_formatted' => $entry->credit > 0 ? '₱' . number_format($entry->credit, 2) : '-',
        'running_balance' => $runningBalance,
        'running_balance_formatted' => '₱' . number_format($runningBalance, 2),
        'balance_class' => $runningBalance > 0 ? 'text-red-600' : ($runningBalance < 0 ? 'text-blue-600' : 'text-green-600'),
        'connection' => $entry->serviceConnection ? [
            'connection_id' => $entry->serviceConnection->connection_id,
            'account_no' => $entry->serviceConnection->account_no ?? 'N/A',
        ] : null,
        'period' => $entry->period ? [
            'per_id' => $entry->period->per_id,
            'period_label' => $entry->period->per_month . ' ' . $entry->period->per_year,
        ] : null,
    ];
}

/**
 * Get source type display label
 */
private function getSourceTypeLabel(string $sourceType): string
{
    return match ($sourceType) {
        'BILL' => 'Water Bill',
        'CHARGE' => 'Charge',
        'PAYMENT' => 'Payment',
        'REFUND' => 'Refund',
        'ADJUST' => 'Adjustment',
        'WRITE_OFF' => 'Write-Off',
        'TRANSFER' => 'Transfer',
        'REVERSAL' => 'Reversal',
        default => $sourceType,
    };
}

/**
 * Get source type badge CSS classes
 */
private function getSourceTypeBadge(string $sourceType): array
{
    return match ($sourceType) {
        'BILL' => ['bg' => 'bg-blue-100 dark:bg-blue-900', 'text' => 'text-blue-800 dark:text-blue-300'],
        'CHARGE' => ['bg' => 'bg-orange-100 dark:bg-orange-900', 'text' => 'text-orange-800 dark:text-orange-300'],
        'PAYMENT' => ['bg' => 'bg-green-100 dark:bg-green-900', 'text' => 'text-green-800 dark:text-green-300'],
        'REFUND' => ['bg' => 'bg-purple-100 dark:bg-purple-900', 'text' => 'text-purple-800 dark:text-purple-300'],
        'ADJUST' => ['bg' => 'bg-yellow-100 dark:bg-yellow-900', 'text' => 'text-yellow-800 dark:text-yellow-300'],
        'WRITE_OFF' => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-800 dark:text-gray-300'],
        default => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-800 dark:text-gray-300'],
    };
}

/**
 * Get default description based on source type
 */
private function getDefaultDescription(CustomerLedger $entry): string
{
    $periodLabel = $entry->period ? $entry->period->per_month . ' ' . $entry->period->per_year : '';

    return match ($entry->source_type) {
        'BILL' => "Water Bill - {$periodLabel}",
        'CHARGE' => 'Service Charge',
        'PAYMENT' => 'Payment Received',
        'REFUND' => 'Refund Issued',
        'ADJUST' => 'Balance Adjustment',
        'WRITE_OFF' => 'Amount Written Off',
        default => $entry->source_type,
    };
}

/**
 * Calculate ledger summary totals
 */
private function calculateLedgerSummary(int $customerId): array
{
    $totals = CustomerLedger::where('customer_id', $customerId)
        ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
        ->first();

    $totalDebit = (float) ($totals->total_debit ?? 0);
    $totalCredit = (float) ($totals->total_credit ?? 0);
    $netBalance = $totalDebit - $totalCredit;

    return [
        'total_debit' => $totalDebit,
        'total_debit_formatted' => '₱' . number_format($totalDebit, 2),
        'total_credit' => $totalCredit,
        'total_credit_formatted' => '₱' . number_format($totalCredit, 2),
        'net_balance' => $netBalance,
        'net_balance_formatted' => '₱' . number_format($netBalance, 2),
        'balance_class' => $netBalance > 0 ? 'text-red-600' : ($netBalance < 0 ? 'text-blue-600' : 'text-green-600'),
    ];
}

/**
 * Get filter options for ledger dropdown filters
 */
private function getLedgerFilterOptions(int $customerId): array
{
    // Get unique connections for this customer's ledger
    $connections = CustomerLedger::where('customer_id', $customerId)
        ->whereNotNull('connection_id')
        ->with('serviceConnection')
        ->get()
        ->pluck('serviceConnection')
        ->filter()
        ->unique('connection_id')
        ->map(fn($conn) => [
            'connection_id' => $conn->connection_id,
            'account_no' => $conn->account_no ?? "Connection #{$conn->connection_id}",
        ])
        ->values();

    // Get unique periods for this customer's ledger
    $periods = CustomerLedger::where('customer_id', $customerId)
        ->whereNotNull('period_id')
        ->with('period')
        ->get()
        ->pluck('period')
        ->filter()
        ->unique('per_id')
        ->sortByDesc('per_id')
        ->map(fn($period) => [
            'per_id' => $period->per_id,
            'label' => $period->per_month . ' ' . $period->per_year,
        ])
        ->values();

    // Get unique source types
    $types = CustomerLedger::where('customer_id', $customerId)
        ->distinct()
        ->pluck('source_type')
        ->map(fn($type) => [
            'value' => $type,
            'label' => $this->getSourceTypeLabel($type),
        ]);

    return [
        'connections' => $connections,
        'periods' => $periods,
        'types' => $types,
    ];
}
```

**Verification:**
- Run inside container: `docker compose exec <container_name> php artisan tinker`
- Then test: `app(App\Services\Customers\CustomerService::class)->getLedgerData(9)`

---

## Task 2: Add getLedgerEntryDetails() to CustomerService

**Files:**
- Modify: `app/Services/Customers/CustomerService.php`

**Step 1: Add method for fetching single entry with source details**

Add this public method after `getLedgerFilterOptions()`:

```php
/**
 * Get detailed ledger entry with source document information
 *
 * @param int $entryId
 * @return array
 * @throws \Exception
 */
public function getLedgerEntryDetails(int $entryId): array
{
    $entry = CustomerLedger::with([
        'serviceConnection.customer',
        'serviceConnection.accountType',
        'period',
        'status',
        'user',
    ])->find($entryId);

    if (!$entry) {
        throw new \Exception('Ledger entry not found');
    }

    $sourceDetails = $this->getSourceDocumentDetails($entry);

    return [
        'entry' => $this->formatLedgerEntry($entry, 0), // Balance not needed for detail view
        'source_details' => $sourceDetails,
        'connection_details' => $entry->serviceConnection ? [
            'connection_id' => $entry->serviceConnection->connection_id,
            'account_no' => $entry->serviceConnection->account_no,
            'customer_name' => $entry->serviceConnection->customer
                ? trim("{$entry->serviceConnection->customer->cust_first_name} {$entry->serviceConnection->customer->cust_last_name}")
                : 'N/A',
            'account_type' => $entry->serviceConnection->accountType?->at_description ?? 'N/A',
        ] : null,
        'audit_info' => [
            'created_by' => $entry->user?->name ?? 'System',
            'created_at' => $entry->created_at?->format('M d, Y H:i:s') ?? 'N/A',
            'post_timestamp' => $entry->post_ts?->format('M d, Y H:i:s.u') ?? 'N/A',
        ],
    ];
}

/**
 * Get source document details based on source_type
 */
private function getSourceDocumentDetails(CustomerLedger $entry): ?array
{
    return match ($entry->source_type) {
        'BILL' => $this->getBillDetails($entry->source_id),
        'CHARGE' => $this->getChargeDetails($entry->source_id),
        'PAYMENT' => $this->getPaymentDetails($entry->source_id),
        default => null,
    };
}

/**
 * Get water bill details
 */
private function getBillDetails(int $billId): ?array
{
    $bill = \App\Models\WaterBillHistory::with(['period', 'serviceConnection', 'currentReading', 'previousReading'])
        ->find($billId);

    if (!$bill) {
        return null;
    }

    return [
        'type' => 'BILL',
        'bill_id' => $bill->bill_id,
        'period' => $bill->period ? $bill->period->per_month . ' ' . $bill->period->per_year : 'N/A',
        'consumption' => number_format($bill->consumption, 3) . ' m³',
        'water_amount' => '₱' . number_format($bill->water_amount, 2),
        'adjustment_total' => '₱' . number_format($bill->adjustment_total ?? 0, 2),
        'total_amount' => '₱' . number_format($bill->total_amount, 2),
        'due_date' => $bill->due_date?->format('M d, Y') ?? 'N/A',
        'prev_reading' => $bill->previousReading?->reading_value ?? 'N/A',
        'curr_reading' => $bill->currentReading?->reading_value ?? 'N/A',
    ];
}

/**
 * Get charge details
 */
private function getChargeDetails(int $chargeId): ?array
{
    $charge = \App\Models\CustomerCharge::with(['chargeItem', 'serviceConnection'])
        ->find($chargeId);

    if (!$charge) {
        return null;
    }

    return [
        'type' => 'CHARGE',
        'charge_id' => $charge->charge_id,
        'charge_item' => $charge->chargeItem?->name ?? 'Service Charge',
        'description' => $charge->description,
        'quantity' => number_format($charge->quantity, 3),
        'unit_amount' => '₱' . number_format($charge->unit_amount, 2),
        'total_amount' => '₱' . number_format($charge->total_amount, 2),
        'due_date' => $charge->due_date?->format('M d, Y') ?? 'N/A',
    ];
}

/**
 * Get payment details
 */
private function getPaymentDetails(int $paymentId): ?array
{
    $payment = \App\Models\Payment::with(['payer', 'user', 'paymentAllocations'])
        ->find($paymentId);

    if (!$payment) {
        return null;
    }

    return [
        'type' => 'PAYMENT',
        'payment_id' => $payment->payment_id,
        'receipt_no' => $payment->receipt_no,
        'payment_date' => $payment->payment_date?->format('M d, Y') ?? 'N/A',
        'amount_received' => '₱' . number_format($payment->amount_received, 2),
        'payer_name' => $payment->payer
            ? trim("{$payment->payer->cust_first_name} {$payment->payer->cust_last_name}")
            : 'N/A',
        'processed_by' => $payment->user?->name ?? 'System',
        'allocations_count' => $payment->paymentAllocations->count(),
    ];
}
```

**Verification:**
- Run inside container: `docker compose exec <container_name> php artisan tinker`
- Then test: `app(App\Services\Customers\CustomerService::class)->getLedgerEntryDetails(1)`

---

## Task 3: Add API Routes and Controller Methods

**Files:**
- Modify: `routes/api.php`
- Modify: `app/Http/Controllers/Customer/CustomerController.php`

**Step 1: Add API routes**

In `routes/api.php`, find the customer routes section and add:

```php
Route::get('/customer/{id}/ledger', [CustomerController::class, 'getLedger'])->name('api.customer.ledger');
Route::get('/customer/ledger/{entryId}', [CustomerController::class, 'getLedgerEntryDetails'])->name('api.customer.ledger.entry');
```

**Step 2: Add controller methods**

In `app/Http/Controllers/Customer/CustomerController.php`, add these methods:

```php
/**
 * Get customer ledger data with filters
 *
 * @param int $id Customer ID
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function getLedger(int $id, Request $request): JsonResponse
{
    try {
        $filters = [
            'connection_id' => $request->query('connection_id'),
            'period_id' => $request->query('period_id'),
            'source_type' => $request->query('source_type'),
            'per_page' => $request->query('per_page', 20),
            'page' => $request->query('page', 1),
        ];

        $data = $this->customerService->getLedgerData($id, $filters);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}

/**
 * Get ledger entry details with source document information
 *
 * @param int $entryId
 * @return \Illuminate\Http\JsonResponse
 */
public function getLedgerEntryDetails(int $entryId): JsonResponse
{
    try {
        $data = $this->customerService->getLedgerEntryDetails($entryId);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 404);
    }
}
```

**Step 3: Ensure JsonResponse import**

At the top of `CustomerController.php`, ensure this import exists:

```php
use Illuminate\Http\JsonResponse;
```

**Verification:**
- Test endpoint from inside container:
  ```bash
  docker compose exec <container_name> curl http://localhost:8000/api/customer/9/ledger
  ```
- Or from host (if port is exposed): `curl http://localhost:8000/api/customer/9/ledger`
- Test with filters: `curl "http://localhost:8000/api/customer/9/ledger?source_type=BILL&per_page=10"`

---

## Task 4: Create Ledger Tab Blade Partial

**Files:**
- Create: `resources/views/pages/customer/tabs/ledger-tab.blade.php`

**Step 1: Create the tabs directory if it doesn't exist**

```bash
docker compose exec <container_name> mkdir -p resources/views/pages/customer/tabs
```

**Step 2: Create the ledger tab partial**

Create file `resources/views/pages/customer/tabs/ledger-tab.blade.php`:

```blade
<!-- Ledger Tab Content -->
<div id="ledger-content" class="tab-content hidden">
    <!-- Filter Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-4">
        <div class="flex flex-wrap gap-4 items-end">
            <!-- Connection Filter -->
            <div class="flex-1 min-w-[200px]">
                <label for="ledger-connection-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Service Connection
                </label>
                <select id="ledger-connection-filter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500"
                    onchange="filterLedger()">
                    <option value="">All Connections</option>
                </select>
            </div>

            <!-- Period Filter -->
            <div class="flex-1 min-w-[200px]">
                <label for="ledger-period-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Billing Period
                </label>
                <select id="ledger-period-filter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500"
                    onchange="filterLedger()">
                    <option value="">All Periods</option>
                </select>
            </div>

            <!-- Type Filter -->
            <div class="flex-1 min-w-[200px]">
                <label for="ledger-type-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Transaction Type
                </label>
                <select id="ledger-type-filter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500"
                    onchange="filterLedger()">
                    <option value="">All Types</option>
                </select>
            </div>

            <!-- Reset Button -->
            <div>
                <button type="button" onclick="resetLedgerFilters()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    <i class="fas fa-undo mr-1"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Description</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Connection</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Debit</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Credit</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Balance</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody id="ledger-tbody" class="divide-y divide-gray-200 dark:divide-gray-700">
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-spinner fa-spin mr-2"></i> Loading ledger data...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="ledger-pagination" class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    Showing <span id="ledger-showing-start">0</span> to <span id="ledger-showing-end">0</span> of <span id="ledger-total">0</span> entries
                </div>
                <div class="flex gap-2" id="ledger-pagination-buttons">
                    <!-- Pagination buttons will be inserted here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Box -->
    <div class="mt-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-lg border border-blue-200 dark:border-gray-600 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Debits</p>
                <p class="text-xl font-bold text-red-600 dark:text-red-400" id="ledger-total-debit">₱0.00</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Credits</p>
                <p class="text-xl font-bold text-green-600 dark:text-green-400" id="ledger-total-credit">₱0.00</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Net Balance</p>
                <p class="text-xl font-bold" id="ledger-net-balance">₱0.00</p>
            </div>
            <div class="text-center">
                <button onclick="exportLedger()"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-download mr-2"></i>Export Ledger
                </button>
            </div>
        </div>
    </div>
</div>
```

**Verification:**
- File exists at `resources/views/pages/customer/tabs/ledger-tab.blade.php`

---

## Task 5: Create Ledger Entry Details Modal

**Files:**
- Create: `resources/views/components/ui/customer/modals/ledger-entry-details.blade.php`

**Step 1: Create the modal component**

Create file `resources/views/components/ui/customer/modals/ledger-entry-details.blade.php`:

```blade
<!-- Ledger Entry Details Modal -->
<div id="ledger-entry-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 transition-opacity" onclick="closeLedgerEntryModal()"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <!-- Header -->
            <div class="bg-gray-100 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="modal-title">
                        <i class="fas fa-file-invoice mr-2 text-blue-600"></i>Transaction Details
                    </h3>
                    <button onclick="closeLedgerEntryModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-4" id="ledger-entry-modal-content">
                <!-- Loading state -->
                <div id="ledger-modal-loading" class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">Loading transaction details...</p>
                </div>

                <!-- Entry details (hidden initially) -->
                <div id="ledger-modal-details" class="hidden space-y-6">
                    <!-- Basic Info Section -->
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Transaction Information</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Transaction Date</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white" id="modal-txn-date">-</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Transaction Type</p>
                                <p class="text-sm" id="modal-txn-type">-</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Description</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white" id="modal-description">-</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Debit Amount</p>
                                <p class="text-sm font-semibold text-red-600" id="modal-debit">-</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Credit Amount</p>
                                <p class="text-sm font-semibold text-green-600" id="modal-credit">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Source Document Section -->
                    <div id="modal-source-section" class="hidden">
                        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Source Document</h4>
                        <div id="modal-source-content" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <!-- Source document details will be inserted here -->
                        </div>
                    </div>

                    <!-- Connection Info Section -->
                    <div id="modal-connection-section" class="hidden">
                        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Service Connection</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Account Number</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white" id="modal-account-no">-</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Account Type</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white" id="modal-account-type">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Audit Info Section -->
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                        <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Audit Information</h4>
                        <div class="grid grid-cols-2 gap-4 text-xs">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Created By</p>
                                <p class="text-gray-900 dark:text-white" id="modal-created-by">-</p>
                            </div>
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Posted At</p>
                                <p class="text-gray-900 dark:text-white" id="modal-post-ts">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-100 dark:bg-gray-700 px-6 py-4 border-t border-gray-200 dark:border-gray-600 flex justify-end gap-3">
                <button onclick="closeLedgerEntryModal()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors">
                    Close
                </button>
                <button id="modal-print-btn" onclick="printLedgerEntry()"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors hidden">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>
```

**Verification:**
- File exists at `resources/views/components/ui/customer/modals/ledger-entry-details.blade.php`

---

## Task 6: Update Customer Details Page

**Files:**
- Modify: `resources/views/pages/customer/customer-details.blade.php`

**Step 1: Add Ledger tab button**

Find the tab navigation section (around line 85-93) and add the Ledger tab button:

```blade
<nav class="-mb-px flex space-x-8">
    <button onclick="switchTab('documents')" id="tab-documents" class="tab-button border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400">
        <i class="fas fa-file-alt mr-2"></i>Documents & History
    </button>
    <button onclick="switchTab('connections')" id="tab-connections" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
        <i class="fas fa-plug mr-2"></i>Service Connections
    </button>
    <button onclick="switchTab('ledger')" id="tab-ledger" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
        <i class="fas fa-book mr-2"></i>Ledger
    </button>
</nav>
```

**Step 2: Include the ledger tab partial**

After the connections-content div (around line 135), add:

```blade
@include('pages.customer.tabs.ledger-tab')
```

**Step 3: Include the ledger entry modal**

After the connection-details modal (around line 139), add:

```blade
<x-ui.customer.modals.ledger-entry-details />
```

**Step 4: Add ledger JavaScript file to Vite**

Update the @vite directive to include the new ledger script:

```blade
@vite(['resources/js/data/customer/customer-details-data.js', 'resources/js/data/customer/enhanced-customer-data.js', 'resources/js/data/customer/customer-ledger-data.js'])
```

**Verification:**
- Visit `/customer/details/9` and verify the Ledger tab appears

---

## Task 7: Create Customer Ledger JavaScript

**Files:**
- Create: `resources/js/data/customer/customer-ledger-data.js`

**Step 1: Create the JavaScript file**

Create file `resources/js/data/customer/customer-ledger-data.js`:

```javascript
/**
 * Customer Ledger Tab - Data and interaction handling
 */

// State management
let ledgerState = {
    customerId: null,
    currentPage: 1,
    perPage: 20,
    filters: {
        connection_id: '',
        period_id: '',
        source_type: ''
    },
    data: null,
    isLoading: false
};

/**
 * Initialize ledger tab when activated
 */
window.initializeLedgerTab = function(customerId) {
    ledgerState.customerId = customerId;
    loadLedgerData();
};

/**
 * Load ledger data from API
 */
async function loadLedgerData() {
    if (!ledgerState.customerId || ledgerState.isLoading) return;

    ledgerState.isLoading = true;
    showLedgerLoading();

    try {
        const params = new URLSearchParams({
            page: ledgerState.currentPage,
            per_page: ledgerState.perPage,
            ...(ledgerState.filters.connection_id && { connection_id: ledgerState.filters.connection_id }),
            ...(ledgerState.filters.period_id && { period_id: ledgerState.filters.period_id }),
            ...(ledgerState.filters.source_type && { source_type: ledgerState.filters.source_type })
        });

        const response = await fetch(`/api/customer/${ledgerState.customerId}/ledger?${params}`);
        const result = await response.json();

        if (result.success) {
            ledgerState.data = result.data;
            populateLedgerTable(result.data.entries);
            populateLedgerSummary(result.data.summary);
            populateLedgerPagination(result.data.pagination);
            populateLedgerFilters(result.data.filters);
        } else {
            showLedgerError(result.message || 'Failed to load ledger data');
        }
    } catch (error) {
        console.error('Error loading ledger:', error);
        showLedgerError('An error occurred while loading ledger data');
    } finally {
        ledgerState.isLoading = false;
    }
}

/**
 * Show loading state in ledger table
 */
function showLedgerLoading() {
    const tbody = document.getElementById('ledger-tbody');
    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Loading ledger data...
                </td>
            </tr>
        `;
    }
}

/**
 * Show error state in ledger table
 */
function showLedgerError(message) {
    const tbody = document.getElementById('ledger-tbody');
    if (tbody) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-red-500 dark:text-red-400">
                    <i class="fas fa-exclamation-triangle mr-2"></i> ${message}
                </td>
            </tr>
        `;
    }
}

/**
 * Populate ledger table with entries
 */
function populateLedgerTable(entries) {
    const tbody = document.getElementById('ledger-tbody');
    if (!tbody) return;

    if (!entries || entries.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-inbox mr-2"></i> No ledger entries found
                </td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = entries.map(entry => `
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer" onclick="showLedgerEntryDetails(${entry.ledger_entry_id})">
            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap">
                ${entry.txn_date_formatted}
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${entry.source_type_badge.bg} ${entry.source_type_badge.text}">
                    ${entry.source_type_label}
                </span>
            </td>
            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100 max-w-xs truncate" title="${entry.description}">
                ${entry.description}
            </td>
            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                ${entry.connection ? entry.connection.account_no : '-'}
            </td>
            <td class="px-4 py-3 text-sm text-right font-medium ${entry.debit > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400'} whitespace-nowrap">
                ${entry.debit_formatted}
            </td>
            <td class="px-4 py-3 text-sm text-right font-medium ${entry.credit > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-400'} whitespace-nowrap">
                ${entry.credit_formatted}
            </td>
            <td class="px-4 py-3 text-sm text-right font-semibold ${entry.balance_class} whitespace-nowrap">
                ${entry.running_balance_formatted}
            </td>
            <td class="px-4 py-3 text-center whitespace-nowrap">
                <button onclick="event.stopPropagation(); showLedgerEntryDetails(${entry.ledger_entry_id})"
                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    <i class="fas fa-eye"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

/**
 * Populate ledger summary section
 */
function populateLedgerSummary(summary) {
    document.getElementById('ledger-total-debit').textContent = summary.total_debit_formatted;
    document.getElementById('ledger-total-credit').textContent = summary.total_credit_formatted;

    const netBalanceEl = document.getElementById('ledger-net-balance');
    netBalanceEl.textContent = summary.net_balance_formatted;
    netBalanceEl.className = `text-xl font-bold ${summary.balance_class}`;
}

/**
 * Populate ledger pagination
 */
function populateLedgerPagination(pagination) {
    document.getElementById('ledger-showing-start').textContent =
        pagination.total === 0 ? 0 : ((pagination.current_page - 1) * pagination.per_page) + 1;
    document.getElementById('ledger-showing-end').textContent =
        Math.min(pagination.current_page * pagination.per_page, pagination.total);
    document.getElementById('ledger-total').textContent = pagination.total;

    const buttonsContainer = document.getElementById('ledger-pagination-buttons');
    if (!buttonsContainer) return;

    let buttons = '';

    // Previous button
    buttons += `
        <button onclick="goToLedgerPage(${pagination.current_page - 1})"
            class="px-3 py-1 text-sm rounded-lg ${pagination.current_page === 1
                ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-700'
                : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500'}"
            ${pagination.current_page === 1 ? 'disabled' : ''}>
            <i class="fas fa-chevron-left"></i>
        </button>
    `;

    // Page numbers (show max 5)
    const startPage = Math.max(1, pagination.current_page - 2);
    const endPage = Math.min(pagination.last_page, startPage + 4);

    for (let i = startPage; i <= endPage; i++) {
        buttons += `
            <button onclick="goToLedgerPage(${i})"
                class="px-3 py-1 text-sm rounded-lg ${i === pagination.current_page
                    ? 'bg-blue-600 text-white'
                    : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500'}">
                ${i}
            </button>
        `;
    }

    // Next button
    buttons += `
        <button onclick="goToLedgerPage(${pagination.current_page + 1})"
            class="px-3 py-1 text-sm rounded-lg ${pagination.current_page === pagination.last_page
                ? 'bg-gray-100 text-gray-400 cursor-not-allowed dark:bg-gray-700'
                : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 dark:bg-gray-600 dark:text-gray-300 dark:border-gray-500'}"
            ${pagination.current_page === pagination.last_page ? 'disabled' : ''}>
            <i class="fas fa-chevron-right"></i>
        </button>
    `;

    buttonsContainer.innerHTML = buttons;
}

/**
 * Populate filter dropdowns (only on first load)
 */
function populateLedgerFilters(filters) {
    // Only populate if dropdowns are empty (first load)
    const connectionSelect = document.getElementById('ledger-connection-filter');
    if (connectionSelect && connectionSelect.options.length <= 1) {
        filters.connections.forEach(conn => {
            const option = document.createElement('option');
            option.value = conn.connection_id;
            option.textContent = conn.account_no;
            connectionSelect.appendChild(option);
        });
    }

    const periodSelect = document.getElementById('ledger-period-filter');
    if (periodSelect && periodSelect.options.length <= 1) {
        filters.periods.forEach(period => {
            const option = document.createElement('option');
            option.value = period.per_id;
            option.textContent = period.label;
            periodSelect.appendChild(option);
        });
    }

    const typeSelect = document.getElementById('ledger-type-filter');
    if (typeSelect && typeSelect.options.length <= 1) {
        filters.types.forEach(type => {
            const option = document.createElement('option');
            option.value = type.value;
            option.textContent = type.label;
            typeSelect.appendChild(option);
        });
    }
}

/**
 * Go to specific ledger page
 */
window.goToLedgerPage = function(page) {
    if (page < 1) return;
    ledgerState.currentPage = page;
    loadLedgerData();
};

/**
 * Filter ledger based on dropdown selections
 */
window.filterLedger = function() {
    ledgerState.filters.connection_id = document.getElementById('ledger-connection-filter')?.value || '';
    ledgerState.filters.period_id = document.getElementById('ledger-period-filter')?.value || '';
    ledgerState.filters.source_type = document.getElementById('ledger-type-filter')?.value || '';
    ledgerState.currentPage = 1; // Reset to first page on filter
    loadLedgerData();
};

/**
 * Reset all ledger filters
 */
window.resetLedgerFilters = function() {
    document.getElementById('ledger-connection-filter').value = '';
    document.getElementById('ledger-period-filter').value = '';
    document.getElementById('ledger-type-filter').value = '';
    ledgerState.filters = { connection_id: '', period_id: '', source_type: '' };
    ledgerState.currentPage = 1;
    loadLedgerData();
};

/**
 * Show ledger entry details modal
 */
window.showLedgerEntryDetails = async function(entryId) {
    const modal = document.getElementById('ledger-entry-modal');
    const loading = document.getElementById('ledger-modal-loading');
    const details = document.getElementById('ledger-modal-details');

    if (!modal) return;

    // Show modal with loading state
    modal.classList.remove('hidden');
    loading.classList.remove('hidden');
    details.classList.add('hidden');

    try {
        const response = await fetch(`/api/customer/ledger/${entryId}`);
        const result = await response.json();

        if (result.success) {
            populateLedgerEntryModal(result.data);
            loading.classList.add('hidden');
            details.classList.remove('hidden');
        } else {
            closeLedgerEntryModal();
            alert(result.message || 'Failed to load entry details');
        }
    } catch (error) {
        console.error('Error loading entry details:', error);
        closeLedgerEntryModal();
        alert('An error occurred while loading entry details');
    }
};

/**
 * Populate ledger entry modal with data
 */
function populateLedgerEntryModal(data) {
    const entry = data.entry;

    // Basic info
    document.getElementById('modal-txn-date').textContent = entry.txn_date_formatted;
    document.getElementById('modal-txn-type').innerHTML = `
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${entry.source_type_badge.bg} ${entry.source_type_badge.text}">
            ${entry.source_type_label}
        </span>
    `;
    document.getElementById('modal-description').textContent = entry.description;
    document.getElementById('modal-debit').textContent = entry.debit_formatted;
    document.getElementById('modal-credit').textContent = entry.credit_formatted;

    // Source document section
    const sourceSection = document.getElementById('modal-source-section');
    const sourceContent = document.getElementById('modal-source-content');

    if (data.source_details) {
        sourceSection.classList.remove('hidden');
        sourceContent.innerHTML = formatSourceDetails(data.source_details);
        document.getElementById('modal-print-btn').classList.remove('hidden');
    } else {
        sourceSection.classList.add('hidden');
        document.getElementById('modal-print-btn').classList.add('hidden');
    }

    // Connection section
    const connectionSection = document.getElementById('modal-connection-section');
    if (data.connection_details) {
        connectionSection.classList.remove('hidden');
        document.getElementById('modal-account-no').textContent = data.connection_details.account_no || '-';
        document.getElementById('modal-account-type').textContent = data.connection_details.account_type || '-';
    } else {
        connectionSection.classList.add('hidden');
    }

    // Audit info
    document.getElementById('modal-created-by').textContent = data.audit_info.created_by;
    document.getElementById('modal-post-ts').textContent = data.audit_info.post_timestamp;
}

/**
 * Format source document details for display
 */
function formatSourceDetails(source) {
    if (source.type === 'BILL') {
        return `
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Billing Period</p>
                    <p class="font-medium text-gray-900 dark:text-white">${source.period}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Due Date</p>
                    <p class="font-medium text-gray-900 dark:text-white">${source.due_date}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Previous Reading</p>
                    <p class="font-medium text-gray-900 dark:text-white">${source.prev_reading}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Current Reading</p>
                    <p class="font-medium text-gray-900 dark:text-white">${source.curr_reading}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Consumption</p>
                    <p class="font-medium text-gray-900 dark:text-white">${source.consumption}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Amount</p>
                    <p class="font-semibold text-red-600">${source.total_amount}</p>
                </div>
            </div>
        `;
    } else if (source.type === 'CHARGE') {
        return `
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div class="col-span-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Charge Item</p>
                    <p class="font-medium text-gray-900 dark:text-white">${source.charge_item}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Description</p>
                    <p class="font-medium text-gray-900 dark:text-white">${source.description || '-'}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Quantity × Unit Price</p>
                    <p class="font-medium text-gray-900 dark:text-white">${source.quantity} × ${source.unit_amount}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Amount</p>
                    <p class="font-semibold text-red-600">${source.total_amount}</p>
                </div>
            </div>
        `;
    } else if (source.type === 'PAYMENT') {
        return `
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Receipt Number</p>
                    <p class="font-mono font-medium text-gray-900 dark:text-white">${source.receipt_no}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Payment Date</p>
                    <p class="font-medium text-gray-900 dark:text-white">${source.payment_date}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Amount Received</p>
                    <p class="font-semibold text-green-600">${source.amount_received}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Processed By</p>
                    <p class="font-medium text-gray-900 dark:text-white">${source.processed_by}</p>
                </div>
            </div>
        `;
    }
    return '<p class="text-gray-500">No additional details available</p>';
}

/**
 * Close ledger entry modal
 */
window.closeLedgerEntryModal = function() {
    const modal = document.getElementById('ledger-entry-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
};

/**
 * Export ledger data
 */
window.exportLedger = function() {
    if (!ledgerState.data || !ledgerState.data.entries.length) {
        alert('No ledger data to export');
        return;
    }

    // Create CSV content
    const headers = ['Date', 'Type', 'Description', 'Connection', 'Debit', 'Credit', 'Balance'];
    const rows = ledgerState.data.entries.map(entry => [
        entry.txn_date_formatted,
        entry.source_type_label,
        `"${entry.description.replace(/"/g, '""')}"`,
        entry.connection ? entry.connection.account_no : '-',
        entry.debit,
        entry.credit,
        entry.running_balance
    ]);

    const csv = [headers.join(','), ...rows.map(row => row.join(','))].join('\n');

    // Download file
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `customer_ledger_${ledgerState.customerId}_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

/**
 * Print ledger entry
 */
window.printLedgerEntry = function() {
    window.print();
};
```

**Verification:**
- File exists at `resources/js/data/customer/customer-ledger-data.js`

---

## Task 8: Update Customer Details JavaScript

**Files:**
- Modify: `resources/js/data/customer/customer-details-data.js`

**Step 1: Update switchTab function to initialize ledger**

Find the `switchTab` function and modify it to handle the ledger tab:

```javascript
window.switchTab = function(tab) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active state from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        button.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    });

    // Show selected tab content
    const selectedContent = document.getElementById(`${tab}-content`);
    if (selectedContent) {
        selectedContent.classList.remove('hidden');
    }

    // Activate selected tab button
    const selectedButton = document.getElementById(`tab-${tab}`);
    if (selectedButton) {
        selectedButton.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        selectedButton.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
    }

    // Initialize ledger tab when first activated
    if (tab === 'ledger' && window.initializeLedgerTab && window.currentCustomerId) {
        window.initializeLedgerTab(window.currentCustomerId);
    }
};
```

**Step 2: Ensure customer ID is stored globally**

In the `loadCustomerDetails` function, add this line after extracting the customer ID:

```javascript
window.currentCustomerId = customerId;
```

**Verification:**
- Click on Ledger tab and verify data loads

---

## Task 9: Testing and Verification

**Manual Testing Steps:**

1. **Navigate to Customer Details:**
   - Go to `/customer/details/9` (or any valid customer ID)
   - Verify the Ledger tab appears in the tab navigation

2. **Test Ledger Tab:**
   - Click the Ledger tab
   - Verify the table loads with data (or shows "No ledger entries found")
   - Check filter dropdowns are populated

3. **Test Filtering:**
   - Select a connection from the dropdown
   - Verify table updates with filtered results
   - Test period filter
   - Test type filter
   - Click Reset and verify all data returns

4. **Test Pagination:**
   - If more than 20 entries, verify pagination works
   - Click page numbers and verify data updates
   - Test Previous/Next buttons

5. **Test Modal:**
   - Click on a ledger entry row
   - Verify modal opens with loading state
   - Verify details are populated correctly
   - Check source document details appear
   - Test Close button

6. **Test Export:**
   - Click Export Ledger button
   - Verify CSV file downloads
   - Open CSV and verify data format

7. **Test API Endpoints (run inside container):**
   ```bash
   # First, get the container name
   docker compose ps

   # Test ledger list
   docker compose exec <container_name> curl http://localhost:8000/api/customer/9/ledger

   # Test with filters
   docker compose exec <container_name> curl "http://localhost:8000/api/customer/9/ledger?source_type=BILL"

   # Test entry details (replace 1 with actual entry ID)
   docker compose exec <container_name> curl http://localhost:8000/api/customer/ledger/1
   ```

**Verification Checklist:**
- [ ] Ledger tab visible on customer details page
- [ ] Table displays ledger entries correctly
- [ ] Running balance calculates properly (oldest to newest)
- [ ] Type badges display with correct colors
- [ ] Debit amounts in red, Credit amounts in green
- [ ] Balance column shows correct color based on value
- [ ] Filters work correctly (connection, period, type)
- [ ] Reset button clears all filters
- [ ] Pagination displays correct page info
- [ ] Page navigation works
- [ ] Modal opens and closes properly
- [ ] Source document details display based on type
- [ ] Export downloads valid CSV file
- [ ] No JavaScript console errors
- [ ] No PHP errors in logs

---

## File Changes Summary

| Action | File Path |
|--------|-----------|
| MODIFY | `app/Services/Customers/CustomerService.php` |
| MODIFY | `app/Http/Controllers/Customer/CustomerController.php` |
| MODIFY | `routes/api.php` |
| CREATE | `resources/views/pages/customer/tabs/ledger-tab.blade.php` |
| CREATE | `resources/views/components/ui/customer/modals/ledger-entry-details.blade.php` |
| CREATE | `resources/js/data/customer/customer-ledger-data.js` |
| MODIFY | `resources/views/pages/customer/customer-details.blade.php` |
| MODIFY | `resources/js/data/customer/customer-details-data.js` |

---

## Rollback Plan

If issues arise, revert changes in this order:

1. Remove the Ledger tab button and include from `customer-details.blade.php`
2. Remove the Vite reference to `customer-ledger-data.js`
3. Remove the modal include
4. Delete created files (ledger-tab.blade.php, ledger-entry-details.blade.php, customer-ledger-data.js)
5. Remove API routes from `routes/api.php`
6. Remove controller methods from `CustomerController.php`
7. Remove service methods from `CustomerService.php`

---

_Last updated: 2026-02-04_
_Feature: Customer Ledger Tab_
_Stack: Laravel 12, PHP 8.2, Blade, Alpine.js, Tailwind CSS_
