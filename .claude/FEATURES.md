# Features & Architecture - Initao Water Billing System

Complete guide to the water billing system architecture, features, models, and business rules.

---

## Table of Contents

1. [Architecture Overview](#architecture-overview)
   - [Critical: Dual Billing System](#critical-dual-billing-system)
2. [Feature-Based Organization](#feature-based-organization)
3. [Key Domain Concepts](#key-domain-concepts)
   - [Customer vs ServiceConnection](#customer-vs-serviceconnection)
   - [Address Hierarchy](#address-hierarchy)
   - [Service Connection Lifecycle](#service-connection-lifecycle)
4. [Core Models & Relationships](#core-models--relationships)
   - [Customer Management](#customer-management)
   - [Billing & Metering](#billing--metering-modern)
   - [Payment System](#payment-system)
   - [Ledger System](#ledger-system)
   - [Area Management](#area-management)
   - [Period Management](#period-management)
   - [Charge System](#charge-system)
5. [Architectural Patterns](#architectural-patterns)
   - [Controllers](#controllers)
   - [Services](#services)
   - [Models](#models)
   - [Events & Listeners](#events--listeners)
6. [Business Rules](#business-rules)
7. [Database & Migrations](#database--migrations)
   - [Migration Sequencing](#migration-sequencing)
   - [Database Naming Conventions](#database-naming-conventions)
8. [Code Standards](#code-standards)
   - [Naming Conventions](#naming-conventions)
   - [Commit Message Format](#commit-message-format)
9. [Security & Performance](#security--performance)
   - [Security Rules](#security-rules)
   - [Performance Rules](#performance-rules)
10. [Helper Functions](#helper-functions)
11. [Development Principles](#development-principles)
12. [Critical Development Notes](#critical-development-notes)

---

## Architecture Overview

### Critical: Dual Billing System

This codebase contains **TWO PARALLEL BILLING SYSTEMS** that coexist in production:

#### Modern System (ServiceConnection-based) - **Recommended for new features**
- **Core Entity:** `ServiceConnection` (water service connection for a customer)
- **Billing Flow:** `ServiceConnection` → `MeterAssignment` → `MeterReading` → `water_bill_history`
- **Ledger:** `CustomerLedger` (polymorphic source tracking: BILL/CHARGE/PAYMENT)
- **Payment:** `Payment` → `PaymentAllocation` (polymorphic distribution)
- **Tables use:** PascalCase (`Payment`, `PaymentAllocation`, `MeterReading`, `MeterAssignment`, `BillAdjustment`, `CustomerCharge`, `ServiceConnection`)

#### Legacy System (Consumer-based) - **Maintenance only**
- **Core Entity:** `Consumer` (links customer + meter + area)
- **Billing Flow:** `Customer` → `Consumer` → `consumer_meters` → `consumer_ledger` → `water_bill`
- **Ledger:** `consumer_ledger` (simple debit/credit)
- **Tables use:** snake_case (`consumer_meters`, `meter_readers`, `payment_transactions`, `water_bill`)

---

## Feature-Based Organization

Code is organized by feature/domain:

```
app/
  Http/
    Controllers/
      Auth/              # Authentication controllers
      Admin/             # Admin features (CustomerController)
      Billing/           # Billing management
      Payments/          # Payment processing
      Consumers/         # Consumer management
  Services/
      Auth/              # Authentication business logic
      Billing/           # Billing calculations and generation
      Payments/          # Payment allocation logic
      Consumers/         # Consumer management logic
  Models/                # Domain entities (User, Consumer, WaterBill, etc.)
  Rules/                 # Custom validation rules
  Traits/                # Reusable model/service traits
  Events/                # Domain events
  Listeners/             # Event handlers
  Helpers/               # Helper utilities (CustomerHelper)
```

---

## Key Domain Concepts

### Customer vs ServiceConnection

- `Customer` = Person/organization (account holder with contact info)
- `ServiceConnection` = Active water service connection (customer can have multiple connections)

### Address Hierarchy

```
Province → Town → Barangay (village) → Purok (sub-village/zone)
```

Stored in `ConsumerAddress` with all levels linked.

### Service Connection Lifecycle

```
ServiceApplication (customer applies)
    ↓ (approved)
ServiceConnection (active service)
    ↓
MeterAssignment (meter installed)
    ↓
MeterReading (periodic readings by MeterReader)
    ↓
WaterBillHistory (bill generated: consumption × rate + adjustments)
    ↓
Payment → PaymentAllocation (payment distributed across bills/charges)
```

---

## Core Models & Relationships

### Customer Management

- `Customer` - Person/organization with hierarchical address
- `ConsumerAddress` - Links Province → Town → Barangay → Purok
- `ServiceApplication` - New service requests
- `ServiceConnection` - Active water services (multiple per customer)
- Resolution numbers: Auto-generated `INITAO-{initials}-{timestamp}`

### Billing & Metering (Modern)

- `Meter` - Physical meter device (can be reassigned)
- `MeterAssignment` - Links meter to ServiceConnection with install/removal dates
- `MeterReading` - Periodic readings by MeterReaders for specific Period
- `WaterBillHistory` - Generated bills (consumption × rate + adjustments)
- `BillAdjustment` - Credits/penalties/waivers linked to bills

### Payment System

- `Payment` - Receipt with total amount paid by Customer
- `PaymentAllocation` - Polymorphic distribution across bills/charges
  - `target_type`: 'BILL' or 'CHARGE'
  - `target_id`: WaterBillHistory or CustomerCharge

### Ledger System

- `CustomerLedger` - Double-entry accounting with polymorphic sources
  - `source_type`: 'BILL', 'CHARGE', or 'PAYMENT'
  - Tracks per ServiceConnection and Period
  - Includes user_id for audit trail

### Area Management

- `Area` - Geographic service zones
- `AreaAssignment` - Assigns MeterReaders to areas with effective dates
- `ReadingSchedule` - Schedules meter readings per period

### Period Management

- `Period` - Monthly billing cycles (auto-generated for 12 months)
- Fields: `per_code` (202501), `per_name` (January 2025)
- `is_closed` flag for finalized periods
- Migration auto-creates periods and closes past ones

### Charge System

- `ChargeItem` - Template/catalog (Connection Fee, Reconnection Fee, etc.)
- `CustomerCharge` - Actual charge instances linked to customers/applications/connections

---

## Architectural Patterns

### Controllers

**Purpose:** Orchestrate flow between request → service → response

**Rules:**
- **NO business logic** inside controllers
- Use `FormRequest` classes for validation
- Return JSON for APIs or Blade views for admin pages

```php
// ✅ Good Controller
public function generate(BillingRequest $request, BillingService $billing)
{
    $count = $billing->generateForPeriod($request->period_id);

    return response()->json([
        'message' => "Billing generated for {$count} consumers."
    ]);
}
```

### Services

**Purpose:** Handle all business logic for a specific feature

**Rules:**
- Services should be **stateless** and **self-contained**
- Services may directly use **Eloquent models** (no repositories)
- If a process spans multiple models, orchestrate it in the service

```php
// ✅ Good Service
class BillingService
{
    public function generateForPeriod(int $periodId): int
    {
        $connections = ServiceConnection::with('latestMeterAssignment.latestReading')->get();
        $count = 0;

        foreach ($connections as $connection) {
            $usage = $this->calculateUsage($connection);
            $amount = $this->calculateAmount($connection, $usage);

            WaterBillHistory::create([
                'connection_id' => $connection->id,
                'period_id' => $periodId,
                'consumption' => $usage,
                'water_amount' => $amount,
            ]);

            $count++;
        }

        return $count;
    }
}
```

### Models

**Purpose:** Represent business entities and handle persistence

**Rules:**
- Define **relationships**, **casts**, and **accessors** clearly
- Use **scopes** for common filters (`active()`, `forPeriod()`)
- Do **NOT** include business logic (keep it in services)

```php
// ✅ Good Model with scopes and relationships
class Consumer extends Model
{
    public function latestReading()
    {
        return $this->hasOne(MeterReading::class)->latestOfMany();
    }

    public function scopeActive($query)
    {
        return $query->where('status_id', Status::getIdByDescription(Status::ACTIVE));
    }
}
```

### Events & Listeners

**Purpose:** Handle side effects asynchronously or independently

**Rules:**
- Use when side effects must be handled asynchronously or independently
- Example: After `WaterBill` generated → create `LedgerEntry`, or log audit
- Keep listeners short and focused

```php
// ✅ Good Event Usage
event(new BillGenerated($connection, $periodId));
```

---

## Business Rules

1. **Polymorphic Relationships:** Always check `source_type`/`target_type` before accessing polymorphic relations (CustomerLedger, PaymentAllocation)
2. **Status Tracking:** Most entities use `status_id` (not soft deletes)
   - Use `Status::getIdByDescription(Status::ACTIVE)` to get IDs
   - Constants: `Status::PENDING`, `Status::ACTIVE`, `Status::INACTIVE`
3. **Period-based Operations:** Most billing tied to specific billing periods
4. **Audit Trails:** Critical operations track `user_id` + timestamps
5. **No Timestamps:** Many models have `public $timestamps = false`
6. **Uppercase Validation:** Customer names use `Uppercase` rule
7. **Resolution Numbers:** Use `CustomerHelper::generateCustomerResolutionNumber()` for unique IDs (format: INITAO-{initials}-{timestamp})
8. **Period Closure:** Respect `is_closed` flag - no modifications to closed periods

---

## Database & Migrations

### Migration Sequencing

Migrations numbered 0001-0043 with critical dependencies:

1. **0001_statuses_table** - **MUST run first** (required by all models)
2. 0002_user_types_table
3. 0003-0006 - RBAC (Roles, Permissions)
4. 0018_customers_table
5. 0019_service_connections_table
6. 0020-0021 - Meters and MeterAssignments
7. 0022-0025 - Billing and Payment tables
8. 0026-0030 - Areas, Readings, Periods

**Note:** Some numbers duplicate (0015, 0019, 0021, 0037) - parallel features developed simultaneously.

### Database Naming Conventions

The database uses **TWO naming conventions** based on the billing system:

#### 1. Modern System Tables (PascalCase):
- `Payment`, `PaymentAllocation`, `MeterReading`, `MeterAssignment`
- `BillAdjustment`, `CustomerCharge`, `ServiceConnection`, `CustomerLedger`
- `ServiceApplication`, `AreaAssignment`, `BillAdjustmentType`, `ChargeItem`

#### 2. Legacy System Tables (snake_case):
- `consumer_meters`, `meter_readers`, `payment_transactions`
- `consumer_ledger`, `water_bill`, `misc_bill`, `Consumer`

#### 3. Shared/Common Tables (snake_case):
- `customer`, `consumer_address`, `area`, `barangay`, `town`, `province`, `purok`
- `meter`, `period`, `statuses`, `users`, `roles`, etc.

#### For New Code:
- Modern system features: Use PascalCase (e.g., new `BillingFeature` table)
- Legacy system maintenance: Use snake_case (e.g., `old_table_name`)
- **Important:** Models MUST explicitly set `protected $table` property to match!

---

## Code Standards

### Naming Conventions

| Item               | Convention          | Example                                  |
| ------------------ | ------------------- | ---------------------------------------- |
| **Controller**     | `FeatureController` | `BillingController`, `PaymentController` |
| **Service**        | `FeatureService`    | `BillingService`, `AuthService`          |
| **Request**        | `FeatureRequest`    | `BillingRequest`                         |
| **Event**          | `ActionOccurred`    | `BillGenerated`, `PaymentReceived`       |
| **Model**          | Singular            | `WaterBill`, `Consumer`                  |
| **Database table** | snake_case plural   | `water_bills`, `consumers`               |

Follow **PSR-12** + Laravel naming + trailing commas + strict typing when possible.

### Commit Message Format

Prefix commits by type:
- `feat(billing): add billing generation logic`
- `fix(payments): correct rounding issue`
- `chore: update composer dependencies`
- `refactor(consumers): extract service logic from controller`

**Rules:**
- Each commit = one logical change
- Run `php artisan test` before pushing
- Never commit `.env` or credentials

---

## Security & Performance

### Security Rules

- Always use **prepared queries** (Eloquent or Query Builder)
- **Never trust user input**; sanitize all external data
- Protect sensitive routes with **policies** or **gates**
- **Rate-limit** authentication and payment endpoints
- Use `bcrypt` or `argon2` for passwords (Laravel default)
- Ensure audit logging for billing, payments, and adjustments

### Performance Rules

- Always **paginate** data on listing endpoints
- Use **eager loading** to avoid N+1 queries
- Add DB **indexes** for frequently queried columns:
  - `consumer_id`, `connection_id`
  - `reading_date`, `period_id`
  - `status_id`, `bill_status`
- Cache heavy reports or analytics queries with Redis
- Enable **OPcache** in production

---

## Helper Functions

### CustomerHelper

**Location:** `app/Http/Helpers/CustomerHelper.php`

```php
// Generate unique resolution number
CustomerHelper::generateCustomerResolutionNumber($firstName, $middleName, $lastName)
// Returns: INITAO-ABC-1234567890

// Create customer with address
CustomerHelper::createCustomer($data)
CustomerHelper::createConsumerAddress($data)
```

### Status Constants

```php
Status::PENDING    // "PENDING"
Status::ACTIVE     // "ACTIVE"
Status::INACTIVE   // "INACTIVE"

// Usage:
$statusId = Status::getIdByDescription(Status::ACTIVE);
```

---

## Development Principles

| Principle         | Description                                    |
| ----------------- | ---------------------------------------------- |
| **KISS**          | Keep It Simple, Scalable                       |
| **DRY**           | Reuse services and traits                      |
| **SRP**           | One class = one responsibility                 |
| **Separation**    | Controllers handle flow, Services handle logic |
| **Fast Delivery** | Prioritize completion, not abstraction         |
| **Readability**   | Code should explain itself                     |

---

## Critical Development Notes

1. **⚠️ DUAL BILLING SYSTEM** - Two parallel systems exist (Modern ServiceConnection-based vs Legacy Consumer-based). Always confirm which system you're working with before coding.

2. **Table naming convention** - Modern system uses PascalCase tables, Legacy uses snake_case. Models must specify correct $table property.

3. **Status table dependency** - Ensure Status model exists and is seeded before creating records

4. **No business logic in controllers** - Always use Services for business logic

5. **No repository pattern** - Use Eloquent models directly in services

6. **Polymorphic queries** - Always check `source_type`/`target_type` fields before accessing polymorphic relations (CustomerLedger, PaymentAllocation - Modern system only)

7. **Meter handling** - Modern: Use MeterAssignment table. Legacy: Use consumer_meters table

8. **Payment handling** - Modern: Use PaymentAllocation (polymorphic). Legacy: Use payment_transactions

9. **Resolution number uniqueness** - Always use `CustomerHelper::generateCustomerResolutionNumber()`, never generate manually

10. **Period closure** - Respect `is_closed` flag on Period model - no modifications to closed periods

11. **Feature-based organization** - Place new code in appropriate feature folder (Auth, Billing, Payments, Consumers)

12. **Service layer pattern** - All business logic goes in Services, controllers only orchestrate

---

**Last Updated:** 2025-11-05
