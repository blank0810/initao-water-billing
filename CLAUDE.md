# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Initao Water Billing System** - A Laravel 12 water utility billing application for managing water service connections, meter readings, billing, and payments in Initao, Philippines.

**Development Philosophy:**
- Build fast, maintain clean
- Organize by **feature** (Billing, Auth, Payments, Consumers)
- Keep logic out of controllers — **Services handle business rules**
- Use **Eloquent models** directly for data access (no repository abstraction)
- Use **Events** or **Observers** only when logic must be decoupled

## Common Commands

### Development

```bash
# Full setup (installs dependencies, generates key, migrates, builds assets)
composer setup

# Start development server (concurrent: Laravel server, queue worker, logs, Vite)
composer dev

# Alternative: Start individual services
php artisan serve               # Start Laravel server (port 8000)
php artisan queue:listen        # Start queue worker
php artisan pail                # View logs
npm run dev                     # Start Vite dev server
```

### Testing

```bash
# Run all tests (uses Pest)
composer test
# or
php artisan test

# Run tests in parallel
php artisan test --parallel

# Run specific test file
php artisan test --filter=TestClassName

# Run specific test method
php artisan test --filter=test_method_name
```

### Database

```bash
# Run migrations
php artisan migrate

# Run migrations with seeding
php artisan migrate --seed

# Rollback last migration batch
php artisan migrate:rollback

# Fresh migrate (drop all tables and re-migrate)
php artisan migrate:fresh
```

### Code Quality

```bash
# Run Laravel Pint (code formatter)
./vendor/bin/pint

# Fix all files
./vendor/bin/pint

# Check without fixing
./vendor/bin/pint --test
```

### Docker Environment

```bash
# Start all services (nginx:9000, mysql:3307, phpmyadmin:8080, mailpit:8025)
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs -f

# Execute commands in app container
docker-compose exec water_billing_app php artisan migrate
docker-compose exec water_billing_app composer install
```

**Service URLs:**
- App: http://localhost:9000
- PhpMyAdmin: http://localhost:8080
- Mailpit UI: http://localhost:8025

## Architecture Overview

### Critical Architectural Pattern: Dual Billing System

This codebase contains **TWO PARALLEL BILLING SYSTEMS** - a legacy Consumer-based system and a modern ServiceConnection-based system. Understanding this duality is essential.

#### Legacy System (Consumer-based)
- **Core Entity:** `Consumer` (represents customer + meter + area relationship)
- **Billing Flow:** `ConsumerLedger` → `WaterBill` → `MiscBill`
- **Meter:** `ConsumerMeter`
- **Ledger:** `ConsumerLedger` (simple debit/credit tracking)

#### Modern System (ServiceConnection-based)
- **Core Entity:** `ServiceConnection` (replaces Consumer concept)
- **Billing Flow:** `ServiceConnection` → `MeterAssignment` → `MeterReading` → `WaterBillHistory`
- **Meter:** `Meter` → `MeterAssignment` (supports meter swaps)
- **Ledger:** `CustomerLedger` (polymorphic source tracking)
- **Payment:** `Payment` → `PaymentAllocation` (polymorphic distribution)

### Feature-Based Folder Structure

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

### Key Domain Concepts

**Customer vs Consumer:**
- `Customer` = Person/organization (account holder with contact info)
- `Consumer` (legacy) = Relationship linking customer + meter + area
- `ServiceConnection` (modern) = Replaces Consumer with cleaner service model

**Address Hierarchy:**
```
Province → Town → Barangay (village) → Purok (sub-village/zone)
```
Stored in `ConsumerAddress` with all levels linked.

**Service Connection Lifecycle:**
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

### Core Models & Relationships

**Customer Management:**
- `Customer` - Person/organization with hierarchical address
- `ConsumerAddress` - Links Province → Town → Barangay → Purok
- `ServiceApplication` - New service requests
- `ServiceConnection` - Active water services (multiple per customer)
- Resolution numbers: Auto-generated `INITAO-{initials}-{timestamp}`

**Billing & Metering (Modern):**
- `Meter` - Physical meter device (can be reassigned)
- `MeterAssignment` - Links meter to ServiceConnection with install/removal dates
- `MeterReading` - Periodic readings by MeterReaders for specific Period
- `WaterBillHistory` - Generated bills (consumption × rate + adjustments)
- `BillAdjustment` - Credits/penalties/waivers linked to bills

**Payment System:**
- `Payment` - Receipt with total amount paid by Customer
- `PaymentAllocation` - Polymorphic distribution across bills/charges
  - `target_type`: 'BILL' or 'CHARGE'
  - `target_id`: WaterBillHistory or CustomerCharge

**Ledger System:**
- `CustomerLedger` - Double-entry accounting with polymorphic sources
  - `source_type`: 'BILL', 'CHARGE', or 'PAYMENT'
  - Tracks per ServiceConnection and Period
  - Includes user_id for audit trail

**Area Management:**
- `Area` - Geographic service zones
- `AreaAssignment` - Assigns MeterReaders to areas with effective dates
- `ReadingSchedule` - Schedules meter readings per period

**Period Management:**
- `Period` - Monthly billing cycles (auto-generated for 12 months)
- Fields: `per_code` (202501), `per_name` (January 2025)
- `is_closed` flag for finalized periods
- Migration auto-creates periods and closes past ones

**Charge System:**
- `ChargeItem` - Template/catalog (Connection Fee, Reconnection Fee, etc.)
- `CustomerCharge` - Actual charge instances linked to customers/applications/connections

### Architectural Rules

**Controllers:**
- Purpose: Orchestrate flow between request → service → response
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

**Services:**
- Purpose: Handle all business logic for a specific feature
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

**Models:**
- Purpose: Represent business entities and handle persistence
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

**Events & Listeners:**
- Use when side effects must be handled asynchronously or independently
- Example: After `WaterBill` generated → create `LedgerEntry`, or log audit
- Keep listeners short and focused

```php
// ✅ Good Event Usage
event(new BillGenerated($connection, $periodId));
```

### Important Business Rules

1. **Polymorphic Relationships:** Always check `source_type`/`target_type` before accessing polymorphic relations
2. **Status Tracking:** Most entities use `status_id` (not soft deletes)
   - Use `Status::getIdByDescription(Status::ACTIVE)` to get IDs
   - Constants: `Status::PENDING`, `Status::ACTIVE`, `Status::INACTIVE`
3. **Period-based Operations:** Most billing tied to specific periods
4. **Audit Trails:** Critical operations track `user_id` + timestamps
5. **No Timestamps:** Many models have `public $timestamps = false`
6. **Uppercase Validation:** Customer names use `Uppercase` rule
7. **Resolution Numbers:** Use `CustomerHelper::generateCustomerResolutionNumber()` for unique IDs
8. **Period Closure:** Respect `is_closed` flag - no modifications to closed periods

### Migration Sequencing

Migrations numbered 0001-0043 with critical dependencies:

1. **0001_statuses_table** - **MUST run first** (required by all models)
2. 0002_user_types_table
3. 0003-0006 - RBAC (Roles, Permissions)
4. 0012_consumers_table (modern with name fields)
5. 0018_customers_table
6. 0019_service_connections_table
7. 0020-0021 - Meters and MeterAssignments
8. 0026_consumers_table (legacy: customer+meter+area relation)

**Note:** Some numbers duplicate (0015, 0019, 0021, 0037) - parallel features.

### Helper Functions

**CustomerHelper** (`app/Http/Helpers/CustomerHelper.php`):
```php
// Generate unique resolution number
CustomerHelper::generateCustomerResolutionNumber($firstName, $middleName, $lastName)
// Returns: INITAO-ABC-1234567890

// Create customer with address
CustomerHelper::createCustomer($data)
CustomerHelper::createConsumerAddress($data)
```

**Status Constants:**
```php
Status::PENDING    // "PENDING"
Status::ACTIVE     // "ACTIVE"
Status::INACTIVE   // "INACTIVE"

// Usage:
$statusId = Status::getIdByDescription(Status::ACTIVE);
```

## Coding Conventions & Standards

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

**Database Naming Note:** Codebase has inconsistent naming (legacy vs modern):
- Legacy: PascalCase tables (`Consumer`, `MeterReading`)
- Modern: snake_case tables (`service_connection`, `customer_ledger`)
- **For new code:** Prefer snake_case to align with modern Laravel conventions

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

## Testing

Uses **Pest PHP** with Laravel plugin:
- **Feature tests** for API endpoints and full flows
- **Unit tests** for core service logic (billing calculations)
- Test database: SQLite in-memory (`:memory:`)
- Test environment variables in `phpunit.xml`

```bash
# Run all tests
composer test

# Run in parallel
php artisan test --parallel

# Run specific feature
php artisan test --filter=BillingTest
```

## Docker Configuration

Services (defined in `docker-compose.yml`):
- `water_billing_app` - PHP-FPM (Laravel)
- `water_billing_nginx` - Nginx web server (port 9000)
- `water_billing_db` - MySQL (port 3307 external, 3306 internal)
- `water_billing_phpmyadmin` - Database admin (port 8080)
- `mailpit` - Email testing (UI: 8025, SMTP: 1025)

Container names and ports configured via `.env` variables.

## Environment Configuration

Key `.env` settings:
- `APP_URL=http://localhost:9000` (Docker nginx port)
- `DB_HOST=water_billing_db` (container name)
- `DB_PORT=3306` (internal container port)
- `MAIL_HOST=mailpit` (for local email testing)
- `VITE_DEV_SERVER_URL=http://localhost:5173`

From host machine:
- Database: `127.0.0.1:3307` (external port)
- PhpMyAdmin: `http://localhost:8080`
- Mailpit UI: `http://localhost:8025`

## Development Principles

| Principle         | Description                                    |
| ----------------- | ---------------------------------------------- |
| **KISS**          | Keep It Simple, Scalable                       |
| **DRY**           | Reuse services and traits                      |
| **SRP**           | One class = one responsibility                 |
| **Separation**    | Controllers handle flow, Services handle logic |
| **Fast Delivery** | Prioritize completion, not abstraction         |
| **Readability**   | Code should explain itself                     |

## Critical Notes for Development

1. **Check which billing system** you're working with (legacy Consumer vs modern ServiceConnection)
2. **Customer ≠ Consumer** - Customer is person, Consumer is legacy relationship entity
3. **Status table dependency** - Ensure Status model exists and is seeded before creating records
4. **No business logic in controllers** - Always use Services
5. **No repository pattern** - Use Eloquent models directly in services
6. **Polymorphic queries** - Always check type fields before accessing polymorphic relations
7. **Meter reassignment** - Use MeterAssignment table, not direct consumer-meter links
8. **Payment allocation** - Use PaymentAllocation for distributing payments, not direct updates
9. **Resolution number uniqueness** - Always use helper function, never generate manually
10. **Feature-based organization** - Place new code in appropriate feature folder (Auth, Billing, Payments, Consumers)
