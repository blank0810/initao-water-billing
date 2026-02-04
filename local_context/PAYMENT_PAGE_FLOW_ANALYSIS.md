# Payment Page Flow Analysis

> **For Claude:** This is a technical analysis document, not an implementation plan. Reference this document when working on payment-related features.

**Purpose:** Comprehensive documentation of the payment page architecture, data flow, and implementation gaps.

**Last Updated:** 2026-02-03

**Author:** Senior Engineer Analysis

---

## Table of Contents

1. [Overview](#overview)
2. [Entry Point & Dashboard](#entry-point--dashboard)
3. [Pending Payments Flow](#pending-payments-flow)
4. [Payment Processing Flow](#payment-processing-flow)
5. [Database Operations](#database-operations)
6. [Payment Types Analysis](#payment-types-analysis)
7. [Implementation Gaps](#implementation-gaps)
8. [File Reference](#file-reference)

---

## Overview

The payment system in Initao Water Billing has **two separate interfaces**:

| System | URL | Purpose | Status |
|--------|-----|---------|--------|
| **Payment Management** | `/customer/payment-management` | Application fees | ✅ Fully Working |
| **Billing & Payments** | `/billing/management` | Water bills | ❌ UI only, no backend |

**Current Implementation:** Only **Application Fees** (from `ServiceApplication` + `CustomerCharge`) are processed through the Payment Management page. Water bill payments have a UI mockup but no backend integration.

---

## Entry Point & Dashboard

### Route Definition

**File:** `routes/web.php:197`

```php
Route::get('/customer/payment-management', [PaymentController::class, 'index'])
    ->name('payment.management');
```

### Controller Method

**File:** `app/Http/Controllers/Payment/PaymentController.php:56-66`

```php
public function index()
{
    return view('pages.payment.payment-management');
}
```

### View Structure

**File:** `resources/views/pages/payment/payment-management.blade.php`

The dashboard has a **dual-tab interface**:

```
┌──────────────────────────────────────────────────┐
│  PAYMENT MANAGEMENT                               │
├────────────────┬─────────────────────────────────┤
│ Pending Payments│  My Transactions               │
│    (active)    │                                  │
├────────────────┴─────────────────────────────────┤
│  [Statistics Cards: Pending, Today, Month]       │
│                                                   │
│  [DataTable with pending payments]               │
└──────────────────────────────────────────────────┘
```

---

## Pending Payments Flow

### Step 1: Frontend Request

When the page loads, JavaScript makes an AJAX call:

```
GET /api/payments/pending?type={optional}&search={optional}
```

**Route:** `routes/web.php:197`
```php
Route::get('/api/payments/pending', [PaymentController::class, 'getPendingPayments'])
    ->name('api.payments.pending');
```

### Step 2: Controller Layer

**File:** `app/Http/Controllers/Payment/PaymentController.php:68-79`

```php
public function getPendingPayments(Request $request): JsonResponse
{
    $type = $request->query('type');
    $search = $request->query('search');

    $pendingPayments = $this->paymentManagementService->getPendingPayments($type, $search);

    return response()->json([
        'success' => true,
        'data' => $pendingPayments,
    ]);
}
```

### Step 3: Service Layer

**File:** `app/Services/Payment/PaymentManagementService.php:30-68`

```php
public function getPendingPayments(?string $type = null, ?string $search = null): Collection
{
    $pendingItems = collect();

    // Currently only APPLICATION_FEE is implemented
    if ($type === null || $type === self::TYPE_APPLICATION_FEE) {
        $pendingItems = $pendingItems->merge($this->getPendingApplicationFees($search));
    }

    return $pendingItems;
}
```

### Step 4: Database Query

**File:** `app/Services/Payment/PaymentManagementService.php:113-175`

```php
protected function getPendingApplicationFees(?string $search = null): Collection
{
    $verifiedStatusId = Status::getIdByDescription(Status::VERIFIED);

    $query = ServiceApplication::query()
        ->where('stat_id', $verifiedStatusId)    // Must be VERIFIED
        ->whereNull('payment_id')                 // Must NOT have a payment yet
        ->with([
            'customer',
            'address.purok',
            'address.barangay',
            'customerCharges.chargeItem',
            'status',
        ]);

    // Optional search filter
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('application_number', 'like', "%{$search}%")
              ->orWhereHas('customer', function ($q) use ($search) {
                  $q->where('cust_fname', 'like', "%{$search}%")
                    ->orWhere('cust_lname', 'like', "%{$search}%")
                    ->orWhere('resolution_no', 'like', "%{$search}%");
              });
        });
    }

    return $query->get()->map(function ($application) {
        return $this->formatPendingItem($application, self::TYPE_APPLICATION_FEE);
    });
}
```

### Filtering Logic

| Filter | Purpose | Table/Column |
|--------|---------|--------------|
| `stat_id = VERIFIED` | Only show applications that passed verification | `ServiceApplication.stat_id` |
| `payment_id IS NULL` | Exclude already-paid applications | `ServiceApplication.payment_id` |
| Search by name/number | User-initiated filter | `ServiceApplication.application_number`, `customer.cust_fname`, etc. |

### Data Flow Diagram

```
Browser                    Controller                    Service                         Database
   │                          │                            │                                │
   │  GET /api/payments/      │                            │                                │
   │  pending?search=Juan     │                            │                                │
   │─────────────────────────>│                            │                                │
   │                          │  getPendingPayments(       │                                │
   │                          │    type=null,              │                                │
   │                          │    search='Juan')          │                                │
   │                          │───────────────────────────>│                                │
   │                          │                            │  SELECT * FROM                 │
   │                          │                            │  ServiceApplication            │
   │                          │                            │  WHERE stat_id = {VERIFIED}    │
   │                          │                            │  AND payment_id IS NULL        │
   │                          │                            │  AND (customer.cust_fname      │
   │                          │                            │       LIKE '%Juan%' OR ...)    │
   │                          │                            │───────────────────────────────>│
   │                          │                            │<───────────────────────────────│
   │                          │                            │  [Collection of apps]          │
   │                          │<───────────────────────────│                                │
   │                          │  [Formatted JSON]          │                                │
   │<─────────────────────────│                            │                                │
   │  { success: true,        │                            │                                │
   │    data: [...] }         │                            │                                │
```

### Tables Involved

| Table | Role |
|-------|------|
| `ServiceApplication` | Primary source - contains application status and payment reference |
| `customer` | Customer details (name, resolution_no) |
| `ConsumerAddress` | Address linked to application |
| `Purok`, `Barangay` | Address components |
| `CustomerCharge` | Individual line items (fees) for the application |
| `ChargeItem` | Fee type definitions (e.g., "Installation Fee", "Meter Deposit") |
| `Status` | Status lookup table |

---

## Payment Processing Flow

### Step 1: Click "Process" Button

From the pending payments table, each row has a "Process" button:

```
GET /payment/process/application/{applicationId}
```

**Route:** `routes/web.php:202`
```php
Route::get('/payment/process/application/{id}', [PaymentController::class, 'processApplicationPayment'])
    ->name('payment.process.application');
```

### Step 2: Load Payment Form

**File:** `app/Http/Controllers/Payment/PaymentController.php:81-109`

```php
public function processApplicationPayment(int $id)
{
    $verifiedStatusId = Status::getIdByDescription(Status::VERIFIED);

    $application = ServiceApplication::where('service_application_id', $id)
        ->where('stat_id', $verifiedStatusId)
        ->whereNull('payment_id')
        ->with([
            'customer',
            'address.purok',
            'address.barangay',
            'customerCharges.chargeItem',
        ])
        ->firstOrFail();

    // Calculate total from all charges
    $totalAmount = $application->customerCharges->sum(function ($charge) {
        return $charge->quantity * $charge->unit_amount;
    });

    return view('pages.payment.process-payment', [
        'application' => $application,
        'totalAmount' => $totalAmount,
    ]);
}
```

**Security Checks:**
- `stat_id = VERIFIED` - Prevents processing unverified applications
- `payment_id IS NULL` - Prevents double-processing

### Step 3: Payment Form UI

**File:** `resources/views/pages/payment/process-payment.blade.php`

```
┌─────────────────────────────────────────────────────────┐
│  PROCESS PAYMENT                                         │
├─────────────────────────────────────────────────────────┤
│  Customer: Juan Dela Cruz                               │
│  Application #: APP-2026-00001                          │
│  Address: Purok 1, Barangay Initao                      │
├─────────────────────────────────────────────────────────┤
│  CHARGES:                                               │
│  ┌─────────────────────────────────────────────────┐   │
│  │ Installation Fee          1 × ₱500.00 = ₱500.00 │   │
│  │ Meter Deposit             1 × ₱800.00 = ₱800.00 │   │
│  │ Processing Fee            1 × ₱200.00 = ₱200.00 │   │
│  └─────────────────────────────────────────────────┘   │
│                                                         │
│  TOTAL DUE:                              ₱1,500.00     │
├─────────────────────────────────────────────────────────┤
│  Amount Received: [_______________]                     │
│                                                         │
│  Change:          ₱0.00 (calculated live)              │
│                                                         │
│  [Cancel]                          [Process Payment]   │
└─────────────────────────────────────────────────────────┘
```

### Step 4: Submit Payment

```
POST /connection/service-application/{id}/process-payment
```

**Route:** `routes/web.php:227`
```php
Route::post('/connection/service-application/{id}/process-payment',
    [ServiceApplicationController::class, 'processPayment'])
    ->name('service-application.process-payment');
```

### Step 5: Controller Receives Payment

**File:** `app/Http/Controllers/Connection/ServiceApplicationController.php`

```php
public function processPayment(int $id, Request $request)
{
    $request->validate([
        'amount_received' => 'required|numeric|min:0',
    ]);

    $result = $this->paymentService->processApplicationPayment(
        $id,
        $request->amount_received,
        auth()->id()
    );

    return redirect()->route('payment.receipt', $result['payment_id'])
        ->with('success', 'Payment processed successfully');
}
```

### Step 6: PaymentService - Core Transaction

**File:** `app/Services/Payment/PaymentService.php:35-120`

```php
public function processApplicationPayment(
    int $applicationId,
    float $amountReceived,
    int $userId
): array {
    return DB::transaction(function () use ($applicationId, $amountReceived, $userId) {

        // 1. Fetch and validate application
        $application = ServiceApplication::where('service_application_id', $applicationId)
            ->where('stat_id', Status::getIdByDescription(Status::VERIFIED))
            ->whereNull('payment_id')
            ->with('customerCharges')
            ->lockForUpdate()  // Prevent race conditions
            ->firstOrFail();

        // 2. Calculate total due
        $totalDue = $application->customerCharges->sum(function ($charge) {
            return $charge->quantity * $charge->unit_amount;
        });

        // 3. Validate payment amount (must cover full amount)
        if ($amountReceived < $totalDue) {
            throw new \InvalidArgumentException(
                "Amount received ({$amountReceived}) is less than total due ({$totalDue})"
            );
        }

        // 4. Create Payment record
        $payment = Payment::create([
            'receipt_no' => $this->generateReceiptNumber(),
            'payer_id' => $application->customer_id,
            'payment_date' => now(),
            'amount_received' => $amountReceived,
            'user_id' => $userId,
            'stat_id' => Status::getIdByDescription(Status::ACTIVE),
        ]);

        // 5. Create PaymentAllocation for each charge
        foreach ($application->customerCharges as $charge) {
            $chargeAmount = $charge->quantity * $charge->unit_amount;

            PaymentAllocation::create([
                'payment_id' => $payment->payment_id,
                'target_type' => 'CHARGE',
                'target_id' => $charge->charge_id,
                'amount_applied' => $chargeAmount,
            ]);

            // 6. Record ledger entry (CREDIT for payment)
            $this->ledgerService->recordPaymentEntry(
                $application->customer_id,
                $payment->payment_id,
                $chargeAmount,
                $charge->description,
                $userId
            );

            // 7. Mark charge as PAID
            $charge->update([
                'stat_id' => Status::getIdByDescription(Status::PAID),
            ]);
        }

        // 8. Update application status to PAID
        $paidStatusId = Status::getIdByDescription(Status::PAID);
        $application->update([
            'stat_id' => $paidStatusId,
            'payment_id' => $payment->payment_id,
            'paid_at' => now(),
        ]);

        // 9. Calculate change
        $change = $amountReceived - $totalDue;

        return [
            'payment_id' => $payment->payment_id,
            'receipt_no' => $payment->receipt_no,
            'total_due' => $totalDue,
            'amount_received' => $amountReceived,
            'change' => $change,
        ];
    });
}
```

---

## Database Operations

### Tables Modified During Payment Processing

| Table | Action | Data |
|-------|--------|------|
| `Payment` | INSERT | New payment record with receipt number, payer, amount, cashier |
| `PaymentAllocation` | INSERT (per charge) | Links payment to each charge with amount applied |
| `CustomerLedger` | INSERT (per charge) | CREDIT entry for each payment allocation |
| `CustomerCharge` | UPDATE (per charge) | Set `stat_id` = PAID |
| `ServiceApplication` | UPDATE | Set `stat_id` = PAID, `payment_id`, `paid_at` |

### Visual Flow of Database Operations

```
┌─────────────────────────────────────────────────────────────────────────────┐
│  DB::transaction() START                                                     │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  ┌─────────────────┐                                                        │
│  │ Payment         │  INSERT: receipt_no=OR-2026-00001                      │
│  │ (payment_id=99) │          payer_id=45, amount=1500, user_id=3           │
│  └────────┬────────┘                                                        │
│           │                                                                  │
│           │ payment_id=99                                                    │
│           ▼                                                                  │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │ PaymentAllocation (one per charge)                                   │   │
│  │                                                                       │   │
│  │  ┌──────────────────────────────────────────────────────────────┐   │   │
│  │  │ payment_id=99, target_type='CHARGE', target_id=201, amt=500  │   │   │
│  │  │ payment_id=99, target_type='CHARGE', target_id=202, amt=800  │   │   │
│  │  │ payment_id=99, target_type='CHARGE', target_id=203, amt=200  │   │   │
│  │  └──────────────────────────────────────────────────────────────┘   │   │
│  └─────────────────────────────────────────────────────────────────────┘   │
│           │                                                                  │
│           ▼                                                                  │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │ CustomerLedger (CREDIT entries)                                      │   │
│  │                                                                       │   │
│  │  ┌──────────────────────────────────────────────────────────────┐   │   │
│  │  │ source_type='PAYMENT', source_id=99, credit=500, debit=0     │   │   │
│  │  │ source_type='PAYMENT', source_id=99, credit=800, debit=0     │   │   │
│  │  │ source_type='PAYMENT', source_id=99, credit=200, debit=0     │   │   │
│  │  └──────────────────────────────────────────────────────────────┘   │   │
│  └─────────────────────────────────────────────────────────────────────┘   │
│           │                                                                  │
│           ▼                                                                  │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │ CustomerCharge (UPDATE each to PAID)                                 │   │
│  │                                                                       │   │
│  │  charge_id=201: stat_id → PAID                                       │   │
│  │  charge_id=202: stat_id → PAID                                       │   │
│  │  charge_id=203: stat_id → PAID                                       │   │
│  └─────────────────────────────────────────────────────────────────────┘   │
│           │                                                                  │
│           ▼                                                                  │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │ ServiceApplication (UPDATE)                                          │   │
│  │                                                                       │   │
│  │  stat_id → PAID                                                       │   │
│  │  payment_id → 99                                                      │   │
│  │  paid_at → 2026-02-03 10:30:00                                       │   │
│  └─────────────────────────────────────────────────────────────────────┘   │
│                                                                              │
├─────────────────────────────────────────────────────────────────────────────┤
│  DB::transaction() COMMIT                                                    │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Why Application Disappears from Pending

After payment, `ServiceApplication.payment_id` is set to the new payment ID. The pending payments query filters by `whereNull('payment_id')`, so the application no longer matches and disappears from the list.

---

## Payment Types Analysis

### Type Constants Defined

**File:** `app/Services/Payment/PaymentManagementService.php:20-28`

```php
class PaymentManagementService
{
    public const TYPE_APPLICATION_FEE = 'APPLICATION_FEE';
    public const TYPE_WATER_BILL = 'WATER_BILL';        // Defined but NOT used
    public const TYPE_MISC_CHARGE = 'MISC_CHARGE';      // Defined but NOT used
    public const TYPE_RECONNECTION = 'RECONNECTION';    // Defined but NOT used
    public const TYPE_OTHER = 'OTHER';                  // Defined but NOT used
}
```

### Current Implementation Status

| Type | Constant | Source Table | Status |
|------|----------|--------------|--------|
| Application Fees | `TYPE_APPLICATION_FEE` | `ServiceApplication` + `CustomerCharge` | ✅ Implemented |
| Water Bills (Modern) | `TYPE_WATER_BILL` | `water_bill_history` | ❌ Not implemented |
| Water Bills (Legacy) | - | `water_bill` | ❌ Not implemented |
| Miscellaneous Bills | `TYPE_MISC_CHARGE` | `misc_bill` | ❌ Not implemented |
| Reconnection Fees | `TYPE_RECONNECTION` | Could be `CustomerCharge` | ❌ Not implemented |
| Other Charges | `TYPE_OTHER` | Various | ❌ Not implemented |

---

## Implementation Gaps

### Complete Gap Analysis

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                     PAYMENT PROCESSING STATUS                                │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  ✅ FULLY IMPLEMENTED (Payment Management Page):                            │
│  ┌────────────────────────────────────────────────────────────────────┐    │
│  │ • Application Fees (ServiceApplication + CustomerCharge)            │    │
│  │   - Has backend PaymentService                                      │    │
│  │   - Creates Payment, PaymentAllocation records                      │    │
│  │   - Records CustomerLedger entries                                  │    │
│  │   - Generates receipt numbers                                       │    │
│  │   - Full transaction support                                        │    │
│  └────────────────────────────────────────────────────────────────────┘    │
│                                                                              │
│  ❌ UI EXISTS BUT NO BACKEND (Billing Page - /billing/management):          │
│  ┌────────────────────────────────────────────────────────────────────┐    │
│  │ • Water Bill Payments                                               │    │
│  │   - Modal exists (payment-modal.blade.php)                          │    │
│  │   - Uses HARDCODED customer data (lines 144-153)                    │    │
│  │   - Submit function only does console.log() (line 268)              │    │
│  │   - NO API endpoint to process payment                              │    │
│  │   - NO PaymentService method for water bills                        │    │
│  │   - Collections tab shows payments but source is unclear            │    │
│  └────────────────────────────────────────────────────────────────────┘    │
│                                                                              │
│  ❌ NOT IMPLEMENTED AT ALL:                                                 │
│  ┌────────────────────────────────────────────────────────────────────┐    │
│  │ • Miscellaneous Bill Payments (misc_bill table)                     │    │
│  │ • Reconnection Fee Payments                                         │    │
│  │ • Penalty/Interest Payments                                         │    │
│  │ • Standalone CustomerCharge Payments (non-application)              │    │
│  │ • Partial Payment Support                                           │    │
│  │ • Multi-bill Payment (pay multiple bills at once)                   │    │
│  │ • Advance Payment / Credit Balance                                  │    │
│  └────────────────────────────────────────────────────────────────────┘    │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Evidence from Code

| File | Line | Evidence |
|------|------|----------|
| `payment-modal.blade.php` | 144-153 | Hardcoded `customerData` object with fake customers |
| `payment-modal.blade.php` | 268 | `console.log('Payment processed:', ...)` - no API call |
| `PaymentManagementService.php` | 30-50 | Only `TYPE_APPLICATION_FEE` branch implemented |
| `PaymentService.php` | 35-120 | Only `processApplicationPayment()` method exists |
| `routes/web.php` | 251-260 | Water bill routes are read-only (no POST for payments) |

### What Would Be Needed to Add Water Bill Payments

1. **New Service Method:** `PaymentService@processWaterBillPayment()`
2. **New API Endpoint:** `POST /api/billing/process-payment`
3. **Query Unpaid Bills:** Method to get unpaid `water_bill_history` records
4. **PaymentAllocation:** Create with `target_type = 'BILL'`, `target_id = bill_id`
5. **Ledger Integration:** Record CREDIT entries for bill payments
6. **Update Bill Status:** Mark bills as paid in `water_bill_history`

### Architectural Decision Point

Two paths forward:

1. **Consolidate** - Extend the Payment Management page to handle all payment types (application fees, water bills, misc charges)

2. **Keep Separate** - Build out the billing page's payment modal with its own backend, keeping application fees and water bills in different interfaces

The current code structure (with `TYPE_WATER_BILL` constant already defined) suggests the original intent was **Option 1** - a unified payment page.

---

## File Reference

### Controllers

| File | Purpose |
|------|---------|
| `app/Http/Controllers/Payment/PaymentController.php` | Main payment management controller |
| `app/Http/Controllers/Connection/ServiceApplicationController.php` | Handles payment submission |

### Services

| File | Purpose |
|------|---------|
| `app/Services/Payment/PaymentManagementService.php` | Fetches pending payments, statistics, cashier transactions |
| `app/Services/Payment/PaymentService.php` | Core payment processing logic |
| `app/Services/Ledger/LedgerService.php` | Records ledger entries |

### Models

| File | Purpose |
|------|---------|
| `app/Models/Payment.php` | Payment record |
| `app/Models/PaymentAllocation.php` | Links payments to charges/bills |
| `app/Models/CustomerCharge.php` | Individual charge items |
| `app/Models/CustomerLedger.php` | Double-entry ledger |
| `app/Models/ServiceApplication.php` | Service applications |

### Views

| File | Purpose |
|------|---------|
| `resources/views/pages/payment/payment-management.blade.php` | Main dashboard |
| `resources/views/pages/payment/partials/my-transactions-tab.blade.php` | Cashier transactions tab |
| `resources/views/pages/payment/process-payment.blade.php` | Payment processing form |
| `resources/views/pages/payment/payment-receipt.blade.php` | Receipt display |

### Migrations

| File | Table |
|------|-------|
| `0025_payments_table.php` | `Payment` |
| `0030_payment_allocations_table.php` | `PaymentAllocation` |
| `0027_customer_ledger_table.php` | `CustomerLedger` |

---

## Key Technical Points

1. **Transaction wrapping** - All payment operations are atomic; if any step fails, everything rolls back
2. **lockForUpdate()** - Prevents race conditions if two cashiers try to process the same application
3. **Full payment required** - No partial payments currently supported for application fees
4. **Double-entry ledger** - Charges create DEBIT entries (at verification), payments create CREDIT entries (balances out)
5. **Polymorphic allocation** - `PaymentAllocation.target_type` can be 'CHARGE' or 'BILL'
6. **Polymorphic ledger** - `CustomerLedger.source_type` can be BILL, CHARGE, PAYMENT, ADJUST, etc.
7. **Receipt generation** - OR-YYYY-NNNNN format, auto-incremented per year

---

## Polymorphic Relationships Reference

### PaymentAllocation.target_type

| Value | Links To |
|-------|----------|
| `'CHARGE'` | `CustomerCharge.charge_id` |
| `'BILL'` | `WaterBillHistory.bill_id` |

### CustomerLedger.source_type

| Value | Links To |
|-------|----------|
| `'BILL'` | Water bill entry |
| `'CHARGE'` | Application charge entry |
| `'PAYMENT'` | Payment received entry |
| `'ADJUST'` | Adjustment entry |
| `'REFUND'` | Refund entry |
| `'WRITE_OFF'` | Write-off entry |
| `'TRANSFER'` | Transfer entry |
| `'REVERSAL'` | Reversal entry |

**Important:** Always check `source_type`/`target_type` before accessing polymorphic relations.

---

*End of Document*
