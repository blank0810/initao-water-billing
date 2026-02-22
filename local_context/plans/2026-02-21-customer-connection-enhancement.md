# Customer Connection Enhancement Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Fix the missing link between application fees and connections (backfill `connection_id`), add an interactive ledger tab to the connection detail page, and enhance the print statement to include application fees.

**Architecture:** The `CustomerLedger` and `CustomerCharge` tables already have `connection_id` columns. Application fees are created before the connection exists, so `connection_id` is NULL. The `transferChargesToConnection()` method exists but is never called. We fix the creation flow, backfill existing data, add a connection-scoped ledger API, build an interactive ledger tab (modeled after the customer ledger tab), and enhance the print statement to include all connection-related entries.

**Tech Stack:** Laravel 12, MySQL 8, Blade, Alpine.js, Tailwind CSS, Pest PHP

---

## Task 1: Backfill — Wire `transferChargesToConnection()` Into Creation Flow

**Files:**
- Modify: `app/Services/ServiceConnection/ServiceConnectionService.php:113-141` (inside `createFromApplication` DB::transaction)
- Modify: `app/Services/Charge/ApplicationChargeService.php:86-90` (extend to also update ledger entries)

**Step 1: Write the failing test**

Create `tests/Feature/Services/Connection/TransferChargesToConnectionTest.php`:

```php
<?php

use App\Models\ChargeItem;
use App\Models\ConsumerAddress;
use App\Models\Customer;
use App\Models\CustomerCharge;
use App\Models\CustomerLedger;
use App\Models\Period;
use App\Models\ServiceApplication;
use App\Models\Status;
use App\Services\Charge\ApplicationChargeService;
use App\Services\ServiceConnection\ServiceConnectionService;

beforeEach(function () {
    // Seed statuses
    $this->seed(\Database\Seeders\StatusSeeder::class);
    $this->seed(\Database\Seeders\BarangaySeeder::class);
    $this->seed(\Database\Seeders\PurokSeeder::class);
    $this->seed(\Database\Seeders\AccountTypeSeeder::class);
    $this->seed(\Database\Seeders\ChargeItemSeeder::class);
});

it('transfers charge and ledger connection_id when connection is created from application', function () {
    // Create customer and address
    $customer = Customer::factory()->create();
    $address = ConsumerAddress::factory()->create(['cust_id' => $customer->cust_id]);

    // Create application in SCHEDULED status
    $scheduledStatusId = Status::getIdByDescription(Status::SCHEDULED);
    $application = ServiceApplication::create([
        'customer_id' => $customer->cust_id,
        'address_id' => $address->ca_id,
        'application_number' => 'APP-TEST-' . uniqid(),
        'submitted_at' => now(),
        'stat_id' => $scheduledStatusId,
    ]);

    // Generate application charges (connection_id will be NULL)
    $chargeService = app(ApplicationChargeService::class);
    $charges = $chargeService->generateApplicationCharges($application);

    // Create ledger entries for those charges (simulating what happens at payment)
    $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
    foreach ($charges as $charge) {
        CustomerLedger::create([
            'customer_id' => $customer->cust_id,
            'connection_id' => null,
            'txn_date' => now()->toDateString(),
            'post_ts' => now(),
            'source_type' => 'CHARGE',
            'source_id' => $charge->charge_id,
            'source_line_no' => 1,
            'description' => $charge->description,
            'debit' => $charge->total_amount,
            'credit' => 0,
            'user_id' => 1,
            'stat_id' => $activeStatusId,
        ]);
    }

    // Verify charges and ledger entries have NULL connection_id
    expect(CustomerCharge::where('application_id', $application->application_id)
        ->whereNull('connection_id')->count())->toBe($charges->count());
    expect(CustomerLedger::where('customer_id', $customer->cust_id)
        ->whereNull('connection_id')->count())->toBe($charges->count());

    // Create connection from application
    $connectionService = app(ServiceConnectionService::class);
    $connection = $connectionService->createFromApplication(
        $application,
        1, // account_type_id
        null
    );

    // After connection creation, charges should have connection_id
    expect(CustomerCharge::where('application_id', $application->application_id)
        ->whereNull('connection_id')->count())->toBe(0);
    expect(CustomerCharge::where('application_id', $application->application_id)
        ->where('connection_id', $connection->connection_id)->count())->toBe($charges->count());

    // Ledger entries should also have connection_id
    expect(CustomerLedger::where('customer_id', $customer->cust_id)
        ->where('source_type', 'CHARGE')
        ->whereNull('connection_id')->count())->toBe(0);
    expect(CustomerLedger::where('customer_id', $customer->cust_id)
        ->where('source_type', 'CHARGE')
        ->where('connection_id', $connection->connection_id)->count())->toBe($charges->count());
});
```

**Step 2: Run test to verify it fails**

Run: `docker compose exec water_billing_app php artisan test --filter=TransferChargesToConnectionTest`
Expected: FAIL — charges and ledger entries still have NULL connection_id after connection creation.

**Step 3: Implement the fix**

In `app/Services/Charge/ApplicationChargeService.php`, extend `transferChargesToConnection()` to also update ledger entries:

```php
/**
 * Transfer charges to connection when application is completed
 *
 * Updates CustomerCharge.connection_id and corresponding CustomerLedger entries
 */
public function transferChargesToConnection(int $applicationId, int $connectionId): void
{
    // Update charges
    $chargeIds = CustomerCharge::where('application_id', $applicationId)
        ->pluck('charge_id');

    CustomerCharge::where('application_id', $applicationId)
        ->update(['connection_id' => $connectionId]);

    // Update ledger entries for these charges (CHARGE debits)
    CustomerLedger::where('source_type', 'CHARGE')
        ->whereIn('source_id', $chargeIds)
        ->whereNull('connection_id')
        ->update(['connection_id' => $connectionId]);

    // Update ledger entries for payments allocated to these charges
    $paymentIds = \App\Models\PaymentAllocation::where('target_type', 'CHARGE')
        ->whereIn('target_id', $chargeIds)
        ->pluck('payment_id');

    if ($paymentIds->isNotEmpty()) {
        // Update payment allocations
        \App\Models\PaymentAllocation::where('target_type', 'CHARGE')
            ->whereIn('target_id', $chargeIds)
            ->whereNull('connection_id')
            ->update(['connection_id' => $connectionId]);

        // Update payment ledger entries that match these allocations
        CustomerLedger::where('source_type', 'PAYMENT')
            ->whereIn('source_id', $paymentIds)
            ->whereNull('connection_id')
            ->update(['connection_id' => $connectionId]);
    }
}
```

In `app/Services/ServiceConnection/ServiceConnectionService.php`, add `ApplicationChargeService` to the constructor and call `transferChargesToConnection()` inside the transaction in `createFromApplication()`:

Add to constructor:
```php
use App\Services\Charge\ApplicationChargeService;

public function __construct(
    protected ServiceApplicationService $applicationService,
    protected NotificationService $notificationService,
    protected ApplicationChargeService $chargeService
) {}
```

Inside `createFromApplication()`, after `$this->applicationService->markAsConnected(...)` (line 132-135), add:
```php
// Transfer application charges and ledger entries to the new connection
$this->chargeService->transferChargesToConnection(
    $application->application_id,
    $connection->connection_id
);
```

**Step 4: Run test to verify it passes**

Run: `docker compose exec water_billing_app php artisan test --filter=TransferChargesToConnectionTest`
Expected: PASS

**Step 5: Commit**

```bash
git add app/Services/Charge/ApplicationChargeService.php app/Services/ServiceConnection/ServiceConnectionService.php tests/Feature/Services/Connection/TransferChargesToConnectionTest.php
git commit -m "fix(connections): wire transferChargesToConnection into creation flow"
```

---

## Task 2: Data Migration — Backfill Existing Records

**Files:**
- Create: `database/migrations/2026_02_21_000001_backfill_connection_id_on_application_charges.php`

**Step 1: Write the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Update CustomerCharge rows that have application_id but no connection_id
        // by looking up ServiceApplication.connection_id
        DB::statement('
            UPDATE CustomerCharge cc
            INNER JOIN ServiceApplication sa ON cc.application_id = sa.application_id
            SET cc.connection_id = sa.connection_id
            WHERE cc.connection_id IS NULL
            AND sa.connection_id IS NOT NULL
        ');

        // Step 2: Update CustomerLedger CHARGE entries that reference those charges
        DB::statement('
            UPDATE CustomerLedger cl
            INNER JOIN CustomerCharge cc ON cl.source_id = cc.charge_id AND cl.source_type = "CHARGE"
            SET cl.connection_id = cc.connection_id
            WHERE cl.connection_id IS NULL
            AND cc.connection_id IS NOT NULL
        ');

        // Step 3: Update PaymentAllocation entries for those charges
        DB::statement('
            UPDATE PaymentAllocation pa
            INNER JOIN CustomerCharge cc ON pa.target_id = cc.charge_id AND pa.target_type = "CHARGE"
            SET pa.connection_id = cc.connection_id
            WHERE pa.connection_id IS NULL
            AND cc.connection_id IS NOT NULL
        ');

        // Step 4: Update CustomerLedger PAYMENT entries that correspond to the updated allocations
        DB::statement('
            UPDATE CustomerLedger cl
            INNER JOIN PaymentAllocation pa ON cl.source_id = pa.payment_id
                AND cl.source_type = "PAYMENT"
                AND cl.source_line_no = pa.payment_allocation_id
            SET cl.connection_id = pa.connection_id
            WHERE cl.connection_id IS NULL
            AND pa.connection_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        // This is a data backfill — rolling back would re-null the connection_ids
        // but we can't distinguish which were originally null vs backfilled.
        // Intentionally left empty for safety.
    }
};
```

**Step 2: Run migration**

Run: `docker compose exec water_billing_app php artisan migrate`
Expected: Migration runs successfully.

**Step 3: Verify backfill worked**

Run: `docker compose exec water_billing_app php artisan tinker --execute="echo 'Charges with NULL connection_id: ' . \App\Models\CustomerCharge::whereNotNull('application_id')->whereNull('connection_id')->count(); echo '\nLedger entries with NULL connection_id: ' . \App\Models\CustomerLedger::whereNull('connection_id')->count();"`
Expected: Both should be 0 (for charges that have applications with connections).

**Step 4: Commit**

```bash
git add database/migrations/2026_02_21_000001_backfill_connection_id_on_application_charges.php
git commit -m "fix(data): backfill connection_id on application charges and ledger entries"
```

---

## Task 3: Connection Ledger API — Service and Controller

**Files:**
- Modify: `app/Services/ServiceConnection/ServiceConnectionService.php` (add `getConnectionLedgerData` method)
- Modify: `app/Http/Controllers/ServiceConnection/ServiceConnectionController.php` (add `getLedger` API method)
- Modify: `routes/web.php` (add API route)

**Step 1: Write the failing test**

Create `tests/Feature/Services/Connection/ConnectionLedgerApiTest.php`:

```php
<?php

use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Models\User;

beforeEach(function () {
    $this->seed(\Database\Seeders\StatusSeeder::class);
    $this->seed(\Database\Seeders\AccountTypeSeeder::class);
    $this->seed(\Database\Seeders\BarangaySeeder::class);
    $this->seed(\Database\Seeders\PurokSeeder::class);
    $this->seed(\Database\Seeders\RoleSeeder::class);
    $this->seed(\Database\Seeders\PermissionSeeder::class);
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    $this->seed(\Database\Seeders\UserTypeSeeder::class);
});

it('returns paginated connection ledger data via API', function () {
    $customer = Customer::factory()->create();
    $address = \App\Models\ConsumerAddress::factory()->create(['cust_id' => $customer->cust_id]);
    $connection = ServiceConnection::create([
        'account_no' => 'TEST-LEDGER-001',
        'customer_id' => $customer->cust_id,
        'address_id' => $address->ca_id,
        'account_type_id' => 1,
        'started_at' => now(),
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

    // Create some ledger entries
    CustomerLedger::create([
        'customer_id' => $customer->cust_id,
        'connection_id' => $connection->connection_id,
        'txn_date' => now()->toDateString(),
        'post_ts' => now(),
        'source_type' => 'BILL',
        'source_id' => 1,
        'source_line_no' => 1,
        'description' => 'Water Bill - Test',
        'debit' => 500.00,
        'credit' => 0,
        'user_id' => 1,
        'stat_id' => $activeStatusId,
    ]);

    CustomerLedger::create([
        'customer_id' => $customer->cust_id,
        'connection_id' => $connection->connection_id,
        'txn_date' => now()->toDateString(),
        'post_ts' => now(),
        'source_type' => 'PAYMENT',
        'source_id' => 1,
        'source_line_no' => 1,
        'description' => 'Payment for Water Bill',
        'debit' => 0,
        'credit' => 300.00,
        'user_id' => 1,
        'stat_id' => $activeStatusId,
    ]);

    $user = User::factory()->create();
    // Assign super_admin role
    $superAdminRole = \App\Models\Role::where('name', 'super_admin')->first();
    if ($superAdminRole) {
        \App\Models\UserRole::create([
            'user_id' => $user->id,
            'role_id' => $superAdminRole->id,
        ]);
    }

    $response = $this->actingAs($user)
        ->getJson("/customer/service-connection/{$connection->connection_id}/ledger");

    $response->assertOk()
        ->assertJsonStructure([
            'success',
            'data' => [
                'entries',
                'pagination' => ['current_page', 'per_page', 'total', 'last_page'],
                'summary' => ['total_debit', 'total_credit', 'net_balance'],
            ],
        ]);

    $data = $response->json('data');
    expect($data['entries'])->toHaveCount(2);
    expect($data['summary']['total_debit'])->toBe(500.0);
    expect($data['summary']['total_credit'])->toBe(300.0);
    expect($data['summary']['net_balance'])->toBe(200.0);
});
```

**Step 2: Run test to verify it fails**

Run: `docker compose exec water_billing_app php artisan test --filter=ConnectionLedgerApiTest`
Expected: FAIL — route not found (404).

**Step 3: Add the service method**

In `app/Services/ServiceConnection/ServiceConnectionService.php`, add:

```php
/**
 * Get paginated, filterable ledger data for a connection
 *
 * Mirrors CustomerService::getLedgerData() but scoped to a single connection.
 */
public function getConnectionLedgerData(int $connectionId, array $filters = []): array
{
    $perPage = max(1, min(100, (int) ($filters['per_page'] ?? 20)));
    $periodId = $filters['period_id'] ?? null;
    $sourceType = $filters['source_type'] ?? null;

    $connection = ServiceConnection::findOrFail($connectionId);

    // Build query scoped to this connection
    $query = CustomerLedger::with(['period', 'status', 'user'])
        ->where('connection_id', $connectionId);

    if ($periodId) {
        $query->where('period_id', $periodId);
    }

    if ($sourceType) {
        $query->where('source_type', $sourceType);
    }

    $entries = $query->orderBy('txn_date', 'desc')
        ->orderBy('post_ts', 'desc')
        ->orderBy('ledger_entry_id', 'desc')
        ->paginate($perPage);

    // Calculate running balances from ALL connection entries (oldest first)
    $allEntries = CustomerLedger::where('connection_id', $connectionId)
        ->orderBy('txn_date', 'asc')
        ->orderBy('post_ts', 'asc')
        ->orderBy('ledger_entry_id', 'asc')
        ->get();

    $runningBalances = [];
    $runningBalance = 0;
    foreach ($allEntries as $entry) {
        $runningBalance += ($entry->debit - $entry->credit);
        $runningBalances[$entry->ledger_entry_id] = $runningBalance;
    }

    // Format entries
    $entriesWithBalance = $entries->getCollection()->map(function ($entry) use ($runningBalances) {
        return $this->formatConnectionLedgerEntry($entry, $runningBalances[$entry->ledger_entry_id] ?? 0);
    });

    // Summary
    $totals = CustomerLedger::where('connection_id', $connectionId)
        ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
        ->first();

    $totalDebit = (float) ($totals->total_debit ?? 0);
    $totalCredit = (float) ($totals->total_credit ?? 0);
    $netBalance = $totalDebit - $totalCredit;

    // Filter options
    $periods = CustomerLedger::where('connection_id', $connectionId)
        ->whereNotNull('period_id')
        ->with('period')
        ->get()
        ->pluck('period')
        ->filter()
        ->unique('per_id')
        ->sortByDesc('per_id')
        ->map(fn ($period) => [
            'per_id' => $period->per_id,
            'label' => $period->per_month . ' ' . $period->per_year,
        ])
        ->values();

    $types = CustomerLedger::where('connection_id', $connectionId)
        ->distinct()
        ->pluck('source_type')
        ->map(fn ($type) => [
            'value' => $type,
            'label' => $this->getSourceTypeLabel($type),
        ]);

    return [
        'entries' => $entriesWithBalance,
        'pagination' => [
            'current_page' => $entries->currentPage(),
            'per_page' => $entries->perPage(),
            'total' => $entries->total(),
            'last_page' => $entries->lastPage(),
        ],
        'summary' => [
            'total_debit' => $totalDebit,
            'total_debit_formatted' => '₱' . number_format($totalDebit, 2),
            'total_credit' => $totalCredit,
            'total_credit_formatted' => '₱' . number_format($totalCredit, 2),
            'net_balance' => $netBalance,
            'net_balance_formatted' => '₱' . number_format($netBalance, 2),
            'balance_class' => $netBalance > 0 ? 'text-red-600' : ($netBalance < 0 ? 'text-blue-600' : 'text-green-600'),
        ],
        'filters' => [
            'periods' => $periods,
            'types' => $types,
        ],
    ];
}

private function formatConnectionLedgerEntry(CustomerLedger $entry, float $runningBalance): array
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
        'description' => $entry->description ?? '-',
        'debit' => (float) $entry->debit,
        'debit_formatted' => $entry->debit > 0 ? '₱' . number_format($entry->debit, 2) : '-',
        'credit' => (float) $entry->credit,
        'credit_formatted' => $entry->credit > 0 ? '₱' . number_format($entry->credit, 2) : '-',
        'running_balance' => $runningBalance,
        'running_balance_formatted' => '₱' . number_format($runningBalance, 2),
        'balance_class' => $runningBalance > 0 ? 'text-red-600' : ($runningBalance < 0 ? 'text-blue-600' : 'text-green-600'),
        'period' => $entry->period ? [
            'per_id' => $entry->period->per_id,
            'period_label' => $entry->period->per_month . ' ' . $entry->period->per_year,
        ] : null,
        'status' => $entry->status ? [
            'stat_desc' => $entry->status->stat_desc,
        ] : null,
    ];
}

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

private function getSourceTypeBadge(string $sourceType): array
{
    return match ($sourceType) {
        'BILL' => ['bg' => 'bg-blue-100 dark:bg-blue-900', 'text' => 'text-blue-800 dark:text-blue-300'],
        'CHARGE' => ['bg' => 'bg-orange-100 dark:bg-orange-900', 'text' => 'text-orange-800 dark:text-orange-300'],
        'PAYMENT' => ['bg' => 'bg-green-100 dark:bg-green-900', 'text' => 'text-green-800 dark:text-green-300'],
        'REFUND' => ['bg' => 'bg-purple-100 dark:bg-purple-900', 'text' => 'text-purple-800 dark:text-purple-300'],
        'ADJUST' => ['bg' => 'bg-yellow-100 dark:bg-yellow-900', 'text' => 'text-yellow-800 dark:text-yellow-300'],
        'WRITE_OFF' => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-800 dark:text-gray-300'],
        'REVERSAL' => ['bg' => 'bg-amber-100 dark:bg-amber-900', 'text' => 'text-amber-800 dark:text-amber-300'],
        default => ['bg' => 'bg-gray-100 dark:bg-gray-700', 'text' => 'text-gray-800 dark:text-gray-300'],
    };
}
```

**Step 4: Add the controller method**

In `app/Http/Controllers/ServiceConnection/ServiceConnectionController.php`, add:

```php
/**
 * Get connection ledger data (API)
 */
public function getLedger(Request $request, int $id): JsonResponse
{
    try {
        $data = $this->connectionService->getConnectionLedgerData($id, $request->all());

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 400);
    }
}
```

**Step 5: Add the route**

In `routes/web.php`, add after the existing connection routes (after the statement route, line ~196):

```php
Route::get('/customer/service-connection/{id}/ledger', [ServiceConnectionController::class, 'getLedger'])->name('service.connection.ledger');
```

**Step 6: Run test to verify it passes**

Run: `docker compose exec water_billing_app php artisan test --filter=ConnectionLedgerApiTest`
Expected: PASS

**Step 7: Commit**

```bash
git add app/Services/ServiceConnection/ServiceConnectionService.php app/Http/Controllers/ServiceConnection/ServiceConnectionController.php routes/web.php tests/Feature/Services/Connection/ConnectionLedgerApiTest.php
git commit -m "feat(connections): add connection-scoped ledger API endpoint"
```

---

## Task 4: Connection Ledger Tab — Blade View

**Files:**
- Create: `resources/views/pages/connection/tabs/ledger-tab.blade.php`
- Create: `resources/js/data/connection/connection-ledger-data.js`
- Modify: `resources/views/pages/connection/service-connection-detail.blade.php` (include the tab)

**Step 1: Create the ledger tab blade component**

Create `resources/views/pages/connection/tabs/ledger-tab.blade.php`. This is modeled after the customer ledger tab but without the "Connection" column (since we're already scoped to one connection) and without the connection filter:

```blade
<!-- Connection Ledger Tab Content -->
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center">
            <i class="fas fa-book mr-2 text-blue-600 dark:text-blue-400"></i>
            Transaction Ledger
        </h3>
    </div>

    <!-- Filter Bar -->
    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-4">
        <div class="flex flex-wrap gap-4 items-end">
            <!-- Period Filter -->
            <div class="flex-1 min-w-[200px]">
                <label for="conn-ledger-period-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Billing Period
                </label>
                <select id="conn-ledger-period-filter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500"
                    onchange="filterConnectionLedger()">
                    <option value="">All Periods</option>
                </select>
            </div>

            <!-- Type Filter -->
            <div class="flex-1 min-w-[200px]">
                <label for="conn-ledger-type-filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Transaction Type
                </label>
                <select id="conn-ledger-type-filter"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500 focus:border-blue-500"
                    onchange="filterConnectionLedger()">
                    <option value="">All Types</option>
                </select>
            </div>

            <!-- Reset Button -->
            <div>
                <button type="button" onclick="resetConnectionLedgerFilters()"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 rounded-lg transition-colors">
                    <i class="fas fa-undo mr-1"></i> Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Time</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Description</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Debit</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Credit</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Balance</th>
                </tr>
            </thead>
            <tbody id="conn-ledger-tbody" class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Loading ledger data...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div id="conn-ledger-pagination" class="px-4 py-3 bg-gray-50 dark:bg-gray-800 rounded-b-lg">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700 dark:text-gray-300">
                Showing <span id="conn-ledger-showing-start">0</span> to <span id="conn-ledger-showing-end">0</span> of <span id="conn-ledger-total">0</span> entries
            </div>
            <div class="flex gap-2" id="conn-ledger-pagination-buttons">
            </div>
        </div>
    </div>

    <!-- Summary Box -->
    <div class="mt-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-lg border border-blue-200 dark:border-gray-600 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Debits</p>
                <p class="text-xl font-bold text-red-600 dark:text-red-400" id="conn-ledger-total-debit">&#8369;0.00</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Credits</p>
                <p class="text-xl font-bold text-green-600 dark:text-green-400" id="conn-ledger-total-credit">&#8369;0.00</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Net Balance</p>
                <p class="text-xl font-bold" id="conn-ledger-net-balance">&#8369;0.00</p>
            </div>
        </div>
    </div>
</div>
```

**Step 2: Create the JavaScript file**

Create `resources/js/data/connection/connection-ledger-data.js`:

This file follows the same pattern as `resources/js/data/customer/customer-ledger-data.js` but uses `conn-ledger-` prefixed element IDs and the connection-specific API endpoint. It should include:

- `connLedgerState` (connectionId, currentPage, perPage, filters for period_id and source_type)
- `window.initializeConnectionLedgerTab(connectionId)` — initializes and loads data
- `loadConnectionLedgerData()` — fetches from `/customer/service-connection/{id}/ledger`
- `populateConnectionLedgerTable(entries)` — renders table rows with date grouping (same visual pattern as customer ledger but without Connection column)
- `populateConnectionLedgerSummary(summary)` — updates summary cards
- `populateConnectionLedgerPagination(pagination)` — renders pagination buttons
- `populateConnectionLedgerFilters(filters)` — populates period and type dropdowns (no connection dropdown)
- `window.filterConnectionLedger()` — reads filter values and reloads
- `window.resetConnectionLedgerFilters()` — resets filters and reloads
- `window.goToConnectionLedgerPage(page)` — pagination handler

Helper functions (`formatRelativeDate`, `formatTimeOnly`, `escapeHtml`) — define locally as `connLedger_` prefixed to avoid conflicts with customer ledger JS if both are loaded.

**Step 3: Include ledger tab in the connection detail page**

In `resources/views/pages/connection/service-connection-detail.blade.php`, add the ledger tab section after the Meter Readings section (after line ~306, before the Suspension/Disconnection Info section):

```blade
<!-- Transaction Ledger -->
@include('pages.connection.tabs.ledger-tab')
```

Also add the JS file. At the bottom of the `<script>` section (before `</x-app-layout>`), add the JS initialization:

```html
@vite(['resources/js/data/connection/connection-ledger-data.js'])
```

And in the `connectionDetail` Alpine `init()` method, add:

```javascript
// Initialize ledger tab
if (window.initializeConnectionLedgerTab && this.connection.id) {
    window.initializeConnectionLedgerTab(this.connection.id);
}
```

**Step 4: Verify visually**

Run: `npm run dev` (if not already running)
Navigate to a connection detail page in the browser.
Expected: The ledger tab appears with entries loaded via AJAX, filters work, pagination works.

**Step 5: Commit**

```bash
git add resources/views/pages/connection/tabs/ledger-tab.blade.php resources/js/data/connection/connection-ledger-data.js resources/views/pages/connection/service-connection-detail.blade.php
git commit -m "feat(connections): add interactive ledger tab to connection detail page"
```

---

## Task 5: Fix Balance Card — Include Application Fees

**Files:**
- Modify: `app/Services/ServiceConnection/ServiceConnectionService.php:267-295` (`getConnectionBalance`)
- Modify: `resources/views/pages/connection/service-connection-detail.blade.php:133-157` (balance card)

**Step 1: Write the failing test**

Create `tests/Feature/Services/Connection/ConnectionBalanceTest.php`:

```php
<?php

use App\Models\Customer;
use App\Models\ConsumerAddress;
use App\Models\CustomerLedger;
use App\Models\ServiceConnection;
use App\Models\Status;
use App\Services\ServiceConnection\ServiceConnectionService;

beforeEach(function () {
    $this->seed(\Database\Seeders\StatusSeeder::class);
    $this->seed(\Database\Seeders\AccountTypeSeeder::class);
    $this->seed(\Database\Seeders\BarangaySeeder::class);
    $this->seed(\Database\Seeders\PurokSeeder::class);
});

it('includes application fee charges in connection balance', function () {
    $customer = Customer::factory()->create();
    $address = ConsumerAddress::factory()->create(['cust_id' => $customer->cust_id]);
    $connection = ServiceConnection::create([
        'account_no' => 'TEST-BAL-001',
        'customer_id' => $customer->cust_id,
        'address_id' => $address->ca_id,
        'account_type_id' => 1,
        'started_at' => now(),
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $activeStatusId = Status::getIdByDescription(Status::ACTIVE);

    // Application fee charge (connection_id set after backfill)
    CustomerLedger::create([
        'customer_id' => $customer->cust_id,
        'connection_id' => $connection->connection_id,
        'txn_date' => now()->subDays(10)->toDateString(),
        'post_ts' => now()->subDays(10),
        'source_type' => 'CHARGE',
        'source_id' => 1,
        'source_line_no' => 1,
        'description' => 'Connection Fee',
        'debit' => 500.00,
        'credit' => 0,
        'user_id' => 1,
        'stat_id' => $activeStatusId,
    ]);

    // Water bill
    CustomerLedger::create([
        'customer_id' => $customer->cust_id,
        'connection_id' => $connection->connection_id,
        'txn_date' => now()->toDateString(),
        'post_ts' => now(),
        'source_type' => 'BILL',
        'source_id' => 1,
        'source_line_no' => 1,
        'description' => 'Water Bill',
        'debit' => 1000.00,
        'credit' => 0,
        'user_id' => 1,
        'stat_id' => $activeStatusId,
    ]);

    // Payment
    CustomerLedger::create([
        'customer_id' => $customer->cust_id,
        'connection_id' => $connection->connection_id,
        'txn_date' => now()->toDateString(),
        'post_ts' => now(),
        'source_type' => 'PAYMENT',
        'source_id' => 1,
        'source_line_no' => 1,
        'description' => 'Payment',
        'debit' => 0,
        'credit' => 500.00,
        'user_id' => 1,
        'stat_id' => $activeStatusId,
    ]);

    $service = app(ServiceConnectionService::class);
    $balance = $service->getConnectionBalance($connection->connection_id);

    expect($balance['total_bills'])->toBe(1000.0);
    expect($balance['total_charges'])->toBe(500.0);
    expect($balance['total_payments'])->toBe(500.0);
    expect($balance['balance'])->toBe(1000.0); // 1000 + 500 - 500
});
```

**Step 2: Run test to verify it passes**

This test should already pass with the current `getConnectionBalance()` implementation since it already sums BILL debits, CHARGE debits, and all credits. But we need to verify the balance card in the Blade template references the correct keys.

Run: `docker compose exec water_billing_app php artisan test --filter=ConnectionBalanceTest`
Expected: PASS

**Step 3: Fix the balance card display**

The balance card in `service-connection-detail.blade.php` references `balance.total_billed` and `balance.total_paid`, but the service returns `total_bills`, `total_charges`, `total_payments`. Update the balance card to show all four values:

In the `@php` block at top (line 19), update:
```php
$balanceData = $balance ?? ['total_bills' => 0, 'total_charges' => 0, 'total_payments' => 0, 'balance' => 0];
```

In the balance card section (lines 133-157), update the Alpine template to show:
- Total Billed (bills + charges combined)
- Total Paid
- Outstanding Balance

Update the `formatCurrency` references in the balance card:
```blade
<span ... x-text="formatCurrency((balance.total_bills || 0) + (balance.total_charges || 0))"></span>
```
and:
```blade
<span ... x-text="formatCurrency(balance.total_payments)"></span>
```

**Step 4: Commit**

```bash
git add resources/views/pages/connection/service-connection-detail.blade.php tests/Feature/Services/Connection/ConnectionBalanceTest.php
git commit -m "fix(connections): fix balance card to show correct keys and include charges"
```

---

## Task 6: Enhance Print Statement — Include Application Fees

**Files:**
- Modify: `app/Services/ServiceConnection/ServiceConnectionService.php:363-370` (`getStatementLedgerEntries`)
- Modify: `resources/views/pages/connection/service-connection-statement.blade.php:303-318` (summary cards)

Since the backfill (Task 2) already sets `connection_id` on application fee ledger entries, the existing `getStatementLedgerEntries()` query (`WHERE connection_id = X`) will now automatically include them. The only changes needed are:

**Step 1: Update the statement's balance summary cards**

The statement page references `$balance['total_billed']` and `$balance['total_paid']` which don't exist in the service response. Fix to match the actual keys from `getConnectionBalance()`:

In `service-connection-statement.blade.php` lines 303-318, update:
```blade
<div class="summary-cards">
    <div class="summary-card">
        <div class="summary-card-label">Total Billed</div>
        <div class="summary-card-value">{{ number_format(($balance['total_bills'] ?? 0) + ($balance['total_charges'] ?? 0), 2) }}</div>
    </div>
    <div class="summary-card">
        <div class="summary-card-label">Total Paid</div>
        <div class="summary-card-value positive">{{ number_format($balance['total_payments'] ?? 0, 2) }}</div>
    </div>
    <div class="summary-card">
        <div class="summary-card-label">Outstanding Balance</div>
        <div class="summary-card-value {{ ($balance['balance'] ?? 0) > 0 ? 'negative' : 'positive' }}">
            {{ number_format($balance['balance'] ?? 0, 2) }}
        </div>
    </div>
</div>
```

**Step 2: Add REVERSAL type badge to statement**

The statement page handles BILL, PAYMENT, CHARGE, ADJUSTMENT type badges but not REVERSAL. Add it:

In the `$typeClass` match block (~line 343-349), add REVERSAL:
```php
'REVERSAL' => 'type-charge',
```

In the `$typeLabel` match block (~line 351-356), add REVERSAL:
```php
str_contains(strtoupper($entry->source_type ?? ''), 'REVERSAL') => 'REVERSAL',
```

Add CSS for reversal badge (in `<style>` section):
```css
.type-reversal {
    background: #fef3c7;
    color: #92400e;
}
```

**Step 3: Remove the 50-entry limit**

The `getStatementLedgerEntries()` method limits to 50 entries. For a complete account statement, remove or increase this limit:

In `ServiceConnectionService.php`, change `getStatementLedgerEntries`:
```php
public function getStatementLedgerEntries(int $connectionId, int $limit = 200): Collection
```

**Step 4: Verify visually**

Navigate to a connection's account statement print page.
Expected: Application fees now appear in the transaction history. Summary cards show correct totals.

**Step 5: Commit**

```bash
git add resources/views/pages/connection/service-connection-statement.blade.php app/Services/ServiceConnection/ServiceConnectionService.php
git commit -m "fix(statement): include application fees and fix balance display keys"
```

---

## Task 7: Run Full Test Suite

**Step 1: Run all tests**

Run: `docker compose exec water_billing_app php artisan test`
Expected: All tests pass, including the new ones from this feature.

**Step 2: Run Pint for code formatting**

Run: `docker compose exec water_billing_app ./vendor/bin/pint`

**Step 3: Final commit if pint made changes**

```bash
git add -A
git commit -m "chore: format code with pint"
```

---

## Summary of Changes

| Area | Change | Files |
|------|--------|-------|
| **Bug Fix** | Wire `transferChargesToConnection()` into `createFromApplication()` | `ServiceConnectionService`, `ApplicationChargeService` |
| **Data Migration** | Backfill `connection_id` on existing charges and ledger entries | New migration |
| **API** | Connection-scoped ledger endpoint with pagination, filters, running balances | `ServiceConnectionService`, `ServiceConnectionController`, `routes/web.php` |
| **UI — Ledger Tab** | Interactive ledger tab on connection detail page | New Blade tab, new JS file, modified detail page |
| **UI — Balance Card** | Fix balance card to show correct keys (total_bills, total_charges, total_payments) | Connection detail Blade |
| **UI — Print Statement** | Fix summary card keys, add REVERSAL badge, application fees now included automatically | Statement Blade |
| **Tests** | Transfer charges test, ledger API test, balance test | 3 new test files |
