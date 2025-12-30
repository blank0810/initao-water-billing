# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

---

## 🎯 Need Help With...?

- 🐳 **Setup/Commands/Docker?** → See [`.claude/SETUP.md`](.claude/SETUP.md)
- 💧 **Features/Architecture/Business Rules?** → See [`.claude/FEATURES.md`](.claude/FEATURES.md)

---

## Project Overview

**Initao Water Billing System** - A Laravel 12 water utility billing application for managing water service connections, meter readings, billing, and payments in Initao, Philippines.

**Tech Stack:**
- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Blade templates, Alpine.js, Tailwind CSS 3, Flowbite
- **Build:** Vite 7
- **Database:** MySQL 8
- **Testing:** Pest PHP
- **Code Quality:** Laravel Pint (PSR-12)
- **UI Libraries:** Chart.js, DataTables (responsive)

**Development Philosophy:**
- Build fast, maintain clean
- Organize by **feature** (Billing, Auth, Payments, Consumers)
- Keep logic out of controllers — **Services handle business rules**
- Use **Eloquent models** directly for data access (no repository abstraction)
- Use **Events** or **Observers** only when logic must be decoupled

---

## ⚠️ Top 5 Critical Rules

1. **⚠️ DUAL BILLING SYSTEM EXISTS** - Two parallel systems (Modern ServiceConnection-based vs Legacy Consumer-based). Confirm which system before coding. See [`.claude/FEATURES.md`](.claude/FEATURES.md) for details.

2. **No business logic in controllers** - Always use Services. Controllers only orchestrate request → service → response.

3. **Status table dependency** - Ensure Status model exists and is seeded before creating records. Use `Status::getIdByDescription(Status::ACTIVE)`.

4. **Polymorphic relations** - Always check `source_type`/`target_type` before accessing polymorphic relations (CustomerLedger, PaymentAllocation).

5. **Period closure** - Respect `is_closed` flag on Period model. No modifications to closed periods.

---

## 🚀 Quick Commands

```bash
# Setup & Start
composer setup                   # Full setup (install deps, migrate, build assets)
composer dev                     # Start dev server (concurrent: Laravel, queue, logs, Vite)

# Development
php artisan serve               # Laravel server (port 8000)
php artisan test                # Run tests (Pest)
php artisan test --filter=ClassName  # Run specific test
./vendor/bin/pint               # Format code (PSR-12)

# Frontend
npm run dev                     # Start Vite dev server
npm run build                   # Build production assets

# Docker
docker-compose up -d            # Start all services
docker-compose logs -f          # View logs
docker-compose exec water_billing_app php artisan migrate

# Database
php artisan migrate             # Run migrations
php artisan migrate:fresh --seed # Fresh start with data
php artisan migrate:status      # Check migration status
```

**See [`.claude/SETUP.md`](.claude/SETUP.md) for complete command reference.**

---

## 🏗️ Architecture at a Glance

### Modern System (Recommended)
**Flow:** `ServiceConnection` → `MeterAssignment` → `MeterReading` → `water_bill_history`
**Ledger:** `CustomerLedger` (polymorphic: BILL/CHARGE/PAYMENT)
**Payment:** `Payment` → `PaymentAllocation` (polymorphic distribution)
**Tables:** PascalCase (`Payment`, `MeterReading`, `ServiceConnection`, etc.)

### Legacy System (Maintenance Only)
**Flow:** `Consumer` → `consumer_meters` → `consumer_ledger` → `water_bill`
**Tables:** snake_case (`consumer_meters`, `meter_readers`, etc.)

**⚠️ Note:** Both systems coexist in production. For complete documentation, see [`.claude/FEATURES.md`](.claude/FEATURES.md).

---

## 📁 Project Structure

```
app/
  Http/
    Controllers/
      Auth/              # Authentication controllers
      Admin/             # Admin features
      Api/               # API endpoints
      Address/           # Address management
  Services/              # Business logic (NO repositories!)
      Address/
      Customers/
  Models/                # Eloquent models
  Rules/                 # Custom validation rules
  Traits/                # Reusable model traits
  Events/                # Domain events
  Listeners/             # Event handlers
  Helpers/               # Helper utilities
resources/
  views/                 # Blade templates
  js/                    # Alpine.js components
  css/                   # Tailwind styles
```

**Organization:** Feature-based, not layer-based. See [`.claude/FEATURES.md`](.claude/FEATURES.md) for details.

---

## 🔑 Key Models

**Customer Management:**
- `Customer`, `ServiceConnection`, `ServiceApplication`, `ConsumerAddress`

**Billing (Modern):**
- `ServiceConnection`, `MeterAssignment`, `MeterReading`, `water_bill_history`, `BillAdjustment`

**Billing (Legacy):**
- `Consumer`, `consumer_meters`, `consumer_ledger`, `water_bill`, `misc_bill`

**Payment:**
- `Payment`, `PaymentAllocation`, `CustomerLedger`, `CustomerCharge`

**Shared:**
- `Period`, `Status`, `Area`, `MeterReader`, `User`

**For complete relationships and details, see [`.claude/FEATURES.md`](.claude/FEATURES.md).**

---

## 📝 Commit Message Format

```
feat(billing): add billing generation logic
fix(payments): correct rounding issue
chore: update composer dependencies
refactor(consumers): extract service logic
docs: update CLAUDE.md
```

---

## 📚 Documentation Structure

```
├── CLAUDE.md                      # This file (navigation hub)
├── CLAUDE.md.backup              # Original file (archived)
├── .claude/
│   ├── SETUP.md                  # Setup, commands, Docker, testing
│   └── FEATURES.md               # Complete architecture & features
└── local_context/
    ├── features/                 # Feature implementation history
    │   └── README.md
    └── patterns/                 # Reusable Laravel patterns
        └── README.md
```

---

## 💡 Development Principles

| Principle         | Description                                    |
| ----------------- | ---------------------------------------------- |
| **KISS**          | Keep It Simple, Scalable                       |
| **DRY**           | Reuse services and traits                      |
| **SRP**           | One class = one responsibility                 |
| **Separation**    | Controllers handle flow, Services handle logic |
| **Fast Delivery** | Prioritize completion, not abstraction         |
| **Readability**   | Code should explain itself                     |

---

## 🆘 Need More Help?

- **Setup issues?** → [`.claude/SETUP.md`](.claude/SETUP.md) - Installation, Docker, testing
- **Feature questions?** → [`.claude/FEATURES.md`](.claude/FEATURES.md) - Architecture, models, business rules
- **Pattern examples?** → `local_context/patterns/` - Reusable Laravel patterns (document as you discover)
- **Recent implementations?** → `local_context/features/` - Feature history (document as you build)

---

## ⚡ Quick Reference

**Helper Functions:**
```php
// Generate resolution number
CustomerHelper::generateCustomerResolutionNumber($firstName, $middleName, $lastName)
// Returns: INITAO-ABC-1234567890

// Get status ID
$statusId = Status::getIdByDescription(Status::ACTIVE);
```

**Status Constants:**
- `Status::PENDING`
- `Status::ACTIVE`
- `Status::INACTIVE`

**For complete helper documentation, see [`.claude/FEATURES.md`](.claude/FEATURES.md).**

---

## 🔧 Common Development Tasks

**Adding a new feature:**
1. Create Service in `app/Services/FeatureName/`
2. Create Controller in `app/Http/Controllers/FeatureName/`
3. Create Request validation in `app/Http/Requests/`
4. Add routes in `routes/web.php` or `routes/api.php`
5. Create Blade views in `resources/views/feature-name/`
6. Write tests in `tests/Feature/`

**Working with the database:**
1. Create migration: `php artisan make:migration create_table_name`
2. Define schema in migration file
3. Run migration: `php artisan migrate`
4. Create model: `php artisan make:model ModelName`
5. Define relationships and casts in model
6. Create seeder if needed: `php artisan make:seeder TableSeeder`

**Frontend development:**
1. Edit Blade templates in `resources/views/`
2. Add Alpine.js components in `resources/js/`
3. Style with Tailwind classes (Flowbite components available)
4. Run `npm run dev` for hot reload
5. DataTables and Chart.js are available for data visualization

---

_Last updated: 2025-12-24_
_Stack: Laravel 12, MySQL, Docker, Pest PHP, Alpine.js, Tailwind CSS, Vite_
_Project: Initao Water Billing System_
