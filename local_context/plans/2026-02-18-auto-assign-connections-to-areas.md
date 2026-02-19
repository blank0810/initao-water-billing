# Auto-Assign Service Connections to Areas

**Date:** 2026-02-18
**Branch:** reading-schedule-seeder
**Status:** Design approved

## Overview

Add a manual-trigger "Auto-Assign" button to the billing Areas & Assignments tab that bulk-assigns unassigned service connections to areas based on their address barangay.

## Design

### Matching Logic

```
ServiceConnection (area_id IS NULL)
  → address_id → ConsumerAddress
    → b_id → Barangay.b_desc
      → match → Area.a_desc
        → SET ServiceConnection.area_id = Area.a_id
```

### Rules

- **Only unassigned connections**: Only targets `ServiceConnection` where `area_id IS NULL`
- **Active connections only**: Must have `stat_id = ACTIVE` and `ended_at IS NULL`
- **Barangay-to-area name match**: `Barangay.b_desc` must exactly match `Area.a_desc`
- **Skip on mismatch**: Connections without an address, without a barangay, or whose barangay doesn't match any area name are counted as "unmatched" and skipped

### UI

- Single "Auto-Assign" button on the billing Areas & Assignments tab (next to existing "Bulk Assign" button)
- Summary toast after completion: "Auto-assigned X connections. Y could not be matched."

### Scope

- Manual trigger only (no auto-assignment on connection creation)
- Button lives in billing Areas tab only

## Implementation Plan

### Task 1: Add `autoAssignByBarangay()` to `AreaService`

**File:** `app/Services/Billing/AreaService.php`

New method:
1. Get all active areas, build a lookup map: `barangay_name → area_id` (since area names match barangay names)
2. Query all active `ServiceConnection` where `area_id IS NULL` and `ended_at IS NULL`
3. Eager load `address.barangay`
4. For each connection, look up `address.barangay.b_desc` in the map
5. If found, update `area_id`; if not, increment unmatched counter
6. Return `{ success, assigned_count, unmatched_count, message }`

### Task 2: Add controller endpoint

**File:** `app/Http/Controllers/AreaController.php`

New method `autoAssignConnections()`:
- Calls `$this->areaService->autoAssignByBarangay()`
- Returns JSON result

### Task 3: Add route

**File:** `routes/web.php`

Add under existing area routes:
```php
Route::post('/areas/auto-assign', [AreaController::class, 'autoAssignConnections'])->name('areas.auto-assign');
```

### Task 4: Add UI button and JS handler

**File:** `resources/views/pages/billing/areas-assignments-tab.blade.php`

- Add "Auto-Assign" button next to existing "Bulk Assign" button
- Add `autoAssignConnections()` JS function that:
  - Shows confirmation dialog
  - POSTs to `/areas/auto-assign`
  - Shows success/error toast
  - Refreshes the connections table and stats
