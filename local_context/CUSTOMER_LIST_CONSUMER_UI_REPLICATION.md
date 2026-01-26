# Customer List - Complete Consumer UI/UX Replication Plan

**Objective**: Replicate every single UI/UX element from the consumer list into the customer list, including the datatable and details page.

**Status**: Planning Phase
**Created**: 2026-01-25
**Branch**: admin-config-dev

---

## Table of Contents

1. [Current State Analysis](#current-state-analysis)
2. [Consumer List UI/UX Inventory](#consumer-list-uiux-inventory)
3. [Customer List Current Implementation](#customer-list-current-implementation)
4. [Gap Analysis](#gap-analysis)
5. [Implementation Plan](#implementation-plan)
6. [Phase 1: List Page Header & Stats](#phase-1-list-page-header--stats)
7. [Phase 2: List Page Table Structure](#phase-2-list-page-table-structure)
8. [Phase 3: List Page JavaScript Functionality](#phase-3-list-page-javascript-functionality)
9. [Phase 4: Details Page Structure](#phase-4-details-page-structure)
10. [Phase 5: Details Page Profile Cards](#phase-5-details-page-profile-cards)
11. [Phase 6: Details Page Tabs](#phase-6-details-page-tabs)
12. [Phase 7: Details Page JavaScript](#phase-7-details-page-javascript)
13. [Testing & Verification](#testing--verification)

---

## Current State Analysis

### Consumer List Implementation Files
- **View**: `resources/views/pages/consumer/consumer-list.blade.php`
- **JavaScript**: `resources/js/data/consumer/consumer-list.js`
- **Details View**: `resources/views/pages/consumer/consumer-details.blade.php`
- **Details JS**: `resources/js/data/consumer/consumer-details.js`
- **Tab Files**:
  - `resources/views/pages/consumer/tabs/documents-tab.blade.php`
  - `resources/views/pages/consumer/tabs/connection-tabs.blade.php`
  - `resources/views/pages/consumer/tabs/ledger-tab.blade.php`

### Customer List Implementation Files
- **View**: `resources/views/pages/customer/customer-list.blade.php`
- **JavaScript**: `resources/js/data/customer/customer-list-simple.js`
- **Details View**: `resources/views/pages/customer/customer-details.blade.php`
- **Details JS**: `resources/js/data/customer/customer-details-data.js`
- **Service**: `app/Services/Customers/CustomerService.php`
- **Controller**: `app/Http/Controllers/Customer/CustomerController.php`

---

## Consumer List UI/UX Inventory

### List Page Elements

#### 1. Page Structure
```blade
<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
```

#### 2. Page Header
```blade
<x-ui.page-header
    title="Customer List"
    icon="fas fa-users">
</x-ui.page-header>
```

#### 3. Stats Cards (4 Cards)
```blade
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <x-ui.stat-card title="Total Customers" value="15" icon="fas fa-user" />
    <x-ui.stat-card title="Residential Type" value="12" icon="fas fa-home" />
    <x-ui.stat-card title="Total Current Bill" value="₱45,200" icon="fas fa-file-invoice-dollar" />
    <x-ui.stat-card title="Overdue" value="3" icon="fas fa-exclamation-triangle" />
</div>
```

**Stats Cards Specs**:
- Uses `x-ui.stat-card` component
- 4 cards in responsive grid
- Icons: user, home, file-invoice-dollar, exclamation-triangle
- Dynamic values from backend

#### 4. Action Functions Bar
```blade
<x-ui.action-functions
    searchPlaceholder="Search customer..."
    filterLabel="All Status"
    :filterOptions="[
        ['value' => 'Active', 'label' => 'Active'],
        ['value' => 'Pending', 'label' => 'Pending'],
        ['value' => 'Overdue', 'label' => 'Overdue'],
        ['value' => 'Inactive', 'label' => 'Inactive']
    ]"
    :showDateFilter="false"
    :showExport="true"
    tableId="consumer-documents-tbody"
/>
```

**Action Functions Specs**:
- Search input with placeholder
- Status filter dropdown
- Export button enabled
- No date filter
- Connected to tbody via tableId

#### 5. Table Container
```blade
<div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm bg-white dark:bg-gray-800">
    <div class="overflow-x-auto">
        <table class="min-w-full" id="consumer-documents-table">
```

**Table Container Specs**:
- `rounded-xl` border radius
- Border: `border-gray-200 dark:border-gray-700`
- Shadow: `shadow-sm`
- Background: `bg-white dark:bg-gray-800`
- Nested overflow-x-auto wrapper

#### 6. Table Headers (6 Columns)
```blade
<thead>
    <tr class="bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Consumer</th>
        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Address & Type</th>
        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Meter No</th>
        <th class="px-4 py-3.5 text-right text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Current Bill</th>
        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Actions</th>
    </tr>
</thead>
```

**Table Header Specs**:
- Background: `bg-gray-100 dark:bg-gray-800`
- Border bottom: `border-b border-gray-200 dark:border-gray-700`
- Padding: `px-4 py-3.5`
- Font: `text-xs font-semibold text-gray-700 dark:text-gray-200`
- Text transform: `uppercase tracking-wider`
- Alignments: left (3), right (1), center (2)

#### 7. Table Body
```blade
<tbody class="divide-y divide-gray-100 dark:divide-gray-700" id="consumer-documents-tbody">
    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
```

**Table Body Specs**:
- Divider: `divide-y divide-gray-100 dark:divide-gray-700`
- Row hover: `hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors`

#### 8. Pagination
```blade
<div class="flex justify-between items-center mt-4 flex-wrap gap-4">
    <!-- Left: Page size selector -->
    <div class="flex items-center gap-2">
        <span class="text-sm text-gray-600 dark:text-gray-400">Show</span>
        <select id="consumerPageSize" onchange="consumerPagination.updatePageSize(this.value)"
                class="text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="20">20</option>
            <option value="50">50</option>
        </select>
        <span class="text-sm text-gray-600 dark:text-gray-400">entries</span>
    </div>

    <!-- Center: Navigation buttons -->
    <div class="flex items-center gap-2">
        <button id="consumerPrevBtn" onclick="consumerPagination.prevPage()"
                class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
            <i class="fas fa-chevron-left mr-1"></i>Previous
        </button>
        <div class="text-sm text-gray-700 dark:text-gray-300 px-3 font-medium">
            Page <span id="consumerCurrentPage">1</span> of <span id="consumerTotalPages">1</span>
        </div>
        <button id="consumerNextBtn" onclick="consumerPagination.nextPage()"
                class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
            Next<i class="fas fa-chevron-right ml-1"></i>
        </button>
    </div>

    <!-- Right: Total records -->
    <div class="text-sm text-gray-600 dark:text-gray-400">
        Showing <span class="font-semibold text-gray-900 dark:text-white" id="consumerTotalRecords">0</span> results
    </div>
</div>
```

**Pagination Specs**:
- 3-section layout: left (page size), center (navigation), right (total)
- Chevron icons in buttons
- Disabled state styling
- Focus ring on select
- All responsive with `flex-wrap gap-4`

### List Page Table Row Structure (JavaScript)

```javascript
<td class="px-4 py-3">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
            ${getInitials(consumer.name)}
        </div>
        <div>
            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">${consumer.name}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400">ID: ${consumer.cust_id}</div>
        </div>
    </div>
</td>
<td class="px-4 py-3">
    <div class="text-sm text-gray-900 dark:text-gray-100">${consumer.address}</div>
</td>
<td class="px-4 py-3">
    <div class="text-sm font-mono text-gray-900 dark:text-gray-100">${consumer.meter_no}</div>
</td>
<td class="px-4 py-3 text-right">
    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">${consumer.total_bill}</div>
</td>
<td class="px-4 py-3 text-center">
    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full ${statusColor}">
        ${consumer.status}
    </span>
</td>
<td class="px-4 py-3 text-center">
    <div class="flex justify-center gap-2">
        <a href="/consumer/${consumer.cust_id}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors" title="View Details">
            <i class="fas fa-eye"></i>
        </a>
    </div>
</td>
```

**Row Cell Specs**:
1. **Consumer Cell**: Avatar (w-10 h-10, gradient blue-purple) + Name (font-medium) + ID (text-xs)
2. **Address Cell**: Simple text-sm
3. **Meter Cell**: font-mono text-sm
4. **Bill Cell**: text-right, font-semibold
5. **Status Cell**: text-center, badge with dynamic color
6. **Actions Cell**: text-center, eye icon only

---

## Details Page UI/UX Inventory

### 1. Page Structure
```blade
<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
```

### 2. Page Header with Back Button
```blade
<x-ui.page-header
    title="Consumer Details"
    subtitle="View consumer information and history">
    <x-slot name="actions">
        <x-ui.button variant="outline" href="{{ route('consumer.list') }}">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </x-ui.button>
    </x-slot>
</x-ui.page-header>
```

**Header Specs**:
- Title + subtitle
- Actions slot with Back button
- Button variant: outline
- Icon: arrow-left

### 3. Profile Cards Grid (3 Cards)
```blade
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
```

#### Card 1: Customer Information
```blade
<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center mb-4">
        <i class="fas fa-user-circle text-blue-600 dark:text-blue-400 mr-2 text-lg"></i>
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">Customer Information</h3>
    </div>
    <div class="space-y-4">
        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Consumer ID</p>
            <p class="text-sm font-medium text-gray-900 dark:text-white" id="consumer-id">-</p>
        </div>
        <!-- More fields... -->
    </div>
</div>
```

**Card Specs**:
- Background: `bg-white dark:bg-gray-800`
- Border: `rounded-lg border border-gray-200 dark:border-gray-700`
- Padding: `p-6`
- Header: Icon (colored) + Title (uppercase, tracking-wide)
- Fields: `space-y-4` with border-t separators
- Field labels: `text-xs uppercase tracking-wide`
- Field values: `text-sm font-medium`

#### Card 2: Meter & Billing
- Same structure as Card 1
- Icon: `fa-tachometer-alt text-green-600`
- Fields: Meter Number, Rate Class, Total Bill
- Total Bill in red: `text-red-600 dark:text-red-400`

#### Card 3: Account Status
- Same structure as Card 1
- Icon: `fa-info-circle text-purple-600`
- Status badge: `inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold`
- Ledger balance in green: `text-green-600 dark:text-green-400`

### 4. Tabs Navigation
```blade
<div class="mb-6">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8">
            <button onclick="switchTab('documents')" id="tab-documents"
                    class="tab-button border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400">
                <i class="fas fa-file-alt mr-2"></i>Documents & History
            </button>
            <button onclick="switchTab('connections')" id="tab-connections"
                    class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                <i class="fas fa-plug mr-2"></i>Service Connections
            </button>
            <button onclick="switchTab('ledger')" id="tab-ledger"
                    class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
                <i class="fas fa-book mr-2"></i>Ledger
            </button>
        </nav>
    </div>
</div>
```

**Tab Specs**:
- Border-bottom on container
- Active tab: `border-blue-500 text-blue-600`
- Inactive tab: `border-transparent text-gray-500`
- Hover states on inactive
- Icons before text
- Padding: `py-4 px-1`

### 5. Documents Tab Content
```blade
<div id="documents-content" class="tab-content">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-file-alt mr-2 text-blue-600"></i>Documents & History
        </h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                <div class="flex items-center gap-3">
                    <i class="fas fa-file-pdf text-red-600 text-2xl"></i>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Service Application Form</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Last updated: 2024-01-15</p>
                    </div>
                </div>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-download mr-2"></i>Download
                </button>
            </div>
        </div>
    </div>
</div>
```

**Documents Tab Specs**:
- Card container with title
- Document items: `space-y-4`
- Each item: `bg-gray-50 dark:bg-gray-700 rounded-lg border p-4`
- Layout: flex justify-between
- Icon (2xl) + Title + Metadata
- Action button: blue, with icon

### 6. Connections Tab Content
```blade
<div id="connections-content" class="tab-content hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-plug mr-2 text-green-600"></i>Service Connections
        </h3>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-200">Meter Number</th>
                        <!-- More columns -->
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 font-mono text-gray-900 dark:text-gray-100" id="conn-meter">-</td>
                        <!-- More cells -->
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
```

**Connections Tab Specs**:
- Same card container
- Table inside with overflow-x-auto
- Table header: `bg-gray-100 dark:bg-gray-700`
- Row hover effect
- Status badge in cell

### 7. Ledger Tab Content
```blade
<div id="ledger-content" class="tab-content hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-book mr-2 text-purple-600"></i>Consumer Ledger
        </h3>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <!-- Ledger table -->
            </table>
        </div>

        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg border border-blue-200 dark:border-blue-700">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Balance</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" id="total-balance">₱0.00</p>
                </div>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-download mr-2"></i>Export Ledger
                </button>
            </div>
        </div>
    </div>
</div>
```

**Ledger Tab Specs**:
- Same card + table structure
- Summary card at bottom: `bg-blue-50 dark:bg-blue-900`
- Total balance: `text-2xl font-bold`
- Export button

---

## Customer List Current Implementation

### List Page - What We Have
✅ **Matches Consumer**:
- max-w-7xl wrapper
- Page header with icon
- Stats cards structure (Phase 7 fixed)
- Action functions component
- Table container with rounded-xl
- Table headers with px-4 py-3.5
- Pagination with chevrons

❌ **Different from Consumer**:
- Stats cards use different structure (custom SVG vs x-ui.stat-card component)
- Stats card data fields (total, active, pending, inactive vs Total, Residential, Total Bill, Overdue)
- Table data loaded from API (dynamic) vs static data
- JavaScript implementation different

### Details Page - What We Have
❌ **Major Differences**:
- Uses `x-ui.card` component wrapper instead of individual cards
- Card structure completely different (simple key-value pairs vs consumer's structured layout)
- Missing colored icons in card headers
- Missing border separators between fields
- Missing uppercase tracking-wide styling on labels
- Only 2 tabs (Documents, Connections) vs 3 tabs (Documents, Connections, Ledger)
- Tab content structure different
- No Documents tab implementation
- No Ledger tab implementation
- Connections tab has different columns

---

## Gap Analysis

### List Page Gaps

1. **Stats Cards Component**
   - Need to switch from custom SVG stats to `x-ui.stat-card` component
   - Need to update stats data structure to match consumer (Total, Residential, Total Bill, Overdue)
   - Backend needs to provide matching stats

2. **Stats API Endpoint**
   - Consumer uses: Total Customers, Residential Type, Total Current Bill, Overdue
   - Customer currently uses: total, active, pending, inactive
   - Need to update CustomerService::getCustomerStats() to match consumer stats

3. **Table Header Styling**
   - Consumer uses: `font-semibold text-gray-700 dark:text-gray-200`
   - Customer uses: `font-medium text-gray-500 dark:text-gray-300`
   - Need to update to match consumer

### Details Page Gaps

1. **Page Structure**
   - Consumer: Uses max-w-7xl wrapper
   - Customer: Missing max-w-7xl wrapper

2. **Profile Cards**
   - Consumer: 3 separate cards with individual borders, icons, structured layout
   - Customer: Single x-ui.card wrapper with simple 3-column grid
   - **Complete rebuild needed**

3. **Card Headers**
   - Consumer: Icon (colored) + Title (uppercase tracking-wide)
   - Customer: Simple h3 text
   - **Missing colored icons, missing uppercase styling**

4. **Card Fields**
   - Consumer: Border-top separators, space-y-4, labels uppercase tracking-wide
   - Customer: Simple flex layout, no separators
   - **Complete rebuild needed**

5. **Tabs**
   - Consumer: 3 tabs (Documents, Connections, Ledger)
   - Customer: 2 tabs (Documents, Connections)
   - **Missing Ledger tab**

6. **Documents Tab**
   - Consumer: Document cards with icons, metadata, download buttons
   - Customer: Empty table structure
   - **Complete rebuild needed**

7. **Connections Tab**
   - Consumer: Simple table (4 columns)
   - Customer: Complex table (7 columns)
   - **Need to simplify to match consumer**

8. **Ledger Tab**
   - Consumer: Table + Summary card with total balance
   - Customer: **Completely missing**
   - **New tab needed**

9. **JavaScript**
   - Consumer: Loads from static data, updates DOM
   - Customer: Needs to load from API
   - **Need to create customer-details.js with API integration**

---

## Implementation Plan

### Phase 1: List Page - Stats Cards Component Migration
**Goal**: Replace custom SVG stats cards with x-ui.stat-card component matching consumer

**Backend Changes**:
```php
// CustomerService.php - Update getCustomerStats()
public function getCustomerStats(): array
{
    return [
        'total_customers' => Customer::where('stat_id', Status::getIdByDescription(Status::ACTIVE))->count(),
        'residential_count' => Customer::where('stat_id', Status::getIdByDescription(Status::ACTIVE))
                                      ->where('c_type', 'RESIDENTIAL')
                                      ->count(),
        'total_current_bill' => number_format(
            DB::table('CustomerLedger')
                ->where('stat_id', Status::getIdByDescription(Status::ACTIVE))
                ->sum(DB::raw('debit - credit')),
            2
        ),
        'overdue_count' => DB::table('customers')
            ->join('CustomerLedger', 'customers.cust_id', '=', 'CustomerLedger.customer_id')
            ->join('water_bill_history', function($join) {
                $join->on('CustomerLedger.source_id', '=', 'water_bill_history.bill_id')
                     ->where('CustomerLedger.source_type', '=', 'BILL');
            })
            ->where('water_bill_history.due_date', '<', now())
            ->where('CustomerLedger.stat_id', Status::getIdByDescription(Status::ACTIVE))
            ->whereRaw('CustomerLedger.debit > CustomerLedger.credit')
            ->distinct('customers.cust_id')
            ->count('customers.cust_id'),
    ];
}
```

**Frontend Changes**:
```blade
<!-- customer-list.blade.php -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <x-ui.stat-card
        title="Total Customers"
        value="0"
        icon="fas fa-user"
        id="stat-total" />
    <x-ui.stat-card
        title="Residential Type"
        value="0"
        icon="fas fa-home"
        id="stat-residential" />
    <x-ui.stat-card
        title="Total Current Bill"
        value="₱0.00"
        icon="fas fa-file-invoice-dollar"
        id="stat-bill" />
    <x-ui.stat-card
        title="Overdue"
        value="0"
        icon="fas fa-exclamation-triangle"
        id="stat-overdue" />
</div>
```

**JavaScript Changes**:
```javascript
// customer-list-simple.js
function renderStats(stats) {
    // Update each stat card by ID
    document.querySelector('#stat-total [data-stat-value]').textContent = stats.total_customers?.toLocaleString() || 0;
    document.querySelector('#stat-residential [data-stat-value]').textContent = stats.residential_count?.toLocaleString() || 0;
    document.querySelector('#stat-bill [data-stat-value]').textContent = '₱' + (parseFloat(stats.total_current_bill) || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
    document.querySelector('#stat-overdue [data-stat-value]').textContent = stats.overdue_count?.toLocaleString() || 0;
}
```

**Tasks**:
1. Update CustomerService::getCustomerStats() method
2. Create /customer/stats route and controller method
3. Update customer-list.blade.php to use x-ui.stat-card components
4. Update customer-list-simple.js renderStats() function
5. Test stats display with real data

---

### Phase 2: List Page - Table Header Styling
**Goal**: Match consumer table header styling exactly

**Changes Needed**:
```blade
<!-- Before -->
<th scope="col" class="px-4 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">

<!-- After (consumer style) -->
<th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wider">
```

**Tasks**:
1. Update all 6 `<th>` elements in customer-list.blade.php
2. Change `font-medium` to `font-semibold`
3. Change `text-gray-500` to `text-gray-700`
4. Change `dark:text-gray-300` to `dark:text-gray-200`
5. Remove `scope="col"` attributes (not in consumer)

---

### Phase 3: List Page - Address Column Simplification
**Goal**: Remove customer type from address column to match consumer

**Current**:
```javascript
<td class="px-4 py-3">
    <div class="text-sm text-gray-900 dark:text-gray-100">
        ${escapeHtml(customer.location || 'N/A')}
    </div>
    <div class="text-xs text-gray-500 dark:text-gray-400">
        Type: ${escapeHtml(customer.c_type || 'N/A')}
    </div>
</td>
```

**Should be** (consumer style):
```javascript
<td class="px-4 py-3">
    <div class="text-sm text-gray-900 dark:text-gray-100">${escapeHtml(customer.location || 'N/A')}</div>
</td>
```

**Tasks**:
1. Update customer-list-simple.js line ~246-251
2. Remove the Type div
3. Keep only location div

---

### Phase 4: Details Page - Page Structure & Header
**Goal**: Add max-w-7xl wrapper and update header to match consumer

**Changes**:
```blade
<!-- Before -->
<x-app-layout>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="p-6">
        <x-ui.page-header
            title="Customer Details"
            subtitle="View customer information and history">

<!-- After (consumer style) -->
<x-app-layout>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <x-ui.page-header
                title="Customer Details"
                subtitle="View customer information and history">
```

**Tasks**:
1. Add `max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8` wrapper
2. Remove standalone `p-6`
3. Verify Back button works

---

### Phase 5: Details Page - Profile Cards Complete Rebuild
**Goal**: Replace single card with 3 individual profile cards matching consumer exactly

**Current Structure** (to be removed):
```blade
<x-ui.card class="mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Simple key-value pairs -->
    </div>
</x-ui.card>
```

**New Structure** (consumer style):
```blade
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Card 1: Customer Information -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-user-circle text-blue-600 dark:text-blue-400 mr-2 text-lg"></i>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">Customer Information</h3>
        </div>
        <div class="space-y-4">
            <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Customer ID</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white" id="customer-id">-</p>
            </div>
            <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Full Name</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white" id="customer-name">-</p>
            </div>
            <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Address</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white" id="customer-address">-</p>
            </div>
        </div>
    </div>

    <!-- Card 2: Meter & Billing -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-tachometer-alt text-green-600 dark:text-green-400 mr-2 text-lg"></i>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">Meter & Billing</h3>
        </div>
        <div class="space-y-4">
            <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Meter Number</p>
                <p class="text-sm font-mono font-medium text-gray-900 dark:text-white" id="customer-meter">-</p>
            </div>
            <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Rate Class</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white" id="customer-rate">-</p>
            </div>
            <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Total Bill</p>
                <p class="text-sm font-semibold text-red-600 dark:text-red-400" id="customer-bill">-</p>
            </div>
        </div>
    </div>

    <!-- Card 3: Account Status -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-info-circle text-purple-600 dark:text-purple-400 mr-2 text-lg"></i>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">Account Status</h3>
        </div>
        <div class="space-y-4">
            <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">Status</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300" id="customer-status">-</span>
            </div>
            <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Ledger Balance</p>
                <p class="text-sm font-semibold text-green-600 dark:text-green-400" id="customer-ledger">-</p>
            </div>
            <div class="border-t border-gray-100 dark:border-gray-700 pt-3">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Last Updated</p>
                <p class="text-sm text-gray-900 dark:text-white" id="customer-updated">-</p>
            </div>
        </div>
    </div>
</div>
```

**Key Details**:
- 3 separate cards, not single wrapper
- Each card: `bg-white dark:bg-gray-800 rounded-lg border p-6`
- Header: Icon (colored by card) + Title (uppercase tracking-wide)
- Fields: `space-y-4` with `border-t pt-3` separators
- Labels: `text-xs uppercase tracking-wide text-gray-500`
- Values: `text-sm font-medium` (or font-semibold for important values)
- Special colors: red for bill, green for ledger, purple for status icon

**Tasks**:
1. Remove `<x-ui.card>` wrapper
2. Create 3 individual card divs
3. Add colored icons to each card header
4. Add border-top separators between fields
5. Update all class names to match consumer exactly
6. Update element IDs to match new JavaScript

---

### Phase 6: Details Page - Tabs Navigation & Content
**Goal**: Add Ledger tab and update tab styling to match consumer

**Tab Navigation Changes**:
```blade
<!-- Add Ledger tab -->
<button onclick="switchTab('ledger')" id="tab-ledger"
        class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300">
    <i class="fas fa-book mr-2"></i>Ledger
</button>
```

**Tasks**:
1. Add Ledger tab button to nav
2. Update tab button classes to match consumer
3. Verify tab switching works

---

### Phase 7: Details Page - Documents Tab Rebuild
**Goal**: Replace table with document cards matching consumer

**Remove**:
```blade
<!-- Current table structure -->
<div id="documents-content" class="tab-content">
    <div class="overflow-x-auto border rounded-lg shadow-sm bg-white dark:bg-gray-800">
        <table>...</table>
    </div>
</div>
```

**Add** (consumer style):
```blade
<div id="documents-content" class="tab-content">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-file-alt mr-2 text-blue-600"></i>Documents & History
        </h3>
        <div class="space-y-4" id="documents-list">
            <!-- Document items will be loaded by JavaScript -->
            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                <div class="flex items-center gap-3">
                    <i class="fas fa-file-pdf text-red-600 text-2xl"></i>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">Service Application Form</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Last updated: -</p>
                    </div>
                </div>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-download mr-2"></i>Download
                </button>
            </div>
        </div>
    </div>
</div>
```

**Tasks**:
1. Remove table structure
2. Add card container with title
3. Create document item template
4. Add space-y-4 container for items
5. Style items with bg-gray-50, icons, metadata

---

### Phase 8: Details Page - Connections Tab Simplification
**Goal**: Simplify connections table to match consumer (4 columns instead of 7)

**Current** (7 columns):
- Account No, Customer Type, Meter Reader & Area, Meter No, Date Installed, Status, Actions

**Should be** (consumer style, 4 columns):
- Meter Number, Status, Date Installed, Actions

**Changes**:
```blade
<table class="min-w-full text-sm">
    <thead>
        <tr class="bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
            <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-200">Meter Number</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-200">Status</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-200">Date Installed</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-200">Actions</th>
        </tr>
    </thead>
    <tbody id="connections-tbody">
        <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
            <td class="px-4 py-3 font-mono text-gray-900 dark:text-gray-100" id="conn-meter">-</td>
            <td class="px-4 py-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300" id="conn-status">Active</span>
            </td>
            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">-</td>
            <td class="px-4 py-3">
                <button class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                    <i class="fas fa-info-circle"></i>
                </button>
            </td>
        </tr>
    </tbody>
</table>
```

**Tasks**:
1. Remove columns: Account No, Customer Type, Meter Reader & Area
2. Keep: Meter Number (font-mono), Status (badge), Date Installed, Actions (icon button)
3. Update table header styling
4. Update tbody ID to match JavaScript

---

### Phase 9: Details Page - Ledger Tab Creation
**Goal**: Create new Ledger tab matching consumer exactly

**Create new file**: `resources/views/pages/customer/tabs/ledger-tab.blade.php`

**Content**:
```blade
<!-- Ledger Tab -->
<div id="ledger-content" class="tab-content hidden">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-book mr-2 text-purple-600"></i>Customer Ledger
        </h3>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-200">Bill Period</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-200">Amount Due</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-200">Amount Paid</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-200">Balance</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-200">Status</th>
                    </tr>
                </thead>
                <tbody id="ledger-tbody">
                    <!-- Ledger entries will be loaded by JavaScript -->
                </tbody>
            </table>
        </div>

        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg border border-blue-200 dark:border-blue-700">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Balance</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white" id="total-balance">₱0.00</p>
                </div>
                <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-download mr-2"></i>Export Ledger
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Update ledger balance when page loads
    document.addEventListener('DOMContentLoaded', function() {
        if (window.currentCustomer) {
            const totalBalanceEl = document.getElementById('total-balance');
            if (totalBalanceEl) {
                totalBalanceEl.textContent = window.currentCustomer.ledger_balance;
            }
        }
    });
</script>
```

**Tasks**:
1. Create ledger-tab.blade.php
2. Include in customer-details.blade.php: `@include('pages.customer.tabs.ledger-tab')`
3. Add table structure
4. Add summary card with total balance
5. Add inline script for balance update

---

### Phase 10: Details Page - Create Tab Files
**Goal**: Move tab content to separate files matching consumer structure

**Create 3 tab files**:
1. `resources/views/pages/customer/tabs/documents-tab.blade.php`
2. `resources/views/pages/customer/tabs/connections-tab.blade.php`
3. `resources/views/pages/customer/tabs/ledger-tab.blade.php` (already created in Phase 9)

**Update customer-details.blade.php**:
```blade
<!-- Remove inline tab content -->
<!-- Add includes -->
@include('pages.customer.tabs.documents-tab')
@include('pages.customer.tabs.connections-tab')
@include('pages.customer.tabs.ledger-tab')
```

**Tasks**:
1. Create documents-tab.blade.php with content from Phase 7
2. Create connections-tab.blade.php with content from Phase 8
3. Update customer-details.blade.php to include tab files
4. Remove inline tab content from customer-details.blade.php

---

### Phase 11: Details Page - JavaScript Implementation
**Goal**: Create customer-details.js matching consumer functionality with API integration

**Create**: `resources/js/data/customer/customer-details.js`

**Content**:
```javascript
(function() {
    'use strict';

    // Get customer ID from URL
    const pathSegments = window.location.pathname.split('/');
    const customerId = pathSegments[pathSegments.length - 1];

    // CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    /**
     * Fetch customer details from API
     */
    async function loadCustomerDetails() {
        try {
            const response = await fetch(`/api/customer/${customerId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const customer = await response.json();

            if (customer) {
                updateCustomerInfo(customer);
                updateMeterBilling(customer);
                updateAccountStatus(customer);

                // Update page title
                document.title = `${customer.customer_name} - Customer Details`;

                // Store customer data globally for tab access
                window.currentCustomer = customer;
            } else {
                console.error(`Customer with ID ${customerId} not found`);
                document.getElementById('customer-name').textContent = 'Customer not found';
            }
        } catch (error) {
            console.error('Error loading customer details:', error);
            showErrorMessage('Failed to load customer details');
        }
    }

    /**
     * Update customer information card
     */
    function updateCustomerInfo(customer) {
        document.getElementById('customer-id').textContent = customer.cust_id || '-';
        document.getElementById('customer-name').textContent = customer.customer_name || '-';
        document.getElementById('customer-address').textContent = customer.location || '-';
    }

    /**
     * Update meter & billing card
     */
    function updateMeterBilling(customer) {
        document.getElementById('customer-meter').textContent = customer.meter_no || 'N/A';
        document.getElementById('customer-rate').textContent = customer.rate_class || '-';
        document.getElementById('customer-bill').textContent = customer.current_bill || '₱0.00';
    }

    /**
     * Update account status card
     */
    function updateAccountStatus(customer) {
        const statusEl = document.getElementById('customer-status');
        statusEl.textContent = customer.status || '-';

        // Update status badge color
        const statusColors = {
            'ACTIVE': 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300',
            'PENDING': 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300',
            'INACTIVE': 'bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-300'
        };
        statusEl.className = `inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${statusColors[customer.status] || statusColors.ACTIVE}`;

        document.getElementById('customer-ledger').textContent = customer.ledger_balance || '₱0.00';
        document.getElementById('customer-updated').textContent = customer.updated_at
            ? new Date(customer.updated_at).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            })
            : '-';
    }

    /**
     * Tab switching functionality
     */
    window.switchTab = function(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active styling from all tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
            button.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
        });

        // Show selected tab content
        const contentId = `${tabName}-content`;
        const contentElement = document.getElementById(contentId);
        if (contentElement) {
            contentElement.classList.remove('hidden');
        }

        // Highlight selected tab button
        const tabButton = document.getElementById(`tab-${tabName}`);
        if (tabButton) {
            tabButton.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            tabButton.classList.add('border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        }

        // Load tab-specific data
        if (tabName === 'documents') {
            loadDocuments();
        } else if (tabName === 'connections') {
            loadConnections();
        } else if (tabName === 'ledger') {
            loadLedger();
        }
    };

    /**
     * Load documents for current customer
     */
    async function loadDocuments() {
        // TODO: Implement document loading from API
        console.log('Loading documents for customer:', customerId);
    }

    /**
     * Load service connections for current customer
     */
    async function loadConnections() {
        try {
            const response = await fetch(`/api/customer/${customerId}/connections`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const connections = await response.json();
            renderConnections(connections);
        } catch (error) {
            console.error('Error loading connections:', error);
        }
    }

    /**
     * Render service connections table
     */
    function renderConnections(connections) {
        const tbody = document.getElementById('connections-tbody');
        if (!tbody || !connections || connections.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">No connections found</td></tr>';
            return;
        }

        tbody.innerHTML = connections.map(conn => `
            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-4 py-3 font-mono text-gray-900 dark:text-gray-100">${conn.meter_no || 'N/A'}</td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${getStatusColor(conn.status)}">
                        ${conn.status}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">${conn.installed_at || '-'}</td>
                <td class="px-4 py-3">
                    <button class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                        <i class="fas fa-info-circle"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    /**
     * Load ledger for current customer
     */
    async function loadLedger() {
        try {
            const response = await fetch(`/api/customer/${customerId}/ledger`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const ledger = await response.json();
            renderLedger(ledger.entries, ledger.total_balance);
        } catch (error) {
            console.error('Error loading ledger:', error);
        }
    }

    /**
     * Render ledger table
     */
    function renderLedger(entries, totalBalance) {
        const tbody = document.getElementById('ledger-tbody');
        if (!tbody || !entries || entries.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">No ledger entries found</td></tr>';
            return;
        }

        tbody.innerHTML = entries.map(entry => `
            <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">${entry.period}</td>
                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">₱${parseFloat(entry.amount_due).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">₱${parseFloat(entry.amount_paid).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">₱${parseFloat(entry.balance).toLocaleString('en-PH', {minimumFractionDigits: 2})}</td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${entry.balance === 0 ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300'}">
                        ${entry.balance === 0 ? 'Paid' : 'Unpaid'}
                    </span>
                </td>
            </tr>
        `).join('');

        // Update total balance
        document.getElementById('total-balance').textContent = `₱${parseFloat(totalBalance).toLocaleString('en-PH', {minimumFractionDigits: 2})}`;
    }

    /**
     * Get status badge color
     */
    function getStatusColor(status) {
        const colors = {
            'ACTIVE': 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300',
            'PENDING': 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300',
            'INACTIVE': 'bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-300'
        };
        return colors[status] || colors.ACTIVE;
    }

    /**
     * Show error message
     */
    function showErrorMessage(message) {
        // TODO: Implement error message display
        console.error(message);
    }

    // Initialize on page load
    loadCustomerDetails();
})();
```

**Tasks**:
1. Create customer-details.js file
2. Add API integration for customer details
3. Implement tab switching
4. Implement connections loading
5. Implement ledger loading
6. Update customer-details.blade.php to include new JS file

---

### Phase 12: Backend API Endpoints
**Goal**: Create API endpoints for customer details page

**Create**: API routes and controller methods

**Routes** (`routes/api.php`):
```php
Route::middleware(['auth'])->group(function () {
    Route::get('/customer/{id}', [CustomerController::class, 'getCustomerDetails']);
    Route::get('/customer/{id}/connections', [CustomerController::class, 'getCustomerConnections']);
    Route::get('/customer/{id}/ledger', [CustomerController::class, 'getCustomerLedger']);
});
```

**Controller Methods** (`app/Http/Controllers/Customer/CustomerController.php`):
```php
public function getCustomerDetails($id)
{
    $customer = Customer::with(['status', 'serviceConnections.meterAssignment.meter', 'customerLedgerEntries'])
        ->findOrFail($id);

    return response()->json([
        'cust_id' => $customer->cust_id,
        'customer_name' => trim("{$customer->cust_first_name} {$customer->cust_middle_name} {$customer->cust_last_name}"),
        'location' => $this->customerService->formatLocation($customer),
        'meter_no' => $this->customerService->getCustomerMeterNumber($customer),
        'rate_class' => 'Residential', // TODO: Get from account type
        'current_bill' => $this->customerService->getCustomerCurrentBill($customer),
        'status' => $customer->status?->stat_desc ?? 'UNKNOWN',
        'ledger_balance' => $this->customerService->getCustomerLedgerBalance($customer),
        'updated_at' => $customer->updated_at,
    ]);
}

public function getCustomerConnections($id)
{
    $customer = Customer::findOrFail($id);

    $connections = $customer->serviceConnections()
        ->with(['meterAssignment.meter', 'status'])
        ->get()
        ->map(function ($connection) {
            return [
                'meter_no' => $connection->meterAssignment?->meter?->mtr_serial ?? 'N/A',
                'status' => $connection->status?->stat_desc ?? 'UNKNOWN',
                'installed_at' => $connection->started_at?->format('Y-m-d') ?? '-',
            ];
        });

    return response()->json($connections);
}

public function getCustomerLedger($id)
{
    $customer = Customer::findOrFail($id);

    // Get ledger entries grouped by billing period
    $entries = DB::table('CustomerLedger')
        ->join('water_bill_history', function($join) {
            $join->on('CustomerLedger.source_id', '=', 'water_bill_history.bill_id')
                 ->where('CustomerLedger.source_type', '=', 'BILL');
        })
        ->join('Period', 'water_bill_history.period_id', '=', 'Period.period_id')
        ->where('CustomerLedger.customer_id', $id)
        ->select([
            'Period.period_desc as period',
            DB::raw('SUM(CASE WHEN source_type = "BILL" THEN debit ELSE 0 END) as amount_due'),
            DB::raw('SUM(CASE WHEN source_type = "PAYMENT" THEN credit ELSE 0 END) as amount_paid'),
            DB::raw('SUM(debit - credit) as balance'),
        ])
        ->groupBy('Period.period_desc')
        ->orderBy('Period.period_id', 'desc')
        ->get();

    $totalBalance = $entries->sum('balance');

    return response()->json([
        'entries' => $entries,
        'total_balance' => number_format($totalBalance, 2),
    ]);
}
```

**Tasks**:
1. Add API routes
2. Create getCustomerDetails method
3. Create getCustomerConnections method
4. Create getCustomerLedger method
5. Add CustomerService helper method: getCustomerLedgerBalance()
6. Test all API endpoints

---

## Testing & Verification

### List Page Testing
1. **Stats Cards**
   - [ ] Stats display correct values
   - [ ] Stats use x-ui.stat-card component
   - [ ] Stats match consumer layout (Total, Residential, Total Bill, Overdue)
   - [ ] Stats update on page load

2. **Table Structure**
   - [ ] Table headers use font-semibold text-gray-700
   - [ ] Table container has rounded-xl border
   - [ ] Pagination has chevron icons
   - [ ] Address column shows location only (no type)

3. **Functionality**
   - [ ] Search works correctly
   - [ ] Filter works correctly
   - [ ] Pagination works correctly
   - [ ] Page size selector works

### Details Page Testing
1. **Page Structure**
   - [ ] Has max-w-7xl wrapper
   - [ ] Back button works
   - [ ] Header has subtitle

2. **Profile Cards**
   - [ ] 3 separate cards (not single wrapper)
   - [ ] Each card has colored icon
   - [ ] Card titles are uppercase tracking-wide
   - [ ] Fields have border-top separators
   - [ ] Total bill is red
   - [ ] Ledger balance is green
   - [ ] Status badge has correct color

3. **Tabs**
   - [ ] 3 tabs (Documents, Connections, Ledger)
   - [ ] Tab switching works
   - [ ] Active tab has blue border
   - [ ] Inactive tabs have gray color

4. **Documents Tab**
   - [ ] Shows document cards (not table)
   - [ ] Each document has icon, title, metadata
   - [ ] Download buttons work

5. **Connections Tab**
   - [ ] Shows 4 columns (Meter, Status, Date, Actions)
   - [ ] Meter number is font-mono
   - [ ] Status badge has color
   - [ ] Data loads from API

6. **Ledger Tab**
   - [ ] Shows ledger table
   - [ ] Shows summary card with total balance
   - [ ] Export button present
   - [ ] Data loads from API

---

## Summary

This plan covers **complete UI/UX replication** of the consumer list into the customer list:

**List Page**: 3 phases covering stats, table headers, and address column
**Details Page**: 9 phases covering structure, profile cards, tabs, and JavaScript
**Backend**: 1 phase for API endpoints

**Total Phases**: 12
**Estimated Effort**: Large (complete rebuild of details page)
**Impact**: High (achieves exact UI/UX parity with consumer list)

All phases are designed to be executed sequentially with clear verification steps.
