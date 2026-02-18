# Global Customer Search Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add a live search dropdown to the header navigation that lets staff quickly find customers by name, customer code (resolution_no), or meter number, linking directly to the customer profile.

**Architecture:** An Alpine.js component in the header fires debounced API requests to a dedicated search endpoint. The backend uses MySQL FULLTEXT indexes on customer name columns and LIKE prefix matching on resolution_no, account_no, and meter serial. Results are joined across customer → ServiceConnection → MeterAssignment → meter tables, limited to 10, and returned as JSON.

**Tech Stack:** Laravel 12, MySQL 8 FULLTEXT, Alpine.js, Tailwind CSS, Flowbite

---

### Task 1: Add database indexes for search performance

**Files:**
- Create: `database/migrations/2026_02_17_000001_add_fulltext_and_search_indexes.php`

**Step 1: Create the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // FULLTEXT index on customer name columns for natural language search
        DB::statement('ALTER TABLE customer ADD FULLTEXT INDEX customer_fulltext_name (cust_first_name, cust_last_name)');

        // Regular index on resolution_no for prefix LIKE search
        Schema::table('customer', function (Blueprint $table) {
            $table->index('resolution_no', 'customer_resolution_no_index');
        });

        // Index on MeterAssignment join columns for faster joins
        Schema::table('MeterAssignment', function (Blueprint $table) {
            $table->index('connection_id', 'meter_assignment_connection_id_index');
            $table->index('meter_id', 'meter_assignment_meter_id_index');
        });
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE customer DROP INDEX customer_fulltext_name');

        Schema::table('customer', function (Blueprint $table) {
            $table->dropIndex('customer_resolution_no_index');
        });

        Schema::table('MeterAssignment', function (Blueprint $table) {
            $table->dropIndex('meter_assignment_connection_id_index');
            $table->dropIndex('meter_assignment_meter_id_index');
        });
    }
};
```

**Step 2: Run migration**

Run: `php artisan migrate`
Expected: Migration runs successfully, no errors.

**Step 3: Commit**

```bash
git add database/migrations/2026_02_17_000001_add_fulltext_and_search_indexes.php
git commit -m "feat(search): add fulltext and search indexes for global customer search"
```

---

### Task 2: Create CustomerSearchService

**Files:**
- Create: `app/Services/Search/CustomerSearchService.php`

**Step 1: Write the failing test**

Create: `tests/Feature/Services/CustomerSearchServiceTest.php`

```php
<?php

use App\Models\Customer;
use App\Models\ConsumerAddress;
use App\Models\ServiceConnection;
use App\Models\MeterAssignment;
use App\Models\Meter;
use App\Models\Status;
use App\Services\Search\CustomerSearchService;

beforeEach(function () {
    $this->service = app(CustomerSearchService::class);
});

it('returns empty array for queries shorter than 2 characters', function () {
    $results = $this->service->search('a');
    expect($results)->toBeArray()->toBeEmpty();
});

it('finds customers by first name', function () {
    $status = Status::factory()->create(['stat_desc' => 'ACTIVE']);
    $address = ConsumerAddress::factory()->create(['stat_id' => $status->stat_id]);

    $customer = Customer::factory()->create([
        'cust_first_name' => 'Juan',
        'cust_last_name' => 'Dela Cruz',
        'ca_id' => $address->ca_id,
        'stat_id' => $status->stat_id,
    ]);

    $results = $this->service->search('Juan');
    expect($results)->toHaveCount(1);
    expect($results[0]['customer_id'])->toBe($customer->cust_id);
    expect($results[0]['name'])->toContain('Juan');
});

it('finds customers by resolution number', function () {
    $status = Status::factory()->create(['stat_desc' => 'ACTIVE']);
    $address = ConsumerAddress::factory()->create(['stat_id' => $status->stat_id]);

    $customer = Customer::factory()->create([
        'cust_first_name' => 'Maria',
        'cust_last_name' => 'Santos',
        'resolution_no' => 'INITAO-MS-1234567890',
        'ca_id' => $address->ca_id,
        'stat_id' => $status->stat_id,
    ]);

    $results = $this->service->search('INITAO-MS');
    expect($results)->toHaveCount(1);
    expect($results[0]['resolution_no'])->toBe('INITAO-MS-1234567890');
});

it('finds customers by meter serial number', function () {
    $status = Status::factory()->create(['stat_desc' => 'ACTIVE']);
    $address = ConsumerAddress::factory()->create(['stat_id' => $status->stat_id]);

    $customer = Customer::factory()->create([
        'cust_first_name' => 'Pedro',
        'cust_last_name' => 'Reyes',
        'ca_id' => $address->ca_id,
        'stat_id' => $status->stat_id,
    ]);

    $connection = ServiceConnection::factory()->create([
        'customer_id' => $customer->cust_id,
        'address_id' => $address->ca_id,
        'stat_id' => $status->stat_id,
    ]);

    $meter = Meter::factory()->create([
        'mtr_serial' => 'MTR-2024-999',
        'stat_id' => $status->stat_id,
    ]);

    MeterAssignment::factory()->create([
        'connection_id' => $connection->connection_id,
        'meter_id' => $meter->mtr_id,
        'installed_at' => now(),
    ]);

    $results = $this->service->search('MTR-2024-999');
    expect($results)->toHaveCount(1);
    expect($results[0]['meter_serial'])->toBe('MTR-2024-999');
});

it('limits results to 10', function () {
    $status = Status::factory()->create(['stat_desc' => 'ACTIVE']);
    $address = ConsumerAddress::factory()->create(['stat_id' => $status->stat_id]);

    Customer::factory()->count(15)->create([
        'cust_first_name' => 'TestName',
        'cust_last_name' => 'User',
        'ca_id' => $address->ca_id,
        'stat_id' => $status->stat_id,
    ]);

    $results = $this->service->search('TestName');
    expect($results)->toHaveCount(10);
});

it('returns correct response shape', function () {
    $status = Status::factory()->create(['stat_desc' => 'ACTIVE']);
    $address = ConsumerAddress::factory()->create(['stat_id' => $status->stat_id]);

    $customer = Customer::factory()->create([
        'cust_first_name' => 'Ana',
        'cust_last_name' => 'Garcia',
        'resolution_no' => 'INITAO-AG-111',
        'ca_id' => $address->ca_id,
        'stat_id' => $status->stat_id,
    ]);

    $connection = ServiceConnection::factory()->create([
        'customer_id' => $customer->cust_id,
        'address_id' => $address->ca_id,
        'account_no' => 'ACC-00001',
        'stat_id' => $status->stat_id,
    ]);

    $meter = Meter::factory()->create([
        'mtr_serial' => 'MTR-001',
        'stat_id' => $status->stat_id,
    ]);

    MeterAssignment::factory()->create([
        'connection_id' => $connection->connection_id,
        'meter_id' => $meter->mtr_id,
        'installed_at' => now(),
    ]);

    $results = $this->service->search('Ana');
    expect($results[0])->toHaveKeys([
        'customer_id', 'name', 'resolution_no', 'account_no',
        'meter_serial', 'barangay', 'status',
    ]);
});
```

**Step 2: Run test to verify it fails**

Run: `php artisan test --filter=CustomerSearchServiceTest`
Expected: FAIL — class `CustomerSearchService` not found.

**Note:** These tests require factories for Customer, ConsumerAddress, ServiceConnection, Meter, MeterAssignment, and Status. If factories don't exist, create minimal ones first. Check `database/factories/` directory before running.

**Step 3: Write the service**

Create: `app/Services/Search/CustomerSearchService.php`

```php
<?php

namespace App\Services\Search;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;

class CustomerSearchService
{
    public function search(string $query): array
    {
        if (strlen(trim($query)) < 2) {
            return [];
        }

        $query = trim($query);

        $customers = Customer::query()
            ->select('customer.cust_id', 'customer.cust_first_name', 'customer.cust_middle_name', 'customer.cust_last_name', 'customer.resolution_no', 'customer.ca_id', 'customer.stat_id')
            ->with([
                'status:stat_id,stat_desc',
                'address.barangay:b_id,b_desc',
                'serviceConnections' => function ($q) {
                    $q->select('connection_id', 'customer_id', 'account_no')
                        ->with(['meterAssignment' => function ($q) {
                            $q->select('assignment_id', 'connection_id', 'meter_id')
                                ->with('meter:mtr_id,mtr_serial');
                        }]);
                },
            ])
            ->where(function (Builder $q) use ($query) {
                // FULLTEXT search on name columns
                $q->whereRaw(
                    'MATCH(cust_first_name, cust_last_name) AGAINST(? IN BOOLEAN MODE)',
                    [$query . '*']
                )
                // Fallback LIKE for partial/middle name matches
                ->orWhere('cust_first_name', 'like', "{$query}%")
                ->orWhere('cust_last_name', 'like', "{$query}%")
                // Code/serial lookups (prefix match)
                ->orWhere('resolution_no', 'like', "{$query}%")
                // Search by account_no or meter serial via relationships
                ->orWhereHas('serviceConnections', function (Builder $sq) use ($query) {
                    $sq->where('account_no', 'like', "{$query}%")
                        ->orWhereHas('meterAssignment', function (Builder $mq) use ($query) {
                            $mq->whereHas('meter', function (Builder $mtq) use ($query) {
                                $mtq->where('mtr_serial', 'like', "{$query}%");
                            });
                        });
                });
            })
            ->limit(10)
            ->get();

        return $customers->map(function ($customer) {
            $connection = $customer->serviceConnections->first();
            $meter = $connection?->meterAssignment?->meter;
            $barangay = $customer->address?->barangay;

            return [
                'customer_id' => $customer->cust_id,
                'name' => trim("{$customer->cust_first_name} {$customer->cust_middle_name} {$customer->cust_last_name}"),
                'resolution_no' => $customer->resolution_no ?? '',
                'account_no' => $connection?->account_no ?? '',
                'meter_serial' => $meter?->mtr_serial ?? '',
                'barangay' => $barangay?->b_desc ?? '',
                'status' => $customer->status?->stat_desc ?? '',
            ];
        })->toArray();
    }
}
```

**Step 4: Run test to verify it passes**

Run: `php artisan test --filter=CustomerSearchServiceTest`
Expected: All tests PASS.

**Step 5: Commit**

```bash
git add app/Services/Search/CustomerSearchService.php tests/Feature/Services/CustomerSearchServiceTest.php
git commit -m "feat(search): add CustomerSearchService with fulltext search"
```

---

### Task 3: Create API endpoint and route

**Files:**
- Create: `app/Http/Controllers/Search/CustomerSearchController.php`
- Modify: `routes/web.php` (add route inside auth middleware group, near line 105 after notifications API routes)

**Step 1: Write the failing test**

Create: `tests/Feature/Http/CustomerSearchControllerTest.php`

```php
<?php

use App\Models\User;
use App\Models\Customer;
use App\Models\ConsumerAddress;
use App\Models\Status;

it('requires authentication', function () {
    $this->getJson('/api/search/customers?q=test')
        ->assertStatus(401);
});

it('returns results for valid query', function () {
    $user = User::factory()->create();
    $status = Status::factory()->create(['stat_desc' => 'ACTIVE']);
    $address = ConsumerAddress::factory()->create(['stat_id' => $status->stat_id]);

    Customer::factory()->create([
        'cust_first_name' => 'SearchTest',
        'cust_last_name' => 'User',
        'ca_id' => $address->ca_id,
        'stat_id' => $status->stat_id,
    ]);

    $this->actingAs($user)
        ->getJson('/api/search/customers?q=SearchTest')
        ->assertOk()
        ->assertJsonCount(1);
});

it('returns empty array for short query', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson('/api/search/customers?q=a')
        ->assertOk()
        ->assertJsonCount(0);
});

it('returns empty array for missing query', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson('/api/search/customers')
        ->assertOk()
        ->assertJsonCount(0);
});
```

**Step 2: Run test to verify it fails**

Run: `php artisan test --filter=CustomerSearchControllerTest`
Expected: FAIL — 404 (route not defined).

**Step 3: Create the controller**

Create: `app/Http/Controllers/Search/CustomerSearchController.php`

```php
<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Services\Search\CustomerSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerSearchController extends Controller
{
    public function __construct(
        private CustomerSearchService $searchService
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $query = $request->input('q', '');
        $results = $this->searchService->search($query);

        return response()->json($results);
    }
}
```

**Step 4: Register the route**

In `routes/web.php`, add inside the `Route::middleware('auth')` group, after the notifications API block (after line 105):

```php
    // -------------------------------------------------------------------------
    // Global Search - All authenticated users
    // -------------------------------------------------------------------------
    Route::get('/api/search/customers', \App\Http\Controllers\Search\CustomerSearchController::class)->name('api.search.customers');
```

**Step 5: Run test to verify it passes**

Run: `php artisan test --filter=CustomerSearchControllerTest`
Expected: All tests PASS.

**Step 6: Commit**

```bash
git add app/Http/Controllers/Search/CustomerSearchController.php routes/web.php tests/Feature/Http/CustomerSearchControllerTest.php
git commit -m "feat(search): add global customer search API endpoint"
```

---

### Task 4: Build Alpine.js search dropdown in header

**Files:**
- Modify: `resources/views/layouts/navigation.blade.php` (lines 153-165, replace the static search bar)

**Step 1: Replace the static search input with the Alpine.js search component**

Replace lines 153-165 in `resources/views/layouts/navigation.blade.php` (the `<!-- Search Bar -->` block) with:

```html
                <!-- Global Customer Search -->
                <div x-data="globalSearch()" x-on:click.outside="close()" x-on:keydown.escape.window="close()" class="relative hidden lg:block">
                    <!-- Search Input -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 dark:text-gray-500">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            x-ref="searchInput"
                            x-model="query"
                            x-on:input.debounce.300ms="search()"
                            x-on:focus="if (results.length) open = true"
                            x-on:keydown.arrow-down.prevent="moveDown()"
                            x-on:keydown.arrow-up.prevent="moveUp()"
                            x-on:keydown.enter.prevent="selectCurrent()"
                            type="text"
                            placeholder="Search customers..."
                            class="pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-[#111826] border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all duration-200 w-72 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                        >
                        <!-- Loading Spinner -->
                        <div x-show="loading" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Results Dropdown -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                         class="absolute top-full left-0 mt-1 w-96 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50 max-h-96 overflow-y-auto">

                        <!-- No Results -->
                        <template x-if="!loading && results.length === 0 && query.length >= 2 && searched">
                            <div class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                No customers found for "<span x-text="query" class="font-medium"></span>"
                            </div>
                        </template>

                        <!-- Results List -->
                        <template x-for="(result, index) in results" :key="result.customer_id">
                            <a :href="'/customer/details/' + result.customer_id"
                               :class="{ 'bg-blue-50 dark:bg-blue-900/30': selectedIndex === index }"
                               class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 last:border-0 transition-colors"
                               x-on:mouseenter="selectedIndex = index">
                                <div class="flex items-center justify-between">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="result.name"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                                            <span x-show="result.account_no" x-text="result.account_no"></span>
                                            <span x-show="result.account_no && result.meter_serial"> &middot; </span>
                                            <span x-show="result.meter_serial" x-text="result.meter_serial"></span>
                                            <span x-show="(result.account_no || result.meter_serial) && result.barangay"> &middot; </span>
                                            <span x-show="result.barangay" x-text="result.barangay"></span>
                                        </p>
                                    </div>
                                    <span x-show="result.status"
                                          :class="{
                                              'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': result.status === 'ACTIVE',
                                              'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400': result.status !== 'ACTIVE'
                                          }"
                                          class="ml-2 px-2 py-0.5 text-xs font-medium rounded-full flex-shrink-0"
                                          x-text="result.status">
                                    </span>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
```

**Step 2: Add the Alpine.js component function**

Add the following `<script>` block at the bottom of `navigation.blade.php`, before the closing tag or in an existing script section. If there is already Alpine data in the file, place this alongside it:

```html
<script>
function globalSearch() {
    return {
        query: '',
        results: [],
        open: false,
        loading: false,
        searched: false,
        selectedIndex: -1,

        async search() {
            if (this.query.length < 2) {
                this.results = [];
                this.open = false;
                this.searched = false;
                return;
            }

            this.loading = true;
            try {
                const response = await fetch(`/api/search/customers?q=${encodeURIComponent(this.query)}`);
                this.results = await response.json();
                this.open = true;
                this.searched = true;
                this.selectedIndex = -1;
            } catch (error) {
                console.error('Search failed:', error);
                this.results = [];
            } finally {
                this.loading = false;
            }
        },

        close() {
            this.open = false;
            this.selectedIndex = -1;
        },

        moveDown() {
            if (this.selectedIndex < this.results.length - 1) {
                this.selectedIndex++;
            }
        },

        moveUp() {
            if (this.selectedIndex > 0) {
                this.selectedIndex--;
            }
        },

        selectCurrent() {
            if (this.selectedIndex >= 0 && this.results[this.selectedIndex]) {
                window.location.href = '/customer/details/' + this.results[this.selectedIndex].customer_id;
            }
        },
    };
}
</script>
```

**Step 3: Manually test in browser**

1. Run: `composer dev` (or `php artisan serve` + `npm run dev`)
2. Log in and verify the search bar appears in the header
3. Type a customer name — results should appear in dropdown
4. Click a result — should navigate to customer details page
5. Test keyboard navigation (arrow keys, Enter, Escape)
6. Type a short query (1 char) — no dropdown should appear
7. Type a non-existent name — "No customers found" message should appear

**Step 4: Commit**

```bash
git add resources/views/layouts/navigation.blade.php
git commit -m "feat(search): add live customer search dropdown to header navigation"
```

---

### Task 5: Add keyboard shortcut to focus search

**Files:**
- Modify: `resources/views/layouts/navigation.blade.php` (add to the globalSearch Alpine component)

**Step 1: Add the keyboard shortcut listener**

In the `globalSearch()` function's `init()` method (or add an `init()` method if not present), add a global keyboard listener for `/` key:

Update the `globalSearch()` function to include an `init()` method:

```javascript
function globalSearch() {
    return {
        // ... existing properties ...

        init() {
            document.addEventListener('keydown', (e) => {
                // Focus search on "/" key press (unless typing in an input/textarea)
                if (e.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
                    e.preventDefault();
                    this.$refs.searchInput.focus();
                }
            });
        },

        // ... existing methods ...
    };
}
```

**Step 2: Manually verify**

1. Press `/` on any page — search input should focus
2. Verify it does NOT trigger when typing in other input fields

**Step 3: Commit**

```bash
git add resources/views/layouts/navigation.blade.php
git commit -m "feat(search): add '/' keyboard shortcut to focus search bar"
```

---

### Task 6: Run full test suite and format code

**Files:**
- All new and modified files

**Step 1: Run Laravel Pint**

Run: `./vendor/bin/pint`
Expected: Files formatted, no errors.

**Step 2: Run full test suite**

Run: `php artisan test`
Expected: All tests pass, including new search tests.

**Step 3: Final commit if Pint made changes**

```bash
git add -A
git commit -m "chore: format code with Laravel Pint"
```

---

## Summary of All Files

**New files (4):**
- `database/migrations/2026_02_17_000001_add_fulltext_and_search_indexes.php`
- `app/Services/Search/CustomerSearchService.php`
- `app/Http/Controllers/Search/CustomerSearchController.php`
- `tests/Feature/Services/CustomerSearchServiceTest.php`
- `tests/Feature/Http/CustomerSearchControllerTest.php`

**Modified files (2):**
- `routes/web.php` — add search API route
- `resources/views/layouts/navigation.blade.php` — replace static search with Alpine.js component
