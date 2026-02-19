# Reading Schedule Seeder Implementation Plan

**Branch:** `reading-schedule-seeder`
**Goal:** Seed default reading schedules so they exist on fresh deployment

---

## Context

Reading schedules require Areas to exist, and ServiceConnections need `area_id` set. Currently:
- **No AreaSeeder** exists (area table is empty after seeding)
- **ServiceConnectionSeeder** creates 5 connections but never sets `area_id`
- **No ReadingScheduleSeeder** exists

Dependencies already seeded: Statuses, Period (current month), Users (meter_reader@test.com, admin@test.com), ServiceConnections (5 records).

---

## Tasks

### Task 1: Create `AreaSeeder`

**File:** `database/seeders/AreaSeeder.php`

Create 16 areas matching the 16 Initao barangays. Follow the same pattern as `BarangaySeeder`:
- Use `DB::table('area')` for insert (consistent with other seeders)
- Check for existing records before inserting (idempotent)
- Set `stat_id` = ACTIVE status via `Status::getIdByDescription(Status::ACTIVE)`
- Set `created_at`/`updated_at` timestamps

Areas to seed:
```
Aluna, Andales, Apas, Calacapan, Gimangpang, Jampason,
Kamelon, Kanitoan, Oguis, Pagahan, Poblacion, Pontacon,
San Pedro, Sinalac, Tawantawan, Tubigan
```

Output info message with count of created/skipped areas.

---

### Task 2: Update `ServiceConnectionSeeder` to assign `area_id`

**File:** `database/seeders/ServiceConnectionSeeder.php`

Modify the existing seeder to assign `area_id` to each connection. After areas are seeded, query for existing area records and distribute the 5 connections across areas.

Changes:
- After the `AreaSeeder` runs (ordering handled in DatabaseSeeder), look up area IDs from the `area` table
- Assign each of the 5 service connections an `area_id` from the first few areas (e.g., spread across 3 areas: Poblacion gets 2, Sinalac gets 2, Aluna gets 1)
- Add `'area_id' => $areaId` to the `ServiceConnection::create()` call in the existing loop

The distribution mapping (hardcoded in the `$serviceConnections` array):
- Juan Dela Cruz (Residential) → Poblacion
- Maria Santos (Residential) → Poblacion
- Pedro Reyes (Residential) → Sinalac
- ABC Trading Corp (Commercial) → Sinalac
- Initao Hardware Store (Commercial) → Aluna

---

### Task 3: Create `ReadingScheduleSeeder`

**File:** `database/seeders/ReadingScheduleSeeder.php`

For the current period, create one pending reading schedule per area that has active service connections. Mirrors the logic in `ReadingScheduleService::createSchedule()` but uses direct DB inserts.

Logic:
1. Get current period: `DB::table('period')->where('is_closed', false)->orderBy('start_date', 'desc')->first()`
2. Get ACTIVE and PENDING status IDs
3. Get the meter reader user: `User::where('email', 'meter@test.com')->first()`
4. Get the admin user for `created_by`: `User::where('email', 'admin@test.com')->first()`
5. For each area that has active, non-ended ServiceConnections:
   a. Count the connections → `total_meters`
   b. Skip if `total_meters` is 0
   c. Check if schedule already exists for this area+period (idempotent)
   d. Create `ReadingSchedule` record:
      - `period_id` = current period `per_id`
      - `area_id` = area `a_id`
      - `reader_id` = meter reader user `id`
      - `scheduled_start_date` = 1st of current month
      - `scheduled_end_date` = 15th of current month
      - `status` = `'pending'`
      - `total_meters` = count from step (a)
      - `meters_read` = 0
      - `meters_missed` = 0
      - `created_by` = admin user `id`
      - `stat_id` = ACTIVE status ID
   e. Create `ReadingScheduleEntry` records for each connection:
      - `schedule_id` = the new schedule's `schedule_id`
      - `connection_id` = connection's `connection_id`
      - `sequence_order` = sequential (1, 2, 3...)
      - `status_id` = PENDING status ID
      - `created_at`/`updated_at` = now()

Output info message: "Reading Schedules seeded: X schedules with Y total entries"

---

### Task 4: Update `DatabaseSeeder`

**File:** `database/seeders/DatabaseSeeder.php`

Add the new seeders in the correct order. The dependency chain is:

```
AreaSeeder              ← NEW (before ServiceConnectionSeeder)
ServiceConnectionSeeder ← EXISTING (now depends on AreaSeeder for area_id)
ReadingScheduleSeeder   ← NEW (after both Area + ServiceConnection)
```

Changes:
1. Add `AreaSeeder::class` to the Phase 2 (service-related tables) `$this->call()` block — areas are reference data like account types
2. Add `ReadingScheduleSeeder::class` at the end of Phase 3 `$this->call()` block — after ServiceConnectionSeeder
3. Update the summary info messages to include:
   - `'- Areas: 16 areas (one per barangay)'`
   - `'- Reading Schedules: 1 per area with connections (pending status)'`

---

## Seeding Order (final)

```
Phase 1: Province → Town → Barangay → Purok
Phase 2: Status → UserType → AccountType → WaterRate → ChargeItem →
         BillAdjustmentType → Role → Permission → AreaSeeder (NEW)
Phase 3: RolePermission → TestUsers → Period → MiscReference →
         Meter → ServiceConnection (UPDATED) → ReadingScheduleSeeder (NEW)
```

---

## Verification

After implementation, run:
```bash
php artisan migrate:fresh --seed
```

Confirm:
- 16 areas exist in `area` table
- 5 service connections have non-null `area_id`
- Reading schedules exist for areas with connections (should be 3 schedules: Poblacion, Sinalac, Aluna)
- Each schedule has correct `reading_schedule_entries` count
- All schedules have status `pending`
