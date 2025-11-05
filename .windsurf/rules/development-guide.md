---
trigger: always_on
---

# Laravel Water Billing System — Unified Guide

> Single-repo, pure-Laravel implementation of the Water Billing System. This document is a developer-oriented system + developer guide combining domain notes and backend best practices into a single reference for building, testing, deploying, and maintaining the application.

---

## Table of contents

1. Purpose & Scope
2. High-level Architecture
3. Roles & Permissions
4. Domain Modules & Workflows
5. Data Model (summary)
6. Project Structure (Laravel layout)
7. Coding Standards & Patterns
8. API Design & Contracts
9. Validation & Rules
10. Authentication & Authorization
11. Audit, Logging & Events
12. Testing Strategy
13. Dev / Local Environment (Docker)
14. CI/CD and Deployments
15. Security Considerations
16. Performance & Optimization
17. Monitoring & Observability
18. Documentation & API Specs
19. Onboarding Checklist & Next Steps

---

## 1. Purpose & Scope

The Water Billing System is a centralized application that manages consumer records, meter readings, bill generation, payments, adjustments, and reporting for a municipal water utility. This repository intentionally contains **only a Laravel application** (no separate Next.js/React frontend). The app exposes a RESTful JSON API and includes blade views for small admin pages if needed.

Primary goals:

* Correctness: accurate billing calculations and ledger consistency
* Auditability: full trace of operations (who, when, what changed)
* Maintainability: clear domain separation, tests, and patterns
* Extensibility: easy to add features (AI hooks, analytics)

## 2. High-level Architecture

* **Monolithic Laravel application** (recommended Laravel 10+, PHP 8.1+)
* Layers:

  * HTTP Controllers → Form Requests → Services → Repositories → Models
  * Domain Events & Listeners for side-effects (ledger entries, notifications)
  * Jobs & Queues for heavy tasks (billing generation, report exports)
  * API Resources for consistent JSON responses
* **Storage**: MySQL/Postgres (primary), Redis (cache/queues/session)
* **Auth**: Laravel Sanctum for token-based API authentication
* **Queues**: Redis + Laravel Horizon
* **Monitoring**: Sentry/Telescope (dev only), Prometheus/grafana for infra

## 3. Roles & Permissions

Suggested roles (RBAC):

* `super_admin` — full control
* `admin` — manage system settings, approve budgets, reports
* `billing_officer` — run billing, view/adjust bills
* `collector` — process payments, view consumer ledger
* `meter_reader` — record meter readings
* `employee` — generic operational tasks

Authorization: implement Policies for models and Gates for high-level permissions.

## 4. Domain Modules & Workflows

### 4.1 Consumer Management

* CRUD consumer and application records
* Application approval creates `consumer_profile` and `consumer_meter`

### 4.2 Metering & Readings

* `consumer_meter` holds meter meta
* `meter_readings` stores `current_reading`, `reading_date` and status
* Validation: monotonic increase (or documented exceptions)

### 4.3 Billing

* Billing job runs per billing cycle/period
* Billing calculation: `amount = (current - previous) * water_rate + penalties + misc_bill`
* Generate `water_bill` and corresponding `consumer_ledger` debit
* Support `bill_adjustment` flows (credit/debit) with ledger linkage

### 4.4 Payments

* `payment_transaction` creation inserts credit ledger entry
* Cancellation marks txns and related ledger entries as cancelled
* Integrate third-party payment providers via adapters

### 4.5 Meter Management

* Replacing a meter closes previous reading and inserts new meter
* Preserve historical readings and ledger links

### 4.6 Reporting & KPIs

* Daily/Monthly reports: total billed, collected, outstanding, arrears
* KPIs: collection rate, average consumption, anomaly detection hooks

## 5. Data Model (summary)

Key tables (short list):

* `users`, `roles`, `permissions`
* `consumers`, `consumer_profiles`, `customer_addresses`
* `consumer_meters`, `meter_readings`
* `water_rates`, `penalties`, `misc_bills`
* `water_bills`, `water_bill_adjustments`
* `consumer_ledgers` (debits/credits)
* `payment_transactions`
* `periods`, `classifications`, `tariffs`

Model relationships should be explicit using Eloquent relations. Use `HasMany`, `BelongsTo`, `MorphMany` where appropriate (e.g., ledger as polymorphic entries if you plan to link to different sources).

## 6. Project Structure (Laravel layout)

Follow PSR-4 and Laravel conventions. Example expanded structure:

```
app/
  Console/
  Exceptions/
  Http/
    Controllers/
    Requests/
    Resources/
    Middleware/
  Jobs/
  Listeners/
  Events/
  Models/
  Policies/
  Repositories/
  Services/
  Rules/
  Traits/
bootstrap/
config/
database/
  migrations/
  seeders/
  factories/
resources/
  views/   # optional small admin blades
routes/
  api.php
  web.php
tests/
  Feature/
  Unit/
```

Notes:

* Place business logic in `Services` and `Repositories`. Controllers should orchestrate only.
* Keep `Requests` with validation rules; keep complex validation as custom `Rules`.

## 7. Coding Standards & Patterns

* **Language**: PHP 8.1+, strict_types
* **Style**: PSR-12
* **Patterns**:

  * Repository + Service layer
  * Domain Events for side effects
  * API Resources for output
  * Form Requests for validation
  * Factories & Seeders for test data
* **Error handling**: custom exceptions and HTTP status codes
* **Static analysis**: integrate PHPStan and Psalm

Naming examples:

* `WaterBill`, `MeterReading`, `ConsumerLedger`
* Repositories: `WaterBillRepository` with interface `WaterBillRepositoryInterface`
* Services: `BillingService`, `PaymentService`, `MeterService`

## 8. API Design & Contracts

* Base path: `/api/v1/`
* Use OpenAPI / Swagger to document endpoints
* Use API Resources to keep responses consistent:

  * `WaterBillResource`, `PaymentResource`, `ConsumerResource`
* Pagination: cursor or offset/limit (be consistent)
* Error responses: JSON with `errors` and `code` keys

Example endpoints:

```
GET /api/v1/consumers
POST /api/v1/consumers
POST /api/v1/meter-readings
POST /api/v1/billing/generate  (admin only)
GET /api/v1/consumers/{id}/bills
POST /api/v1/payments
```

## 9. Validation & Rules

* Use Form Requests with type-hinted DTOs where helpful
* Custom validation rules:

  * Meter reading monotonicity
  * Consumer uniqueness constraints
  * Payment amount matches outstanding balance (with tolerance)

Always sanitize and normalize inputs (trim strings, normalize phone numbers).

## 10. Authentication & Authorization

* **Auth**: Laravel Sanctum for SPA/API tokens
* **Password policy**: strong hashing (bcrypt/argon2), password resets
* **2FA**: optional for critical roles
* **Authorization**: Policies for model-level checks; Gates for generic checks
* Use role & permission package (e.g., spatie/laravel-permission) for RBAC

## 11. Audit, Logging & Events

* Use model observers or explicit services to write audit logs into `activity_logs` table
* Log critical actions: bill generation, payment, adjustment, consumer approval
* Event examples: `BillGenerated`, `PaymentCreated`, `MeterChanged` with listeners creating ledger entries and notifications

## 12. Testing Strategy

* Tools: PHPUnit, Pest (optional)
* Use in-memory sqlite for unit tests when possible
* Tests to include:

  * Unit tests for Services (billing logic, calculation)
  * Feature tests for controllers & endpoints (including validation)
  * Integration tests for full billing + payment flows
* Example priorities:

  1. Billing calculation accuracy (edge cases: negative readings, adjustment)
  2. Ledger consistency (debit/credit pairs)
  3. Payment & cancellation flows

## 13. Dev / Local Environment (Docker)

Provide a `docker-compose.yml` with services:

* `app` (PHP-FPM), `nginx`, `mysql` (or mariadb), `redis`, `phpmyadmin` (optional), `worker` (queue)
* Mount `./src` into container for live edits

Essential env vars (.env.example):

```
APP_NAME=WaterBilling
APP_ENV=local
APP_KEY=
APP_URL=http://localhost
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=water_billing
DB_USERNAME=root
DB_PASSWORD=root
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis
SANCTUM_STATEFUL_DOMAINS=localhost
```

Local commands:

* `composer install`
* `cp .env.example .env` + set env
* `php artisan key:generate`
* `php artisan migrate --seed`
* `php artisan queue:work` (or `horizon`)

## 14. CI/CD and Deployments

* Use GitHub Actions or equivalent
* Pipeline steps:

  1. `composer install --no-dev`
  2. Run static analysis (PHPStan)
  3. Run tests
  4. Build Docker image and push to registry
  5. Deploy to staging
* Use migrations & seeders as part of release process with zero-downtime migrations strategy

## 15. Security Considerations

* Never commit `.env`
* Use prepared statements (Eloquent/Query Builder) to avoid SQLi
* Rate limit sensitive endpoints
* Use HTTPS in production
* Sanitize file uploads and keep storage outside webroot (use signed URLs)
* Secure queue workers and background jobs
* Periodically run dependency audits

## 16. Performance & Optimization

* Use DB indexing on frequently queried columns (consumer_id, bill_status, reading_date)
* Eager load relations to avoid N+1
* Cache expensive read queries and reports (Redis)
* Use pagination where listing large datasets
* Use bulk operations for meter import / large bill generation
* Enable OPcache and tune PHP-FPM in production

## 17. Monitoring & Observability

* Application logs: daily rotating files + centralized log shipping (ELK)
* Errors: Sentry for error aggregation
* Queue & job monitoring: Laravel Horizon
* Metrics: expose app metrics (Prometheus) for request latency, queue length, error rate

## 18. Documentation & API Specs

* Maintain an OpenAPI (Swagger) spec in `docs/openapi.yaml`
* Keep a short developer onboarding doc (this file)
* Use API versioning (`/api/v1`) and a changelog for breaking changes

## 19. Onboarding Checklist & Next Steps

### Immediate setup

* [ ] Create repo and standard `.gitignore`
* [ ] Add `docker-compose.yml` and `.env.example`
* [ ] Scaffold core models + migrations for `consumers`, `meter_readings`, `water_bills`, `consumer_ledgers`, `payment_transactions`
* [ ] Add basic auth (Sanctum) and role seeder
* [ ] Add basic BillingService and unit tests for calculation

### Medium-term

* [ ] Add Events & Listeners for ledger creation
* [ ] Integrate Horizon and Redis queues
* [ ] Add OpenAPI docs and API contract tests
* [ ] Add auditing table & logic

### Long-term

* [ ] Payment gateway integrations (adapter pattern)
* [ ] Reporting micro-batch exports & scheduled jobs
* [ ] Add anomaly detection hook for AI forecasting

---

### Appendix: Quick snippet — billing calculation (pseudocode)

```php
// BillingService::calculateAmount(Consumer $consumer, MeterReading $current, MeterReading $previous, Period $period)
$consumption = max(0, $current->value - $previous->value);
$rate = $this->rateRepository->getRateForConsumer($consumer, $period);
$baseAmount = $consumption * $rate->unit_price;
$penalty = $this->penaltyCalculator->calculate($consumer, $period);
$misc = $this->miscBillRepository->sumForPeriod($consumer, $period);
$total = $baseAmount + $penalty + $misc;

return round($total, 2);
```

---

If you want, I can also:

* generate the initial migrations & model stubs for the key tables,
* scaffold Controllers + Requests for the main flows (consumer, readings, billing, payments), or
* produce an OpenAPI spec skeleton for `/api/v1` endpoints.

Tell me which of the above to generate next and I'll scaffold the code inside the same repo structure.
