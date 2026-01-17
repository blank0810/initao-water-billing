# Complete Service Application to Connection Flow - Comprehensive Reference

## Overview

This document provides a comprehensive breakdown of the service application to connection workflow, including:
- Database tables written at each step
- Actual code snippets from service methods
- UI/View flow showing which forms trigger each action
- Error scenarios and validation failures

---

## Visual Flow Diagram

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                    SERVICE APPLICATION → CONNECTION FLOW                         │
└─────────────────────────────────────────────────────────────────────────────────┘

    ┌──────────────┐
    │   CUSTOMER   │
    │   APPLIES    │
    └──────┬───────┘
           │
           ▼
┌──────────────────────┐
│  1. SUBMITTED        │     Tables: customer, consumer_address, ServiceApplication
│     (Auto-VERIFIED)  │              CustomerCharge, CustomerLedger
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│  2. PAID             │     Tables: Payment, PaymentAllocation, CustomerLedger
│                      │              ServiceApplication (update), CustomerCharge (update)
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│  3. SCHEDULED        │     Tables: ServiceApplication (update only)
│                      │
└──────────┬───────────┘
           │
           ▼
┌──────────────────────┐
│  4. CONNECTED        │     Tables: ServiceConnection, meter, MeterAssignment
│                      │              ServiceApplication (update)
└──────────────────────┘
```

---

# STAGE 1: Application Creation (SUBMITTED → Auto-VERIFIED)

## UI Flow

| Step | View/Component | URL | User Action |
|------|----------------|-----|-------------|
| 1 | `pages/application/service-application.blade.php` | `/connection/service-application/create` | Fill 4-step wizard form |
| 2 | Alpine.js `submitApplication()` | POST `/connection/service-application` | Click "Submit Application" |
| 3 | Success redirect | `/connection/service-application/{id}` | View application details |

## Form Fields Collected

**Step 1: Customer Type**
- `customerType`: 'new' or 'existing'

**Step 2: Customer Details (if new)**
- `customer.firstName`, `customer.lastName`, `customer.middleName`
- `customer.phone`, `customer.idType`, `customer.idNumber`
- `customer.barangay`, `customer.purok`, `customer.landmark` (home address)

**Step 3: Application Details (service location)**
- `application.barangay`, `application.purok`, `application.landmark`

---

## Code Snippet: ServiceApplicationService::createApplication()

```php
public function createApplication(string $customerType, array $customerData, array $applicationData, ?int $userId = null): array
{
    return DB::transaction(function () use ($customerType, $customerData, $applicationData, $userId) {
        // Transform data from camelCase to snake_case
        $transformedCustomer = $this->transformCustomerData($customerData);
        $transformedApplication = $this->transformApplicationData($applicationData);

        // Get status IDs
        $verifiedStatusId = Status::getIdByDescription(Status::VERIFIED);
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $pendingStatusId = Status::getIdByDescription(Status::PENDING);

        // ACTION 1.1: Create SERVICE ADDRESS (where water connection will be installed)
        $serviceAddress = ConsumerAddress::create([
            'p_id' => $transformedApplication['p_id'],
            'b_id' => $transformedApplication['b_id'],
            't_id' => 1, // Initao
            'prov_id' => 1, // Misamis Oriental
            'stat_id' => $activeStatusId,
        ]);

        // ACTION 1.2: Handle customer based on type
        if ($customerType === 'new') {
            // Generate resolution number
            $resolutionNo = CustomerHelper::generateCustomerResolutionNumber(
                $transformedCustomer['cust_first_name'],
                $transformedCustomer['cust_last_name']
            );

            // Create customer's HOME address (separate from service address)
            if ($customerData['barangay'] && $customerData['purok']) {
                $customerAddress = ConsumerAddress::create([
                    'p_id' => $customerData['purok'],
                    'b_id' => $customerData['barangay'],
                    't_id' => 1,
                    'prov_id' => 1,
                    'stat_id' => $activeStatusId,
                ]);
                $customerAddressId = $customerAddress->ca_id;
            } else {
                $customerAddressId = $serviceAddress->ca_id;
            }

            // Create new customer with HOME address
            $customer = Customer::create([
                'cust_first_name' => $transformedCustomer['cust_first_name'],
                'cust_middle_name' => $transformedCustomer['cust_middle_name'] ?? null,
                'cust_last_name' => $transformedCustomer['cust_last_name'],
                'contact_number' => $transformedCustomer['contact_number'] ?? null,
                'id_type' => $transformedCustomer['id_type'] ?? null,
                'id_number' => $transformedCustomer['id_number'] ?? null,
                'ca_id' => $customerAddressId,
                'land_mark' => $customerLandmark ?? null,
                'stat_id' => $pendingStatusId,
                'c_type' => $transformedCustomer['c_type'] ?? 'RESIDENTIAL',
                'resolution_no' => $resolutionNo,
                'create_date' => now(),
            ]);
        } else {
            // Existing customer - DO NOT update their home address
            $customer = Customer::findOrFail($customerData['customerId']);
        }

        // ACTION 1.3: Generate application number
        $applicationNumber = 'APP-'.date('Y').'-'.str_pad(
            ServiceApplication::count() + 1,
            5, '0', STR_PAD_LEFT
        );

        // ACTION 1.3: Create service application with VERIFIED status (auto-verify)
        $application = ServiceApplication::create([
            'customer_id' => $customer->cust_id,
            'address_id' => $serviceAddress->ca_id,
            'application_number' => $applicationNumber,
            'submitted_at' => now(),
            'verified_at' => now(),  // Auto-verified immediately
            'verified_by' => $userId,
            'stat_id' => $verifiedStatusId,  // Skip PENDING
            'remarks' => $applicationData['remarks'] ?? null,
        ]);

        // ACTION 1.4: Generate charges immediately
        $charges = $this->chargeService->generateApplicationCharges($application);

        // ACTION 1.5: Record charges in ledger as DEBIT entries
        $this->ledgerService->recordCharges($charges, $userId ?? 1);

        return [
            'customer' => $customer->fresh(['address', 'status']),
            'application' => $application->fresh(['customer', 'address', 'status', 'customerCharges']),
            'charges' => $charges,
            'total_amount' => $charges->sum(fn ($c) => $c->total_amount),
        ];
    });
}
```

---

## Database Tables Written - Stage 1

### Action 1.1: Create Service Address

**Table:** `consumer_address`

| Column | Value | Source |
|--------|-------|--------|
| `ca_id` | Auto-increment | System |
| `p_id` | Selected purok ID | Form: `application.purok` |
| `b_id` | Selected barangay ID | Form: `application.barangay` |
| `t_id` | 1 | Hardcoded (Initao) |
| `prov_id` | 1 | Hardcoded (Misamis Oriental) |
| `stat_id` | ACTIVE status ID | `Status::getIdByDescription(Status::ACTIVE)` |

---

### Action 1.2: Create Customer (if new)

**Table:** `customer`

| Column | Value | Source |
|--------|-------|--------|
| `cust_id` | Auto-increment | System |
| `create_date` | Current datetime | `now()` |
| `cust_first_name` | First name | Form: `customer.firstName` |
| `cust_middle_name` | Middle name | Form: `customer.middleName` |
| `cust_last_name` | Last name | Form: `customer.lastName` |
| `ca_id` | Home address ID | Customer's home, not service location |
| `land_mark` | Landmark | Form: `customer.landmark` |
| `stat_id` | PENDING status ID | `Status::getIdByDescription(Status::PENDING)` |
| `c_type` | 'RESIDENTIAL' | Default |
| `resolution_no` | Generated | `CustomerHelper::generateCustomerResolutionNumber()` → `INITAO-ABC-1234567890` |
| `contact_number` | Phone | Form: `customer.phone` |
| `id_type` | ID type | Form: `customer.idType` |
| `id_number` | ID number | Form: `customer.idNumber` |

---

### Action 1.3: Create Service Application

**Table:** `ServiceApplication`

| Column | Value | Source |
|--------|-------|--------|
| `application_id` | Auto-increment | System |
| `customer_id` | Customer's ID | `$customer->cust_id` |
| `address_id` | Service address ID | `$serviceAddress->ca_id` |
| `application_number` | Generated | `APP-YYYY-NNNNN` |
| `submitted_at` | Current datetime | `now()` |
| `verified_at` | Current datetime | `now()` (auto-verified) |
| `verified_by` | Current user ID | `Auth::id()` |
| `stat_id` | VERIFIED status ID | Auto-verified |

**Columns remain NULL:** `paid_at`, `payment_id`, `scheduled_at`, `scheduled_connection_date`, `scheduled_by`, `connected_at`, `connection_id`

---

### Action 1.4: Generate Charges

**Table:** `CustomerCharge` (one per ChargeItem with type='APPLICATION')

| Column | Value | Source |
|--------|-------|--------|
| `charge_id` | Auto-increment | System |
| `customer_id` | Customer's ID | `$application->customer_id` |
| `application_id` | Application ID | `$application->application_id` |
| `connection_id` | NULL | Not connected yet |
| `charge_item_id` | Charge item ID | From `ChargeItem` record |
| `description` | Charge name | From `ChargeItem.name` |
| `quantity` | 1.000 | Default |
| `unit_amount` | Fee amount | From `ChargeItem.default_amount` |
| `total_amount` | Calculated | `quantity × unit_amount` |
| `due_date` | Current + 7 days | `now()->addDays(7)` |
| `stat_id` | PENDING | `Status::getIdByDescription(Status::PENDING)` |

---

### Action 1.5: Record Ledger Entries (DEBIT)

**Table:** `CustomerLedger` (one per charge)

| Column | Value | Source |
|--------|-------|--------|
| `ledger_entry_id` | Auto-increment | System |
| `customer_id` | Customer's ID | `$charge->customer_id` |
| `connection_id` | NULL | Not connected yet |
| `period_id` | NULL | Not tied to billing period |
| `txn_date` | Current date | `now()->toDateString()` |
| `post_ts` | Current timestamp | `CURRENT_TIMESTAMP(6)` |
| `source_type` | 'CHARGE' | Enum |
| `source_id` | Charge ID | `$charge->charge_id` |
| `description` | Charge description | `$charge->description` |
| `debit` | Charge amount | `$charge->total_amount` |
| `credit` | 0.00 | DEBIT entry |
| `user_id` | Current user | `Auth::id()` |
| `stat_id` | ACTIVE | Status ID |

---

## Error Scenarios - Stage 1

| Error | Condition | Message | HTTP Code |
|-------|-----------|---------|-----------|
| Validation failed | Missing required fields | Laravel validation errors | 422 |
| Customer not found | Existing customer ID invalid | "No query results for model [Customer]" | 404 |
| Database error | Transaction failure | Exception message | 400 |

---

# STAGE 2: Payment Processing (VERIFIED → PAID)

## UI Flow

| Step | View/Component | URL | User Action |
|------|----------------|-----|-------------|
| 1 | `pages/connection/service-application-detail.blade.php` | `/connection/service-application/{id}` | View application |
| 2 | Payment button (when status=VERIFIED) | Modal opens | Click "Process Payment" |
| 3 | Payment form | POST `/connection/service-application/{id}/process-payment` | Enter amount, submit |
| 4 | Success | Shows receipt number | Payment complete |

---

## Code Snippet: PaymentService::processApplicationPayment()

```php
public function processApplicationPayment(int $applicationId, float $amountReceived, int $userId): array
{
    $application = ServiceApplication::with('customer')->findOrFail($applicationId);

    // VALIDATION: Application must be VERIFIED
    if ($application->stat_id !== Status::getIdByDescription(Status::VERIFIED)) {
        throw new \Exception('Payment can only be processed for VERIFIED applications.');
    }

    // Get charges total
    $chargesData = $this->chargeService->getApplicationChargesTotal($applicationId);

    if ($chargesData['charges']->isEmpty()) {
        throw new \Exception('No charges found for this application.');
    }

    $totalDue = $chargesData['remaining_amount'];

    // VALIDATION: Full payment required
    if ($amountReceived < $totalDue) {
        throw new \Exception('Full payment required. Amount due: ₱'.number_format($totalDue, 2).
            '. Received: ₱'.number_format($amountReceived, 2));
    }

    $change = $amountReceived - $totalDue;

    return DB::transaction(function () use ($application, $chargesData, $amountReceived, $totalDue, $change, $userId) {
        $activeStatusId = Status::getIdByDescription(Status::ACTIVE);
        $paidStatusId = Status::getIdByDescription(Status::PAID);

        // ACTION 2.1: Create Payment record
        $payment = Payment::create([
            'receipt_no' => $this->generateReceiptNumber(),  // OR-YYYY-NNNNN
            'payer_id' => $application->customer_id,
            'payment_date' => now()->toDateString(),
            'amount_received' => $amountReceived,
            'user_id' => $userId,
            'stat_id' => $activeStatusId,
        ]);

        $allocations = collect();

        // ACTION 2.2 & 2.3: Create PaymentAllocation + Ledger CREDIT for each charge
        foreach ($chargesData['charges'] as $charge) {
            $remainingOnCharge = $charge->remaining_amount;
            if ($remainingOnCharge <= 0) continue;

            $allocation = PaymentAllocation::create([
                'payment_id' => $payment->payment_id,
                'target_type' => 'CHARGE',
                'target_id' => $charge->charge_id,
                'amount_applied' => $remainingOnCharge,
                'period_id' => null,
                'connection_id' => $charge->connection_id,
            ]);

            $allocations->push($allocation);

            // Create CREDIT entry in ledger
            $this->ledgerService->recordPaymentAllocation(
                $allocation, $payment,
                "Payment for: {$charge->description}",
                $userId
            );
        }

        // ACTION 2.4: Mark charges as PAID
        $this->chargeService->markChargesAsPaid($application->application_id);

        // ACTION 2.5: Update application status to PAID
        $application->update([
            'stat_id' => $paidStatusId,
            'paid_at' => now(),
            'payment_id' => $payment->payment_id,
        ]);

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

---

## Database Tables Written - Stage 2

### Action 2.1: Create Payment Record

**Table:** `Payment`

| Column | Value | Source |
|--------|-------|--------|
| `payment_id` | Auto-increment | System |
| `receipt_no` | Generated | `OR-YYYY-NNNNN` |
| `payer_id` | Customer ID | `$application->customer_id` |
| `payment_date` | Current date | `now()->toDateString()` |
| `amount_received` | Amount paid | Form input |
| `user_id` | Cashier ID | `Auth::id()` |
| `stat_id` | ACTIVE | Status ID |

---

### Action 2.2: Create Payment Allocations

**Table:** `PaymentAllocation` (one per charge)

| Column | Value | Source |
|--------|-------|--------|
| `payment_allocation_id` | Auto-increment | System |
| `payment_id` | Payment ID | `$payment->payment_id` |
| `target_type` | 'CHARGE' | Polymorphic type |
| `target_id` | Charge ID | `$charge->charge_id` |
| `amount_applied` | Charge amount | `$charge->remaining_amount` |
| `period_id` | NULL | Not tied to period |
| `connection_id` | NULL | Not connected yet |

---

### Action 2.3: Record Ledger Entries (CREDIT)

**Table:** `CustomerLedger` (one per allocation)

| Column | Value | Source |
|--------|-------|--------|
| `ledger_entry_id` | Auto-increment | System |
| `customer_id` | Customer ID | `$charge->customer_id` |
| `connection_id` | NULL | Not connected yet |
| `txn_date` | Current date | `now()->toDateString()` |
| `source_type` | 'PAYMENT' | Enum |
| `source_id` | Payment ID | `$payment->payment_id` |
| `description` | Payment note | "Payment for: {charge description}" |
| `debit` | 0.00 | CREDIT entry |
| `credit` | Amount applied | `$allocation->amount_applied` |
| `user_id` | Cashier ID | `Auth::id()` |

---

### Action 2.4: Update Charges to PAID

**Table:** `CustomerCharge` (UPDATE)

| Column | Old Value | New Value |
|--------|-----------|-----------|
| `stat_id` | PENDING | PAID status ID |
| `updated_at` | Previous | Current timestamp |

---

### Action 2.5: Update Application to PAID

**Table:** `ServiceApplication` (UPDATE)

| Column | Old Value | New Value |
|--------|-----------|-----------|
| `stat_id` | VERIFIED | PAID status ID |
| `paid_at` | NULL | `now()` |
| `payment_id` | NULL | `$payment->payment_id` |
| `updated_at` | Previous | Current timestamp |

---

## Error Scenarios - Stage 2

| Error | Condition | Message | HTTP Code |
|-------|-----------|---------|-----------|
| Wrong status | Application not VERIFIED | "Payment can only be processed for VERIFIED applications." | 400 |
| No charges | No charges exist | "No charges found for this application." | 400 |
| Insufficient payment | Amount < total due | "Full payment required. Amount due: ₱X. Received: ₱Y" | 400 |
| Application not found | Invalid ID | "No query results for model [ServiceApplication]" | 404 |

---

# STAGE 3: Schedule Connection (PAID → SCHEDULED)

## UI Flow

| Step | View/Component | URL | User Action |
|------|----------------|-----|-------------|
| 1 | `pages/connection/service-application-detail.blade.php` | `/connection/service-application/{id}` | View paid application |
| 2 | Schedule button (when status=PAID) | Modal opens | Click "Schedule Connection" |
| 3 | Date picker | POST `/connection/service-application/{id}/schedule` | Select date, submit |

---

## Code Snippet: ServiceApplicationService::scheduleConnection()

```php
public function scheduleConnection(int $applicationId, Carbon $scheduledDate, int $scheduledBy): ServiceApplication
{
    $application = ServiceApplication::findOrFail($applicationId);

    // VALIDATION: Application must be PAID
    if ($application->stat_id !== Status::getIdByDescription(Status::PAID)) {
        throw new \Exception('Only PAID applications can be scheduled');
    }

    // ACTION 3.1: Update application with schedule
    $application->update([
        'stat_id' => Status::getIdByDescription(Status::SCHEDULED),
        'scheduled_at' => now(),
        'scheduled_connection_date' => $scheduledDate,
        'scheduled_by' => $scheduledBy,
    ]);

    return $application->fresh();
}
```

---

## Database Tables Written - Stage 3

### Action 3.1: Update Application with Schedule

**Table:** `ServiceApplication` (UPDATE)

| Column | Old Value | New Value |
|--------|-----------|-----------|
| `stat_id` | PAID | SCHEDULED status ID |
| `scheduled_at` | NULL | `now()` |
| `scheduled_connection_date` | NULL | Selected date |
| `scheduled_by` | NULL | `Auth::id()` |
| `updated_at` | Previous | Current timestamp |

---

## Error Scenarios - Stage 3

| Error | Condition | Message | HTTP Code |
|-------|-----------|---------|-----------|
| Wrong status | Application not PAID | "Only PAID applications can be scheduled" | 400 |
| Invalid date | Date in past | Laravel validation: "after_or_equal:today" | 422 |
| Application not found | Invalid ID | "No query results for model [ServiceApplication]" | 404 |

---

# STAGE 4: Complete Connection (SCHEDULED → CONNECTED)

## UI Flow

| Step | View/Component | URL | User Action |
|------|----------------|-----|-------------|
| 1 | `pages/connection/service-connection.blade.php` | `/customer/service-connection` | View scheduled applications banner |
| 2 | Click "Complete Connection" | Opens modal | `complete-connection-modal.blade.php` |
| 3 | Fill form: account type, meter info | POST `/customer/service-connection/complete` | Submit form |
| 4 | Success redirect | `/customer/service-connection/{id}` | View new connection |

## Form Fields Collected

- `application_id`: Hidden, from button data
- `account_type_id`: Dropdown (loaded from API)
- `meter_serial`: Text input (unique)
- `meter_brand`: Text input
- `install_read`: Number input (default 0.000)

---

## Code Snippet: ServiceConnectionService::createFromApplication()

```php
public function createFromApplication(
    ServiceApplication $application,
    int $accountTypeId
): ServiceConnection {
    // VALIDATION: Application must be SCHEDULED
    if ($application->stat_id !== Status::getIdByDescription(Status::SCHEDULED)) {
        throw new \Exception('Application must be in SCHEDULED status to create connection');
    }

    $attempts = 0;
    $lastException = null;

    // Retry loop for race condition handling
    while ($attempts < self::MAX_ACCOUNT_NUMBER_RETRIES) {
        $attempts++;

        try {
            return DB::transaction(function () use ($application, $accountTypeId) {
                $barangayId = $application->address->b_id;

                // ACTION 4.1a: Generate account number with lock
                $accountNumber = $this->generateAccountNumberWithLock($barangayId);

                // ACTION 4.1b: Create service connection
                $connection = ServiceConnection::create([
                    'account_no' => $accountNumber,
                    'customer_id' => $application->customer_id,
                    'address_id' => $application->address_id,
                    'account_type_id' => $accountTypeId,
                    'started_at' => now(),
                    'stat_id' => Status::getIdByDescription(Status::ACTIVE),
                ]);

                // ACTION 4.4: Mark application as connected
                $this->applicationService->markAsConnected(
                    $application->application_id,
                    $connection->connection_id
                );

                return $connection;
            });
        } catch (QueryException $e) {
            // Handle unique constraint violation (race condition)
            if ($this->isUniqueConstraintViolation($e)) {
                usleep(50000 * $attempts);  // Backoff delay
                continue;
            }
            throw $e;
        }
    }

    throw new \Exception("Failed to generate unique account number after {$attempts} attempts.");
}

protected function generateAccountNumberWithLock(int $barangayId): string
{
    $barangay = Barangay::findOrFail($barangayId);
    $year = now()->year;
    $code = $barangay->b_code ?? 'UNKN';

    // Row-level lock to prevent race conditions
    $lastConnection = ServiceConnection::query()
        ->where('account_no', 'like', "{$year}-{$code}-%")
        ->orderByRaw("CAST(SUBSTRING_INDEX(account_no, '-', -1) AS UNSIGNED) DESC")
        ->lockForUpdate()
        ->first();

    if ($lastConnection) {
        $lastSequence = (int) substr($lastConnection->account_no, -5);
        $newSequence = $lastSequence + 1;
    } else {
        $newSequence = 1;
    }

    return sprintf('%d-%s-%05d', $year, $code, $newSequence);
}
```

---

## Code Snippet: MeterAssignmentService::createAndAssignMeter()

```php
public function createAndAssignMeter(
    int $connectionId,
    string $meterSerial,
    string $meterBrand,
    float $installRead,
    Carbon $installedAt
): MeterAssignment {
    $connection = ServiceConnection::findOrFail($connectionId);

    // VALIDATION: Connection must be ACTIVE
    if ($connection->stat_id !== Status::getIdByDescription(Status::ACTIVE)) {
        throw new \Exception('Can only assign meters to ACTIVE connections');
    }

    // VALIDATION: No existing active meter
    $currentAssignment = $this->getCurrentAssignment($connectionId);
    if ($currentAssignment) {
        throw new \Exception('Connection already has an active meter. Remove it first.');
    }

    // VALIDATION: Meter serial must be unique
    $existingMeter = Meter::where('mtr_serial', $meterSerial)->first();
    if ($existingMeter) {
        throw new \Exception('A meter with this serial number already exists');
    }

    return DB::transaction(function () use ($connectionId, $meterSerial, $meterBrand, $installRead, $installedAt) {
        // ACTION 4.2: Create meter record
        $meter = Meter::create([
            'mtr_serial' => $meterSerial,
            'mtr_brand' => $meterBrand,
            'stat_id' => Status::getIdByDescription(Status::INACTIVE),  // In use
        ]);

        // ACTION 4.3: Create meter assignment
        $assignment = MeterAssignment::create([
            'connection_id' => $connectionId,
            'meter_id' => $meter->mtr_id,
            'installed_at' => $installedAt,
            'install_read' => $installRead,
        ]);

        return $assignment;
    });
}
```

---

## Database Tables Written - Stage 4

### Action 4.1: Create Service Connection

**Table:** `ServiceConnection`

| Column | Value | Source |
|--------|-------|--------|
| `connection_id` | Auto-increment | System |
| `account_no` | Generated | `YYYY-BCODE-NNNNN` (e.g., 2025-POBA-00001) |
| `customer_id` | Customer ID | `$application->customer_id` |
| `address_id` | Service address | `$application->address_id` |
| `account_type_id` | Account type | Form: `account_type_id` |
| `started_at` | Current date | `now()->toDateString()` |
| `ended_at` | NULL | Active connection |
| `stat_id` | ACTIVE | Status ID |

**Account Number Format:**
```
YYYY-BCODE-NNNNN
2025-POBA-00001

YYYY = Current year (2025)
BCODE = Barangay code (POBA = Poblacion A)
NNNNN = Sequential number (00001, 00002...)
```

---

### Action 4.2: Create Meter Record

**Table:** `meter`

| Column | Value | Source |
|--------|-------|--------|
| `mtr_id` | Auto-increment | System |
| `mtr_serial` | Serial number | Form: `meter_serial` |
| `mtr_brand` | Brand name | Form: `meter_brand` |
| `stat_id` | INACTIVE | Meter is in use |

---

### Action 4.3: Create Meter Assignment

**Table:** `MeterAssignment`

| Column | Value | Source |
|--------|-------|--------|
| `assignment_id` | Auto-increment | System |
| `connection_id` | Connection ID | `$connection->connection_id` |
| `meter_id` | Meter ID | `$meter->mtr_id` |
| `installed_at` | Current date | `now()->toDateString()` |
| `removed_at` | NULL | Currently installed |
| `install_read` | Initial reading | Form: `install_read` (default 0.000) |
| `removal_read` | NULL | Not removed |

---

### Action 4.4: Update Application to CONNECTED

**Table:** `ServiceApplication` (UPDATE)

| Column | Old Value | New Value |
|--------|-----------|-----------|
| `stat_id` | SCHEDULED | CONNECTED status ID |
| `connected_at` | NULL | `now()` |
| `connection_id` | NULL | `$connection->connection_id` |
| `updated_at` | Previous | Current timestamp |

---

## Error Scenarios - Stage 4

| Error | Condition | Message | HTTP Code |
|-------|-----------|---------|-----------|
| Wrong status | Application not SCHEDULED | "Application must be in SCHEDULED status to create connection" | 400 |
| Duplicate meter serial | Serial exists in meter table | Laravel validation: "unique:meter,mtr_serial" | 422 |
| Race condition | Account number conflict | "Failed to generate unique account number after 5 attempts" | 400 |
| Connection not ACTIVE | Status check fails | "Can only assign meters to ACTIVE connections" | 400 |
| Meter already assigned | Active assignment exists | "Connection already has an active meter. Remove it first." | 400 |
| Invalid account type | ID not in account_type table | Laravel validation: "exists:account_type,at_id" | 422 |

---

# Complete Flow Summary

## Total Database Activity

| Stage | Tables Affected | Inserts | Updates | Total Records |
|-------|-----------------|---------|---------|---------------|
| 1. Application | 5 tables | 8-14 | 0 | 8-14 |
| 2. Payment | 5 tables | 7-11 | 4-6 | 11-17 |
| 3. Schedule | 1 table | 0 | 1 | 1 |
| 4. Connection | 4 tables | 3 | 1 | 4 |
| **TOTAL** | **10 tables** | **18-28** | **6-8** | **24-36** |

---

## All Tables Involved (10 tables)

| Table | Stage(s) | Purpose |
|-------|----------|---------|
| `consumer_address` | 1 | Service location storage |
| `customer` | 1 | Customer master (if new) |
| `ServiceApplication` | 1, 2, 3, 4 | Application workflow tracking |
| `CustomerCharge` | 1, 2 | Application fees |
| `CustomerLedger` | 1, 2 | Financial transaction log |
| `Payment` | 2 | Payment receipt |
| `PaymentAllocation` | 2 | Payment distribution |
| `ServiceConnection` | 4 | Active service account |
| `meter` | 4 | Physical meter record |
| `MeterAssignment` | 4 | Meter-to-connection link |

---

## Status Transitions Summary

| Stage | From Status | To Status | Trigger |
|-------|-------------|-----------|---------|
| 1 | (none) | VERIFIED | Application submission (auto-verified) |
| 2 | VERIFIED | PAID | Full payment received |
| 3 | PAID | SCHEDULED | Admin sets connection date |
| 4 | SCHEDULED | CONNECTED | Connection completed |

---

## Key Routes

| Stage | HTTP Method | Route | Controller Method |
|-------|-------------|-------|-------------------|
| 1 | POST | `/connection/service-application` | `ServiceApplicationController@store` |
| 2 | POST | `/connection/service-application/{id}/process-payment` | `ServiceApplicationController@processPayment` |
| 3 | POST | `/connection/service-application/{id}/schedule` | `ServiceApplicationController@schedule` |
| 4 | POST | `/customer/service-connection/complete` | `ServiceConnectionController@completeConnection` |

---

## Key File Paths

| Component | Path |
|-----------|------|
| **Models** | |
| ServiceApplication | `app/Models/ServiceApplication.php` |
| ServiceConnection | `app/Models/ServiceConnection.php` |
| CustomerCharge | `app/Models/CustomerCharge.php` |
| CustomerLedger | `app/Models/CustomerLedger.php` |
| Payment | `app/Models/Payment.php` |
| Meter | `app/Models/Meter.php` |
| MeterAssignment | `app/Models/MeterAssignment.php` |
| **Services** | |
| ServiceApplicationService | `app/Services/ServiceApplication/ServiceApplicationService.php` |
| ServiceConnectionService | `app/Services/ServiceConnection/ServiceConnectionService.php` |
| PaymentService | `app/Services/Payment/PaymentService.php` |
| MeterAssignmentService | `app/Services/Meter/MeterAssignmentService.php` |
| ApplicationChargeService | `app/Services/Charge/ApplicationChargeService.php` |
| LedgerService | `app/Services/Ledger/LedgerService.php` |
| **Controllers** | |
| ServiceApplicationController | `app/Http/Controllers/ServiceApplication/ServiceApplicationController.php` |
| ServiceConnectionController | `app/Http/Controllers/ServiceConnection/ServiceConnectionController.php` |
| **Views** | |
| Application Form | `resources/views/pages/application/service-application.blade.php` |
| Application Detail | `resources/views/pages/connection/service-application-detail.blade.php` |
| Connection List | `resources/views/pages/connection/service-connection.blade.php` |
| Connection Detail | `resources/views/pages/connection/service-connection-detail.blade.php` |
| Complete Connection Modal | `resources/views/components/ui/connection/complete-connection-modal.blade.php` |

---

## Validation Rules Summary

### Stage 1 → 2 (Before Payment)
```php
if ($application->stat_id !== Status::getIdByDescription(Status::VERIFIED)) {
    throw new \Exception('Payment can only be processed for VERIFIED applications.');
}
if ($amountReceived < $totalDue) {
    throw new \Exception('Full payment required.');
}
```

### Stage 2 → 3 (Before Schedule)
```php
if ($application->stat_id !== Status::getIdByDescription(Status::PAID)) {
    throw new \Exception('Only PAID applications can be scheduled');
}
```

### Stage 3 → 4 (Before Connection)
```php
if ($application->stat_id !== Status::getIdByDescription(Status::SCHEDULED)) {
    throw new \Exception('Application must be in SCHEDULED status to create connection');
}
```

### Meter Assignment Validation
```php
if ($connection->stat_id !== Status::getIdByDescription(Status::ACTIVE)) {
    throw new \Exception('Can only assign meters to ACTIVE connections');
}
if ($currentAssignment) {
    throw new \Exception('Connection already has an active meter.');
}
if (Meter::where('mtr_serial', $meterSerial)->exists()) {
    throw new \Exception('A meter with this serial number already exists');
}
```

---

This document provides a complete reference for understanding exactly what happens in the database, code, and UI at each step of the service application to connection workflow.

---

*Last updated: 2026-01-15*
*Stack: Laravel 12, MySQL, Alpine.js*
