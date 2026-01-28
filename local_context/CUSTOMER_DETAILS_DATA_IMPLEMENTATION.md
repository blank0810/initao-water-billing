# Customer Details Page - Real Data Implementation Plan

**Created:** 2026-01-26
**Status:** Planning Phase
**Purpose:** Replace placeholder data with real database data on customer details page

---

## Table of Contents

1. [Current State Analysis](#1-current-state-analysis)
2. [Gap Analysis](#2-gap-analysis)
3. [Database Schema Mapping](#3-database-schema-mapping)
4. [Architecture Overview](#4-architecture-overview)
5. [Implementation Plan](#5-implementation-plan)
6. [Code Examples](#6-code-examples)
7. [Testing Checklist](#7-testing-checklist)

---

## 1. Current State Analysis

### How Consumer Details Works (Legacy System)

**Route:**
```php
// routes/web.php:344
Route::get('/consumer/details/{id}', function ($id) {
    return view('pages.consumer.consumer-details');
});
```

**Data Flow:**
1. **Static Data Source**: Uses hardcoded JavaScript data from `consumer.js`
2. **Client-Side Only**: All data is in the browser (no API calls)
3. **Data Structure**: Simple flat objects with basic fields
4. **Population Method**: Direct DOM manipulation with `getElementById`

**JavaScript Implementation** (`consumer-details.js`):
```javascript
// 1. Get ID from URL
const consumerId = pathSegments[pathSegments.length - 1];

// 2. Find from static data
const consumer = consumerData.find(c => c.cust_id == consumerId);

// 3. Populate DOM directly
document.getElementById('consumer-id').textContent = consumer.cust_id;
document.getElementById('consumer-name').textContent = consumer.name;
// ... etc
```

**Data Source** (`consumer.js`):
```javascript
export const consumerData = [
    {
        cust_id: 1001,
        name: 'Juan Dela Cruz',
        address: 'Purok 1, Poblacion',
        meter_no: 'MTR-XYZ-12345',
        rate: '₱25.50/m³',
        total_bill: '₱3,500.00',
        ledger_balance: '₱0.00',
        status: 'Active'
    }
    // ... more static records
];
```

### Current Customer Details Implementation

**Route:**
```php
// routes/web.php:109
Route::get('/customer/details/{id}', function ($id) {
    session(['active_menu' => 'customer-list']);
    return view('pages.customer.customer-details', ['customer_id' => $id]);
});
```

**Current State:**
- ✅ UI is complete (3 beautiful cards replicated from consumer)
- ✅ JavaScript files exist (`customer-details-data.js`, `enhanced-customer-data.js`)
- ❌ Uses dummy `enhancedCustomerData` array (not real database)
- ❌ No API endpoint to fetch real customer data
- ❌ No service method to retrieve customer details
- ❌ All fields show placeholder data

**Current JavaScript** (`customer-details-data.js`):
```javascript
// Still using static data
const customer = enhancedCustomerData.find(c => c.customer_code === customerCode);

if (customer) {
    document.getElementById('consumer-id').textContent = customer.customer_code;
    document.getElementById('consumer-name').textContent = fullName;
    // ... populating from static array
}
```

---

## 2. Gap Analysis

### What's Missing for Customer Details

| Component | Consumer (Legacy) | Customer (Modern) | Gap |
|-----------|-------------------|-------------------|-----|
| **Route** | Simple closure | Simple closure with session | No API route |
| **Controller** | None | CustomerController exists | Missing `show()` call |
| **Service** | None | CustomerService exists | Has `getCustomerById()` ✅ |
| **API Endpoint** | None | None | Need to create |
| **JavaScript** | Static data import | Static data import | Need fetch/axios call |
| **Data Structure** | Flat object | Nested relationships | Need to map complex data |
| **Error Handling** | Basic console.error | None | Need comprehensive handling |

### Key Differences: Consumer vs Customer

**Consumer Model (Legacy):**
- Simple structure
- Direct relationships
- Table: `Consumer` (PascalCase)
- Primary Key: `c_id`
- Related to: `Customer`, `ConsumerMeter`, `Area`, `ConsumerLedger`

**Customer Model (Modern):**
- Complex structure with multiple relationships
- Modern system integration
- Table: `customer` (snake_case)
- Primary Key: `cust_id`
- Related to: `ServiceConnection`, `MeterAssignment`, `CustomerLedger`, `Payment`, `ServiceApplication`

**Critical Distinction:**
- **Consumer**: A person with a meter (legacy billing system)
- **Customer**: A registered person who may have multiple service connections (modern system)
- One Customer can have multiple ServiceConnections
- Each ServiceConnection can have a MeterAssignment

---

## 3. Database Schema Mapping

### Customer Details Page UI Fields

#### Card 1: Customer Information

| UI Field | Database Source | Model Chain | Notes |
|----------|----------------|-------------|-------|
| **Customer ID** | `customer.cust_id` | `Customer.cust_id` | Primary key |
| **Full Name** | `customer.cust_first_name` + `cust_middle_name` + `cust_last_name` | `Customer` | Concatenate with spaces |
| **Address** | Multiple tables | `Customer → ConsumerAddress → Purok, Barangay, Town, Province` | Format: "Purok X, Barangay Y, Town Z" |

**Address Mapping:**
```
Customer.ca_id → ConsumerAddress.ca_id
  ConsumerAddress.p_id → Purok.p_id → Purok.p_desc
  ConsumerAddress.b_id → Barangay.b_id → Barangay.b_desc
  ConsumerAddress.t_id → Town.t_id → Town.t_desc
  ConsumerAddress.prov_id → Province.prov_id → Province.prov_desc
```

#### Card 2: Meter & Billing

| UI Field | Database Source | Model Chain | Notes |
|----------|----------------|-------------|-------|
| **Meter Number** | `meter.mtr_serial` | `Customer → ServiceConnection → MeterAssignment → Meter` | Get active connection only |
| **Rate Class** | `account_type.at_description` | `Customer → ServiceConnection → AccountType` | e.g., "Residential", "Commercial" |
| **Total Bill** | Calculated | `Customer → CustomerLedger` | Sum unpaid bills: `SUM(debit - credit)` |

**Meter Number Query Path:**
```
Customer.cust_id → ServiceConnection.customer_id
  WHERE ServiceConnection.stat_id = ACTIVE
  ServiceConnection.connection_id → MeterAssignment.connection_id
    WHERE MeterAssignment.removed_at IS NULL
    MeterAssignment.meter_id → Meter.mtr_id → Meter.mtr_serial
```

**Total Bill Calculation:**
```sql
SELECT SUM(debit) - SUM(credit)
FROM CustomerLedger
WHERE customer_id = {cust_id}
  AND source_type = 'BILL'
  AND (debit - credit) > 0
```

#### Card 3: Account Status

| UI Field | Database Source | Model Chain | Notes |
|----------|----------------|-------------|-------|
| **Status** | `status.stat_desc` | `Customer → Status` | Display as badge |
| **Ledger Balance** | Calculated | `Customer → CustomerLedger` | `SUM(debit) - SUM(credit)` for all entries |
| **Last Updated** | `customer.updated_at` | `Customer.updated_at` | Format: "Jan 20, 2026" |

**Status Badge Colors:**
- `ACTIVE` → Green
- `PENDING` → Orange
- `INACTIVE` → Gray
- `UNKNOWN` → Gray

### Data Relationships Diagram

```
Customer (cust_id)
├── Status (stat_id) → stat_desc
├── ConsumerAddress (ca_id)
│   ├── Purok (p_id) → p_desc
│   ├── Barangay (b_id) → b_desc
│   ├── Town (t_id) → t_desc
│   └── Province (prov_id) → prov_desc
│
├── ServiceConnection (customer_id) [1:N]
│   ├── Status (stat_id) → stat_desc
│   ├── AccountType (account_type_id) → at_description
│   ├── MeterAssignment (connection_id) [1:1 current]
│   │   └── Meter (meter_id) → mtr_serial
│   │
│   └── CustomerLedger (connection_id) [1:N]
│       ├── source_type (BILL, PAYMENT, CHARGE)
│       ├── debit (amount owed)
│       └── credit (amount paid)
│
└── CustomerLedger (customer_id) [1:N] (all ledger entries)
    ├── debit
    └── credit
```

---

## 4. Architecture Overview

### Data Flow: Database → Frontend

```
┌─────────────────────────────────────────────────────────────────┐
│                        CUSTOMER DETAILS PAGE                     │
└─────────────────────────────────────────────────────────────────┘
                                  │
                                  │ 1. Page Load
                                  ▼
┌─────────────────────────────────────────────────────────────────┐
│  JavaScript (customer-details-data.js)                           │
│  • Get customer_id from URL                                      │
│  • Fetch data from API                                           │
│  • Handle response/errors                                        │
│  • Populate DOM elements                                         │
└─────────────────────────────────────────────────────────────────┘
                                  │
                                  │ 2. Fetch Request
                                  ▼
┌─────────────────────────────────────────────────────────────────┐
│  Route (web.php)                                                 │
│  GET /api/customer/{id}/details                                  │
└─────────────────────────────────────────────────────────────────┘
                                  │
                                  │ 3. Route to Controller
                                  ▼
┌─────────────────────────────────────────────────────────────────┐
│  CustomerController::getDetails($id)                             │
│  • Validate request                                              │
│  • Call service                                                  │
│  • Return JSON response                                          │
└─────────────────────────────────────────────────────────────────┘
                                  │
                                  │ 4. Business Logic
                                  ▼
┌─────────────────────────────────────────────────────────────────┐
│  CustomerService::getCustomerDetails($id)                        │
│  • Query customer with relationships                             │
│  • Calculate total bill                                          │
│  • Format address                                                │
│  • Get active meter                                              │
│  • Return structured data                                        │
└─────────────────────────────────────────────────────────────────┘
                                  │
                                  │ 5. Data Access
                                  ▼
┌─────────────────────────────────────────────────────────────────┐
│  Eloquent Models                                                 │
│  • Customer                                                      │
│  • ServiceConnection                                             │
│  • MeterAssignment                                               │
│  • CustomerLedger                                                │
│  • ConsumerAddress                                               │
│  • Status                                                        │
└─────────────────────────────────────────────────────────────────┘
                                  │
                                  │ 6. Query Database
                                  ▼
┌─────────────────────────────────────────────────────────────────┐
│  MySQL Database                                                  │
│  Tables: customer, ServiceConnection, MeterAssignment, etc.     │
└─────────────────────────────────────────────────────────────────┘
```

### Response Data Structure

```json
{
    "success": true,
    "data": {
        "customer_info": {
            "cust_id": 1,
            "customer_code": "CUST-2024-001",
            "full_name": "Juan Santos Dela Cruz",
            "address": "Purok 1, Barangay Central, Poblacion, Misamis Oriental"
        },
        "meter_billing": {
            "meter_no": "MTR-2001",
            "rate_class": "Residential",
            "total_bill": "3500.00",
            "total_bill_formatted": "₱3,500.00"
        },
        "account_status": {
            "status": "ACTIVE",
            "status_badge": {
                "text": "Active",
                "color": "green"
            },
            "ledger_balance": "0.00",
            "ledger_balance_formatted": "₱0.00",
            "last_updated": "2026-01-26",
            "last_updated_formatted": "Jan 26, 2026"
        },
        "service_connections": [
            {
                "connection_id": 1,
                "account_no": "ACC-2024-5001",
                "connection_type": "Residential",
                "meter_no": "MTR-2001",
                "status": "ACTIVE"
            }
        ]
    }
}
```

---

## 5. Implementation Plan

### Phase 1: Backend API Development

#### Step 1.1: Add New Service Method

**File:** `app/Services/Customers/CustomerService.php`

**Method:** `getCustomerDetails(int $customerId): array`

**Purpose:** Retrieve comprehensive customer details with all relationships

**Implementation Steps:**
1. Query customer with eager loading
2. Calculate billing totals
3. Get active meter information
4. Format address string
5. Return structured array

#### Step 1.2: Add Controller Method

**File:** `app/Http/Controllers/Customer/CustomerController.php`

**Method:** `getDetails(int $id): JsonResponse`

**Purpose:** Handle API request and return JSON response

**Implementation Steps:**
1. Call service method
2. Handle exceptions
3. Return JSON with proper HTTP status codes

#### Step 1.3: Add API Route

**File:** `routes/web.php` or `routes/api.php`

**Route:** `GET /api/customer/{id}/details`

**Purpose:** Expose API endpoint for JavaScript to call

**Middleware:** `permission:customers.view`

### Phase 2: Frontend JavaScript Development

#### Step 2.1: Update JavaScript to Use Real API

**File:** `resources/js/data/customer/customer-details-data.js`

**Changes:**
1. Remove static data import
2. Add fetch/axios call to API
3. Handle loading state
4. Handle errors gracefully
5. Populate DOM with real data

#### Step 2.2: Add Loading Indicators

**Purpose:** Show user that data is being fetched

**Implementation:**
1. Add spinner/skeleton on page load
2. Hide spinner when data loads
3. Show error message if fetch fails

#### Step 2.3: Format Data for Display

**Purpose:** Convert raw API data to user-friendly format

**Formatting:**
1. Currency: `₱3,500.00`
2. Dates: `Jan 26, 2026`
3. Status badges: Colored badges
4. Default values: Show "-" if data missing

### Phase 3: Error Handling & Edge Cases

#### Step 3.1: Backend Error Handling

**Scenarios:**
1. Customer not found (404)
2. Database connection error (500)
3. Invalid customer ID (400)
4. Permission denied (403)

**Implementation:**
- Return proper HTTP status codes
- Include error messages in JSON
- Log errors for debugging

#### Step 3.2: Frontend Error Handling

**Scenarios:**
1. Network error
2. API returns error
3. Invalid response format
4. Missing data fields

**Implementation:**
- Show user-friendly error messages
- Provide retry option
- Log errors to console
- Fallback to "-" for missing fields

### Phase 4: Testing & Validation

#### Step 4.1: Backend Testing

**Test Cases:**
1. Customer with no connections
2. Customer with multiple connections
3. Customer with unpaid bills
4. Customer with incomplete address
5. Non-existent customer ID

#### Step 4.2: Frontend Testing

**Test Cases:**
1. Page loads correctly
2. Data displays properly
3. Error handling works
4. Loading states show
5. Browser compatibility

---

## 6. Code Examples

### 6.1 Backend Implementation

#### Add Service Method

**File:** `app/Services/Customers/CustomerService.php`

```php
<?php

namespace App\Services\Customers;

use App\Models\Customer;
use App\Models\Status;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    // ... existing methods ...

    /**
     * Get comprehensive customer details for details page
     *
     * @param int $customerId
     * @return array
     * @throws \Exception
     */
    public function getCustomerDetails(int $customerId): array
    {
        // Query customer with all necessary relationships
        $customer = Customer::with([
            'status',
            'address.purok',
            'address.barangay',
            'address.town',
            'address.province',
            'serviceConnections' => function ($query) {
                $query->with([
                    'status',
                    'accountType',
                    'meterAssignment.meter',
                    'area'
                ]);
            },
            'customerLedgerEntries'
        ])->find($customerId);

        if (!$customer) {
            throw new \Exception('Customer not found');
        }

        // Build response data
        return [
            'customer_info' => $this->buildCustomerInfo($customer),
            'meter_billing' => $this->buildMeterBilling($customer),
            'account_status' => $this->buildAccountStatus($customer),
            'service_connections' => $this->buildServiceConnections($customer)
        ];
    }

    /**
     * Build customer information section
     *
     * @param Customer $customer
     * @return array
     */
    private function buildCustomerInfo(Customer $customer): array
    {
        $fullName = trim("{$customer->cust_first_name} {$customer->cust_middle_name} {$customer->cust_last_name}");
        $address = $this->formatLocation($customer);

        return [
            'cust_id' => $customer->cust_id,
            'customer_code' => $customer->resolution_no ?? "CUST-{$customer->cust_id}",
            'full_name' => $fullName,
            'first_name' => $customer->cust_first_name,
            'middle_name' => $customer->cust_middle_name ?? '',
            'last_name' => $customer->cust_last_name,
            'contact_number' => $customer->contact_number ?? 'N/A',
            'address' => $address,
            'address_parts' => $this->getAddressParts($customer),
            'landmark' => $customer->land_mark ?? ''
        ];
    }

    /**
     * Build meter and billing section
     *
     * @param Customer $customer
     * @return array
     */
    private function buildMeterBilling(Customer $customer): array
    {
        // Get active service connection
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $activeConnection = $customer->serviceConnections
            ->where('stat_id', $activeStatusId)
            ->first();

        if (!$activeConnection) {
            return [
                'meter_no' => 'Not Assigned',
                'rate_class' => 'N/A',
                'total_bill' => '0.00',
                'total_bill_formatted' => '₱0.00',
                'has_active_connection' => false
            ];
        }

        // Get meter information
        $meterAssignment = $activeConnection->meterAssignment;
        $meterNo = ($meterAssignment && $meterAssignment->meter)
            ? $meterAssignment->meter->mtr_serial
            : 'Not Assigned';

        // Get rate class from account type
        $rateClass = $activeConnection->accountType
            ? $activeConnection->accountType->at_description
            : 'N/A';

        // Calculate total unpaid bills
        $totalBill = $this->calculateTotalUnpaidBills($customer);

        return [
            'meter_no' => $meterNo,
            'rate_class' => $rateClass,
            'total_bill' => number_format($totalBill, 2, '.', ''),
            'total_bill_formatted' => '₱' . number_format($totalBill, 2, '.', ','),
            'has_active_connection' => true,
            'connection_id' => $activeConnection->connection_id,
            'account_no' => $activeConnection->account_no
        ];
    }

    /**
     * Build account status section
     *
     * @param Customer $customer
     * @return array
     */
    private function buildAccountStatus(Customer $customer): array
    {
        $status = $customer->status?->stat_desc ?? 'UNKNOWN';
        $ledgerBalance = $this->calculateLedgerBalance($customer);
        $lastUpdated = $customer->updated_at ?? $customer->create_date;

        return [
            'status' => $status,
            'status_badge' => $this->getStatusBadgeData($status),
            'ledger_balance' => number_format($ledgerBalance, 2, '.', ''),
            'ledger_balance_formatted' => '₱' . number_format($ledgerBalance, 2, '.', ','),
            'last_updated' => $lastUpdated ? $lastUpdated->format('Y-m-d') : 'N/A',
            'last_updated_formatted' => $lastUpdated ? $lastUpdated->format('M d, Y') : 'N/A',
            'created_at' => $customer->create_date ? $customer->create_date->format('M d, Y') : 'N/A'
        ];
    }

    /**
     * Build service connections list
     *
     * @param Customer $customer
     * @return array
     */
    private function buildServiceConnections(Customer $customer): array
    {
        return $customer->serviceConnections->map(function ($connection) {
            $meterAssignment = $connection->meterAssignment;
            $meterNo = ($meterAssignment && $meterAssignment->meter)
                ? $meterAssignment->meter->mtr_serial
                : 'Not Assigned';

            return [
                'connection_id' => $connection->connection_id,
                'account_no' => $connection->account_no,
                'connection_type' => $connection->accountType?->at_description ?? 'N/A',
                'meter_no' => $meterNo,
                'status' => $connection->status?->stat_desc ?? 'UNKNOWN',
                'status_badge' => $this->getStatusBadgeData($connection->status?->stat_desc ?? 'UNKNOWN'),
                'started_at' => $connection->started_at ? $connection->started_at->format('M d, Y') : 'N/A',
                'area' => $connection->area?->a_desc ?? 'N/A'
            ];
        })->toArray();
    }

    /**
     * Calculate total unpaid bills
     *
     * @param Customer $customer
     * @return float
     */
    private function calculateTotalUnpaidBills(Customer $customer): float
    {
        $totalDebits = $customer->customerLedgerEntries
            ->where('source_type', 'BILL')
            ->sum('debit');

        $totalCredits = $customer->customerLedgerEntries
            ->where('source_type', 'PAYMENT')
            ->sum('credit');

        return max(0, $totalDebits - $totalCredits);
    }

    /**
     * Calculate ledger balance (all entries)
     *
     * @param Customer $customer
     * @return float
     */
    private function calculateLedgerBalance(Customer $customer): float
    {
        $totalDebits = $customer->customerLedgerEntries->sum('debit');
        $totalCredits = $customer->customerLedgerEntries->sum('credit');

        return $totalDebits - $totalCredits;
    }

    /**
     * Get status badge data for frontend
     *
     * @param string $status
     * @return array
     */
    private function getStatusBadgeData(string $status): array
    {
        $badges = [
            'ACTIVE' => [
                'text' => 'Active',
                'color' => 'green',
                'classes' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300'
            ],
            'PENDING' => [
                'text' => 'Pending',
                'color' => 'orange',
                'classes' => 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-300'
            ],
            'INACTIVE' => [
                'text' => 'Inactive',
                'color' => 'gray',
                'classes' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'
            ],
            'UNKNOWN' => [
                'text' => 'Unknown',
                'color' => 'gray',
                'classes' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'
            ]
        ];

        return $badges[$status] ?? $badges['UNKNOWN'];
    }

    /**
     * Get address parts as an array
     *
     * @param Customer $customer
     * @return array
     */
    private function getAddressParts(Customer $customer): array
    {
        if (!$customer->address) {
            return [
                'purok' => '',
                'barangay' => '',
                'town' => '',
                'province' => ''
            ];
        }

        return [
            'purok' => $customer->address->purok?->p_desc ?? '',
            'barangay' => $customer->address->barangay?->b_desc ?? '',
            'town' => $customer->address->town?->t_desc ?? '',
            'province' => $customer->address->province?->prov_desc ?? ''
        ];
    }

    // Note: formatLocation() method already exists in the service
}
```

#### Add Controller Method

**File:** `app/Http/Controllers/Customer/CustomerController.php`

```php
<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customers\CustomerService;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    protected CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    // ... existing methods ...

    /**
     * Get comprehensive customer details for details page
     *
     * @param int $id Customer ID
     * @return JsonResponse
     */
    public function getDetails(int $id): JsonResponse
    {
        try {
            $details = $this->customerService->getCustomerDetails($id);

            return response()->json([
                'success' => true,
                'data' => $details
            ], 200);

        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error("Failed to fetch customer details for ID {$id}: {$e->getMessage()}");

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'Failed to load customer details'
            ], $e->getMessage() === 'Customer not found' ? 404 : 500);
        }
    }
}
```

#### Add API Route

**File:** `routes/web.php` (add inside customer middleware group)

```php
// Inside the customers.view permission group (around line 106-119)
Route::middleware(['permission:customers.view'])->group(function () {
    // ... existing routes ...

    // API endpoint for customer details
    Route::get('/api/customer/{id}/details', [CustomerController::class, 'getDetails'])
        ->name('customer.details.api');
});
```

### 6.2 Frontend Implementation

#### Update JavaScript to Fetch Real Data

**File:** `resources/js/data/customer/customer-details-data.js`

**Replace entire file with:**

```javascript
/**
 * Customer Details Page - Real Data Implementation
 * Fetches customer data from backend API and populates the UI
 */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        loadingTimeout: 30000, // 30 seconds
        retryAttempts: 3,
        retryDelay: 2000 // 2 seconds
    };

    // State
    let currentCustomer = null;
    let currentAttempt = 0;

    /**
     * Initialize the page
     */
    async function init() {
        const customerId = getCustomerIdFromUrl();

        if (!customerId) {
            showError('Invalid customer ID in URL');
            return;
        }

        showLoading();
        await fetchCustomerDetails(customerId);
    }

    /**
     * Get customer ID from URL
     * @returns {string|null}
     */
    function getCustomerIdFromUrl() {
        const pathParts = window.location.pathname.split('/');
        const id = pathParts[pathParts.length - 1];
        return id && id !== 'details' ? id : null;
    }

    /**
     * Fetch customer details from API
     * @param {number} customerId
     */
    async function fetchCustomerDetails(customerId) {
        try {
            const response = await fetch(`/api/customer/${customerId}/details`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || 'Failed to load customer details');
            }

            currentCustomer = result.data;
            populateCustomerDetails(result.data);
            hideLoading();

        } catch (error) {
            console.error('Error fetching customer details:', error);

            // Retry logic
            if (currentAttempt < CONFIG.retryAttempts) {
                currentAttempt++;
                console.log(`Retrying... Attempt ${currentAttempt} of ${CONFIG.retryAttempts}`);

                setTimeout(() => {
                    fetchCustomerDetails(customerId);
                }, CONFIG.retryDelay);

                return;
            }

            hideLoading();
            showError(error.message || 'Failed to load customer details. Please try again.');
        }
    }

    /**
     * Populate customer details on the page
     * @param {Object} data
     */
    function populateCustomerDetails(data) {
        try {
            // Card 1: Customer Information
            populateCustomerInfo(data.customer_info);

            // Card 2: Meter & Billing
            populateMeterBilling(data.meter_billing);

            // Card 3: Account Status
            populateAccountStatus(data.account_status);

            // Service Connections Tab
            populateServiceConnections(data.service_connections || []);

            // Update page title
            document.title = `${data.customer_info.full_name} - Customer Details`;

        } catch (error) {
            console.error('Error populating customer details:', error);
            showError('Error displaying customer data. Some fields may be incomplete.');
        }
    }

    /**
     * Populate customer information card
     * @param {Object} info
     */
    function populateCustomerInfo(info) {
        setTextContent('consumer-id', info.customer_code || '-');
        setTextContent('consumer-name', info.full_name || '-');
        setTextContent('consumer-address', info.address || '-');
    }

    /**
     * Populate meter and billing card
     * @param {Object} billing
     */
    function populateMeterBilling(billing) {
        setTextContent('consumer-meter', billing.meter_no || '-');
        setTextContent('consumer-rate', billing.rate_class || '-');

        // Format bill with color based on amount
        const billElement = document.getElementById('consumer-bill');
        if (billElement) {
            billElement.textContent = billing.total_bill_formatted || '₱0.00';

            // Change color if there's an outstanding bill
            const billAmount = parseFloat(billing.total_bill || 0);
            if (billAmount > 0) {
                billElement.classList.add('text-red-600', 'dark:text-red-400');
                billElement.classList.remove('text-green-600', 'dark:text-green-400');
            } else {
                billElement.classList.add('text-green-600', 'dark:text-green-400');
                billElement.classList.remove('text-red-600', 'dark:text-red-400');
            }
        }
    }

    /**
     * Populate account status card
     * @param {Object} status
     */
    function populateAccountStatus(status) {
        // Status badge
        const statusElement = document.getElementById('consumer-status');
        if (statusElement) {
            statusElement.textContent = status.status_badge?.text || '-';
            statusElement.className = `inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${status.status_badge?.classes || ''}`;
        }

        // Ledger balance
        const ledgerElement = document.getElementById('consumer-ledger');
        if (ledgerElement) {
            ledgerElement.textContent = status.ledger_balance_formatted || '₱0.00';

            // Change color based on balance
            const balance = parseFloat(status.ledger_balance || 0);
            if (balance < 0) {
                ledgerElement.classList.add('text-red-600', 'dark:text-red-400');
                ledgerElement.classList.remove('text-green-600', 'dark:text-green-400');
            } else {
                ledgerElement.classList.add('text-green-600', 'dark:text-green-400');
                ledgerElement.classList.remove('text-red-600', 'dark:text-red-400');
            }
        }

        // Last updated
        setTextContent('consumer-updated', status.last_updated_formatted || '-');
    }

    /**
     * Populate service connections table
     * @param {Array} connections
     */
    function populateServiceConnections(connections) {
        const tbody = document.getElementById('connections-tbody');
        if (!tbody) return;

        if (connections.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        <i class="fas fa-plug text-3xl mb-2 opacity-50"></i>
                        <p>No service connections found</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = connections.map(conn => `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-white">${escapeHtml(conn.account_no)}</td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">${escapeHtml(conn.connection_type)}</td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">${escapeHtml(conn.area)}</td>
                <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-white">${escapeHtml(conn.meter_no)}</td>
                <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">${escapeHtml(conn.started_at)}</td>
                <td class="px-4 py-3 text-center">
                    <span class="px-2 py-1 text-xs rounded-full ${conn.status_badge?.classes || ''}">${escapeHtml(conn.status_badge?.text || conn.status)}</span>
                </td>
                <td class="px-4 py-3 text-center">
                    <button onclick="viewConnectionDetails(${conn.connection_id})"
                        class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded"
                        title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    /**
     * Show loading state
     */
    function showLoading() {
        // Show loading for all cards
        ['consumer-id', 'consumer-name', 'consumer-address',
         'consumer-meter', 'consumer-rate', 'consumer-bill',
         'consumer-status', 'consumer-ledger', 'consumer-updated'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.innerHTML = '<i class="fas fa-spinner fa-spin text-gray-400"></i>';
            }
        });
    }

    /**
     * Hide loading state
     */
    function hideLoading() {
        // Loading is hidden automatically when content is populated
    }

    /**
     * Show error message
     * @param {string} message
     */
    function showError(message) {
        // Show error in all main fields
        ['consumer-name', 'consumer-meter', 'consumer-status'].forEach(id => {
            setTextContent(id, 'Error loading data');
        });

        // Show alert
        alert(`Error: ${message}\n\nPlease refresh the page or contact support.`);
    }

    /**
     * Set text content safely
     * @param {string} elementId
     * @param {string} text
     */
    function setTextContent(elementId, text) {
        const element = document.getElementById(elementId);
        if (element) {
            element.textContent = text;
        }
    }

    /**
     * Escape HTML to prevent XSS
     * @param {string} text
     * @returns {string}
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Tab switching functionality
     * @param {string} tabName
     */
    function switchTab(tabName) {
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
    }

    /**
     * View connection details (placeholder)
     * @param {number} connectionId
     */
    function viewConnectionDetails(connectionId) {
        console.log('View connection details:', connectionId);
        // TODO: Implement modal or navigation to connection details
        alert(`Connection details for ID: ${connectionId}\n\nThis feature will be implemented soon.`);
    }

    // Expose functions globally
    window.switchTab = switchTab;
    window.viewConnectionDetails = viewConnectionDetails;
    window.currentCustomer = currentCustomer;

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
```

#### Update Blade Template (if needed)

**File:** `resources/views/pages/customer/customer-details.blade.php`

**Change the @vite directive** (around line 141):

```blade
{{-- Remove enhanced-customer-data.js since we're using real API now --}}
@vite(['resources/js/data/customer/customer-details-data.js'])
```

---

## 7. Testing Checklist

### Backend Testing

#### Unit Tests

- [ ] **Test `getCustomerDetails()` with valid customer ID**
  - Expected: Returns complete data structure
  - Verify: All fields are populated

- [ ] **Test with customer that has no service connections**
  - Expected: Returns customer info with "Not Assigned" for meter
  - Verify: No errors, graceful handling

- [ ] **Test with customer that has multiple connections**
  - Expected: Returns all connections in array
  - Verify: Active connection is identified correctly

- [ ] **Test with non-existent customer ID**
  - Expected: Throws exception "Customer not found"
  - Verify: Returns 404 status code

- [ ] **Test address formatting with incomplete address**
  - Expected: Shows "N/A" for missing parts
  - Verify: No null pointer errors

- [ ] **Test bill calculation with no ledger entries**
  - Expected: Returns "₱0.00"
  - Verify: Calculation doesn't break

#### Integration Tests

- [ ] **Test API endpoint `/api/customer/{id}/details`**
  - Expected: Returns JSON with success: true
  - Verify: Response structure matches design

- [ ] **Test with invalid authentication**
  - Expected: Returns 401 or 403
  - Verify: Middleware is working

- [ ] **Test with database connection error**
  - Expected: Returns 500 error
  - Verify: Error is logged

### Frontend Testing

#### Functional Tests

- [ ] **Page loads without JavaScript errors**
  - Browser console is clean
  - No 404 errors for assets

- [ ] **Data fetches successfully**
  - Spinner shows while loading
  - Data populates all fields
  - Loading spinner disappears

- [ ] **Error handling works**
  - Shows error message on API failure
  - Retry logic works
  - User can refresh page

- [ ] **All cards display correctly**
  - Customer Information card: ID, name, address
  - Meter & Billing card: Meter number, rate, bill
  - Account Status card: Status badge, balance, date

- [ ] **Tab switching works**
  - Documents tab shows content
  - Connections tab shows table
  - Tabs highlight correctly

#### Browser Compatibility

- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

#### Responsive Design

- [ ] Desktop (1920x1080)
- [ ] Tablet (768x1024)
- [ ] Mobile (375x667)

### Edge Cases

- [ ] **Customer with very long name**
  - Expected: Name wraps or truncates gracefully

- [ ] **Customer with special characters in address**
  - Expected: Characters are escaped (XSS prevention)

- [ ] **Customer with very large unpaid bill**
  - Expected: Number formats correctly (e.g., ₱1,234,567.89)

- [ ] **Network timeout**
  - Expected: Shows error after timeout
  - Retry logic activates

- [ ] **Customer with inactive status**
  - Expected: Gray badge shows
  - Data still displays correctly

### Performance Testing

- [ ] **Page load time < 2 seconds** (with fast network)
- [ ] **API response time < 500ms** (database query)
- [ ] **No memory leaks** (check browser DevTools)
- [ ] **Efficient queries** (use Laravel Debugbar to verify N+1 is avoided)

---

## Implementation Steps Summary

### Step-by-Step Execution Order

1. **Backend First (30 minutes)**
   - [ ] Add `getCustomerDetails()` to CustomerService
   - [ ] Add `getDetails()` to CustomerController
   - [ ] Add API route in web.php
   - [ ] Test API endpoint with Postman/Insomnia

2. **Frontend Next (20 minutes)**
   - [ ] Replace customer-details-data.js with new implementation
   - [ ] Update @vite directive in blade template
   - [ ] Test page load in browser

3. **Testing (15 minutes)**
   - [ ] Test with real customer IDs from database
   - [ ] Test error cases (invalid ID, network error)
   - [ ] Test on different browsers
   - [ ] Test responsive design

4. **Polish & Deploy (10 minutes)**
   - [ ] Review code for best practices
   - [ ] Add comments where needed
   - [ ] Commit changes with good message
   - [ ] Deploy to staging/production

**Total Estimated Time: 75 minutes (1 hour 15 minutes)**

---

## Notes & Considerations

### Important Reminders

1. **Use Existing Service Methods**: The CustomerService already has `getCustomerById()` and `formatLocation()` - reuse them!

2. **Eager Loading**: Always use `with()` to avoid N+1 query problems

3. **Status Check**: Use `Status::getIdByDescription(Status::ACTIVE)` to find active connections

4. **Error Logging**: Always log errors with context for debugging

5. **XSS Prevention**: Use `escapeHtml()` or Laravel's `{{ }}` syntax to prevent XSS

### Future Enhancements

1. **Add Caching**: Cache customer details for 5 minutes to reduce DB load
2. **Add Websockets**: Real-time updates when data changes
3. **Add Export**: PDF/Excel export of customer details
4. **Add History**: Show customer activity timeline
5. **Add Documents**: Display uploaded customer documents

---

## Questions & Decisions

### Decision Log

**Q: Should we use `routes/api.php` or `routes/web.php` for the API endpoint?**
**A:** Use `routes/web.php` since it's authenticated via web middleware and same-origin

**Q: Should we cache the customer details?**
**A:** Not in initial implementation. Add later if performance issues arise.

**Q: What if customer has no address?**
**A:** Show "N/A" and don't throw errors. Handle gracefully.

**Q: Should we show all service connections or just active ones?**
**A:** Show all connections in the table, but highlight active one in meter/billing card.

**Q: How to handle permission errors?**
**A:** Frontend should show "Access denied" message, backend returns 403 status.

---

## Conclusion

This implementation plan provides a complete roadmap for transitioning the customer details page from static dummy data to real database data. The approach follows the existing patterns in the codebase (CustomerService methods, API endpoints, fetch-based JavaScript) while handling edge cases and errors gracefully.

**Key Takeaways:**
- Backend provides a comprehensive `getCustomerDetails()` method
- Frontend uses modern fetch API with retry logic
- Error handling is robust on both sides
- Data structure is well-designed and extensible
- Testing checklist ensures quality

**Next Steps:**
1. Review this plan with the team
2. Begin implementation following the step-by-step order
3. Test thoroughly before deployment
4. Document any issues or improvements for future reference

---

**Document Version:** 1.0
**Last Updated:** 2026-01-26
**Author:** Claude Code (AI Assistant)
**Status:** Ready for Implementation
