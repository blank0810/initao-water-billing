# Reading Schedule Admin Config Page - Implementation Plan

**Branch:** `reading-schedule-seeder`
**Goal:** Add a Reading Schedule config page to the admin section following the same CRUD + workflow pattern as Areas

---

## Context

The reading schedule full CRUD + lifecycle workflow already exists:
- **Backend:** `ReadingScheduleController` + `ReadingScheduleService` (fully working API)
- **Existing UI:** A tab inside the Billing page (`reading-schedule-tab.blade.php`) with vanilla JS

We need a **standalone admin config page** at `/config/reading-schedules` using the Alpine.js + configTable pattern, reusing the existing backend API at `/reading-schedules/*`.

---

## Tasks

### Task 1: Create `readingScheduleManager.js`

**File:** `resources/js/components/admin/config/reading-schedules/readingScheduleManager.js`

Extends `configTable` but points to `/reading-schedules` (existing API). Adds:
- `createSchedule()` - POST to `/reading-schedules`
- `updateSchedule()` - PUT to `/reading-schedules/{id}`
- `deleteSchedule()` - DELETE to `/reading-schedules/{id}`
- `startSchedule(id)` - POST to `/reading-schedules/{id}/start`
- `completeSchedule(id)` - POST to `/reading-schedules/{id}/complete`
- `delaySchedule(id)` - POST to `/reading-schedules/{id}/delay`
- `downloadSchedule(id)` - redirects to `/reading-schedules/{id}/download`
- `loadDropdowns()` - fetches areas, periods, meter readers for the create/edit form
- Override `init()` to call `loadDropdowns()` alongside `fetchItems()`
- Extra state: `areas: []`, `periods: []`, `meterReaders: []`, `showCompleteModal: false`, `showDelayModal: false`

Note: The existing `/reading-schedules` index endpoint returns `{success, data, stats}` not the paginated `{data, meta, links}` format. The manager must handle this by overriding `fetchItems()` to set `this.items = data.data` directly and also store `stats`.

Register on `window.readingScheduleManager`.

### Task 2: Register JS in `app.js`

**File:** `resources/js/app.js`

Add import:
```js
import './components/admin/config/reading-schedules/readingScheduleManager.js';
```

### Task 3: Create Blade index page

**File:** `resources/views/pages/admin/config/reading-schedules/index.blade.php`

Structure (following area pattern):
- `x-data="readingScheduleManager()"`
- Page header: "Manage Reading Schedules" with "Add Schedule" button
- Stats cards row (Total, Pending, In Progress, Completed, Delayed) - rendered from Alpine data
- Status filter dropdown (All, Pending, In Progress, Completed, Delayed)
- Table component
- 5 modals: Create, Edit, View, Complete, Delete
- Success/Error notifications

### Task 4: Create table component

**File:** `resources/views/components/ui/admin/config/reading-schedule/table.blade.php`

Columns: Period | Area | Reader | Scheduled Dates | Progress | Status | Actions

Action buttons change based on status (same logic as existing `getScheduleActionButtons`):
- Pending: Start, Download, Edit, Delete
- In Progress: Download, Complete, Delay, Delete
- Completed: View only
- Delayed: Delete

Progress column shows a bar with `meters_read/total_meters`.

### Task 5: Create modal components

**Files:**
- `resources/views/components/ui/admin/config/reading-schedule/modals/create-schedule.blade.php`
  - Form: Period dropdown, Area dropdown, Reader (auto-populated from area), Start Date, End Date, Total Meters (optional), Notes (optional)
- `resources/views/components/ui/admin/config/reading-schedule/modals/edit-schedule.blade.php`
  - Same as create but pre-populated
- `resources/views/components/ui/admin/config/reading-schedule/modals/view-schedule.blade.php`
  - Read-only: all fields + actual dates, meters read/missed, completion %, creator, completer
- `resources/views/components/ui/admin/config/reading-schedule/modals/complete-schedule.blade.php`
  - Form: Meters Read, Meters Missed
- `resources/views/components/ui/admin/config/reading-schedule/modals/delete-schedule.blade.php`
  - Confirmation with schedule info

### Task 6: Add route for the config page

**File:** `routes/web.php`

Add under the geographic config permission group (since reading schedules relate to areas):
```php
Route::get('/config/reading-schedules', [ReadingScheduleController::class, 'configIndex'])
    ->name('config.reading-schedules.index');
```

### Task 7: Add `configIndex` method to `ReadingScheduleController`

**File:** `app/Http/Controllers/ReadingScheduleController.php`

Add a new method that returns the Blade view (not JSON):
```php
public function configIndex()
{
    return view('pages.admin.config.reading-schedules.index');
}
```

The existing JSON endpoints remain unchanged - the Alpine.js manager calls them via fetch.

### Task 8: Add sidebar link

**File:** `resources/views/components/sidebar-revamped.blade.php`

Add "Reading Schedules" link under the Geographic submenu, after "Areas":
```blade
<a href="{{ route('config.reading-schedules.index') }}" ...>
    <i class="fas fa-calendar-alt"></i>
    <span>Reading Schedules</span>
</a>
```

Add to `routeMap`:
```js
'/config/reading-schedules': 'config-geographic-reading-schedules',
```

---

## Key Design Decisions

1. **Reuse existing API** - No new backend code needed except the `configIndex` route/method. The Alpine.js manager calls the existing `/reading-schedules/*` endpoints.
2. **Override fetchItems()** - The existing API returns `{success, data, stats}` not `{data, meta, links}`. The manager overrides the shared configTable fetch to handle this format.
3. **No pagination initially** - The existing API returns all schedules (no pagination). If needed later, can add pagination to the API.
4. **Status filter** - Uses the existing `?status=` query param the API already supports.

---

## Verification

1. Navigate to `/config/reading-schedules` - page loads with seeded schedule(s)
2. Create a new schedule via the modal
3. Start, complete, delay lifecycle actions work
4. Download CSV works
5. Delete works
6. Sidebar link highlights correctly
7. Areas config page at `/config/areas` now lists all 17 areas
