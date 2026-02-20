# Automated Features Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add four features: auto-create periods with rates, auto-apply penalties, auto-close reading schedules, and enforce adjustment/recompute validation rules.

**Architecture:** A new `SystemSetting` model stores key-value toggles. Two Laravel scheduled commands handle period creation and penalty application. Reading schedule auto-close is event-driven (checked after each upload). Adjustment/recompute validation is enforced by adding guards in `BillAdjustmentService`.

**Tech Stack:** Laravel 12 (PHP 8.2), Pest PHP for testing, Laravel Scheduler, existing NotificationService, existing PeriodService/WaterRateService/PenaltyService/BillAdjustmentService.

---

## Task 1: Create SystemSetting Model and Migration

**Files:**
- Create: `database/migrations/2026_02_19_000001_create_system_settings_table.php`
- Create: `app/Models/SystemSetting.php`
- Create: `database/seeders/SystemSettingSeeder.php`

**Step 1: Create the migration**

```php
// database/migrations/2026_02_19_000001_create_system_settings_table.php
Schema::create('system_settings', function (Blueprint $table) {
    $table->id();
    $table->string('key')->unique();
    $table->text('value')->nullable();
    $table->string('type')->default('string'); // string, boolean, integer
    $table->string('group')->default('general'); // automation, billing, etc.
    $table->string('description')->nullable();
    $table->timestamps();
});
```

**Step 2: Create the SystemSetting model**

```php
// app/Models/SystemSetting.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'description'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $setting->value,
            default => $setting->value,
        };
    }

    public static function setValue(string $key, mixed $value): void
    {
        static::where('key', $key)->update(['value' => (string) $value]);
    }

    public static function isEnabled(string $key): bool
    {
        return (bool) static::getValue($key, false);
    }
}
```

**Step 3: Create seeder for automation settings**

```php
// database/seeders/SystemSettingSeeder.php
// Seeds these settings:
// auto_create_period    => false  (boolean, automation group, "Automatically create next month period on the last day of each month")
// auto_apply_penalties  => false  (boolean, automation group, "Automatically apply late payment penalties daily to overdue bills")
// auto_close_reading_schedule => true (boolean, automation group, "Auto-complete reading schedules when all entries are read")
```

**Step 4: Run migration and seeder**

Run: `php artisan migrate && php artisan db:seed --class=SystemSettingSeeder`

**Step 5: Commit**

```
feat(settings): add SystemSetting model for automation toggles
```

---

## Task 2: Auto-Create Period Command

**Files:**
- Create: `app/Console/Commands/AutoCreatePeriod.php`
- Modify: `routes/console.php` (add schedule entry after line 14)
- Modify: `app/Services/Notification/NotificationService.php` (add notification method after line 353)
- Modify: `app/Models/Notification.php` (add type constant after line 60)

**Step 1: Add notification type constant**

In `app/Models/Notification.php`, after line 60 (`TYPE_USER_CREATED`), add:

```php
public const TYPE_PERIOD_AUTO_CREATED = 'period_auto_created';
public const TYPE_SCHEDULE_OVERDUE = 'schedule_overdue';
```

Also update `getCategoryColor()` to handle `period_auto_created` (return `'amber'`) and `schedule_overdue` (return `'red'`).

Update `getCategory()` to include `TYPE_PERIOD_AUTO_CREATED` under `'billing'` and `TYPE_SCHEDULE_OVERDUE` under `'billing'`.

Update `getTypesForCategory()` in `NotificationService.php` to include both new types under `'billing'`.

**Step 2: Add notification method to NotificationService**

In `app/Services/Notification/NotificationService.php`, add after `notifyUserCreated()` (after line 353):

```php
public function notifyPeriodAutoCreated(string $periodName): void
{
    $this->notifyByRole(
        Notification::TYPE_PERIOD_AUTO_CREATED,
        'Period Auto-Created',
        "Period {$periodName} was automatically created with rates copied from the previous period.",
        null, null, null, null
    );
}

public function notifyScheduleOverdue(string $areaName, string $periodName, int $scheduleId): void
{
    $this->notifyByRole(
        Notification::TYPE_SCHEDULE_OVERDUE,
        'Reading Schedule Overdue',
        "Reading schedule for {$areaName} ({$periodName}) is past its end date and needs manual review.",
        null, null, null, null
    );
}
```

**Step 3: Create the artisan command**

```php
// app/Console/Commands/AutoCreatePeriod.php
namespace App\Console\Commands;

use App\Models\Period;
use App\Models\SystemSetting;
use App\Services\Billing\PeriodService;
use App\Services\Billing\WaterRateService;
use App\Services\Notification\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoCreatePeriod extends Command
{
    protected $signature = 'billing:auto-create-period';
    protected $description = 'Auto-create next month period and copy rates from current month';

    public function handle(
        PeriodService $periodService,
        WaterRateService $waterRateService,
        NotificationService $notificationService
    ): int {
        if (! SystemSetting::isEnabled('auto_create_period')) {
            $this->info('Auto-create period is disabled.');
            return self::SUCCESS;
        }

        $nextMonth = Carbon::now()->addMonth();
        $perCode = $nextMonth->format('Ym');

        // Check if next month already exists (idempotent)
        if (Period::where('per_code', $perCode)->exists()) {
            $this->info("Period {$perCode} already exists. Skipping.");
            return self::SUCCESS;
        }

        // Create the period
        $result = $periodService->createPeriod([
            'per_name' => $nextMonth->format('F Y'),
            'per_code' => $perCode,
            'start_date' => $nextMonth->startOfMonth()->format('Y-m-d'),
            'end_date' => $nextMonth->endOfMonth()->format('Y-m-d'),
            'grace_period' => 10,
        ]);

        if (! $result['success']) {
            $this->error('Failed to create period: ' . $result['message']);
            return self::FAILURE;
        }

        $newPeriodId = $result['data']['per_id'];

        // Copy rates from current month (or defaults) at 0% adjustment
        $currentPeriod = Period::where('is_closed', false)
            ->where('per_code', '!=', $perCode)
            ->orderBy('start_date', 'desc')
            ->first();

        $sourcePeriodId = $currentPeriod?->per_id;
        $ratesCopied = $waterRateService->copyRatesToPeriod($newPeriodId, 0, $sourcePeriodId);

        // Notify admins
        $notificationService->notifyPeriodAutoCreated($nextMonth->format('F Y'));

        $this->info("Period {$nextMonth->format('F Y')} created with {$ratesCopied} rates copied.");
        return self::SUCCESS;
    }
}
```

**Step 4: Register in scheduler**

In `routes/console.php`, add after line 14:

```php
Schedule::command('billing:auto-create-period')
    ->lastDayOfMonth('00:00')
    ->description('Auto-create next month billing period and copy rates');
```

**Step 5: Commit**

```
feat(automation): add auto-create period command with rate copying
```

---

## Task 3: Auto-Apply Penalties Command

**Files:**
- Create: `app/Console/Commands/AutoApplyPenalties.php`
- Modify: `routes/console.php` (add schedule entry)

**Step 1: Create the artisan command**

```php
// app/Console/Commands/AutoApplyPenalties.php
namespace App\Console\Commands;

use App\Models\SystemSetting;
use App\Services\Billing\PenaltyService;
use Illuminate\Console\Command;

class AutoApplyPenalties extends Command
{
    protected $signature = 'billing:auto-apply-penalties';
    protected $description = 'Automatically apply penalties to overdue bills';

    public function handle(PenaltyService $penaltyService): int
    {
        if (! SystemSetting::isEnabled('auto_apply_penalties')) {
            $this->info('Auto-apply penalties is disabled.');
            return self::SUCCESS;
        }

        // PenaltyService::processAllOverdueBills already:
        // - Finds overdue bills (due_date < today, stat_id = ACTIVE, period open)
        // - Skips bills that already have penalties (hasExistingPenalty check)
        // - Creates penalty charges + ledger entries
        // - Sends notification via NotificationService
        // This is inherently one-time-per-bill since hasExistingPenalty() prevents duplicates
        $result = $penaltyService->processAllOverdueBills(0); // userId=0 for system

        $this->info($result['message']);
        $this->info("Processed: {$result['processed']}, Skipped: {$result['skipped']}");

        return self::SUCCESS;
    }
}
```

**Step 2: Register in scheduler**

In `routes/console.php`, add:

```php
Schedule::command('billing:auto-apply-penalties')
    ->daily()
    ->description('Auto-apply penalties to overdue bills');
```

**Step 3: Commit**

```
feat(automation): add auto-apply penalties daily command
```

---

## Task 4: Auto-Close Reading Schedule on All Entries Completed

**Files:**
- Modify: `app/Services/Billing/UploadedReadingService.php:294-300` (add auto-close check after incrementing meters_read)
- Modify: `app/Services/Billing/ReadingScheduleService.php` (add autoCompleteSchedule method after line 496)

**Step 1: Add system completion method to ReadingScheduleService**

In `app/Services/Billing/ReadingScheduleService.php`, add after `completeSchedule()` (after line 496):

```php
/**
 * Auto-complete a schedule when all entries are completed.
 * Called by system (not a user action).
 */
public function autoCompleteSchedule(int $scheduleId): array
{
    $schedule = ReadingSchedule::with('period')->find($scheduleId);

    if (! $schedule || $schedule->status !== 'in_progress') {
        return ['success' => false, 'message' => 'Schedule not eligible for auto-completion.'];
    }

    if ($schedule->period?->is_closed) {
        return ['success' => false, 'message' => 'Cannot modify schedule for a closed period.'];
    }

    // Verify ALL entries are completed
    $pendingCount = ReadingScheduleEntry::where('schedule_id', $scheduleId)
        ->where('status_id', '!=', Status::getIdByDescription(Status::COMPLETED))
        ->count();

    if ($pendingCount > 0) {
        return ['success' => false, 'message' => "Still {$pendingCount} entries pending."];
    }

    $schedule->update([
        'status' => 'completed',
        'actual_end_date' => now()->format('Y-m-d'),
        'meters_missed' => 0,
    ]);

    return [
        'success' => true,
        'message' => 'Schedule auto-completed (all entries read).',
        'data' => $this->getScheduleById($schedule->schedule_id),
    ];
}
```

**Step 2: Modify UploadedReadingService to trigger auto-close**

In `app/Services/Billing/UploadedReadingService.php`, replace the `updateScheduleMetersRead()` method (lines 294-300) with:

```php
private function updateScheduleMetersRead(array $scheduleUploadCounts): void
{
    foreach ($scheduleUploadCounts as $scheduleId => $count) {
        ReadingSchedule::where('schedule_id', $scheduleId)
            ->increment('meters_read', $count);

        // Check if all entries are now completed -> auto-close
        if (SystemSetting::isEnabled('auto_close_reading_schedule')) {
            $this->tryAutoCompleteSchedule($scheduleId);
        }
    }
}

private function tryAutoCompleteSchedule(int $scheduleId): void
{
    $schedule = ReadingSchedule::find($scheduleId);

    if (! $schedule || $schedule->status !== 'in_progress') {
        return;
    }

    $totalEntries = ReadingScheduleEntry::where('schedule_id', $scheduleId)->count();
    $completedEntries = ReadingScheduleEntry::where('schedule_id', $scheduleId)
        ->where('status_id', Status::getIdByDescription(Status::COMPLETED))
        ->count();

    if ($totalEntries > 0 && $completedEntries >= $totalEntries) {
        app(ReadingScheduleService::class)->autoCompleteSchedule($scheduleId);
    }
}
```

Add import at top of `UploadedReadingService.php`:

```php
use App\Models\SystemSetting;
```

**Step 3: Commit**

```
feat(automation): auto-close reading schedule when all entries completed
```

---

## Task 5: Overdue Reading Schedule Notification Command

**Files:**
- Create: `app/Console/Commands/NotifyOverdueSchedules.php`
- Modify: `routes/console.php` (add schedule entry)

**Step 1: Create the artisan command**

```php
// app/Console/Commands/NotifyOverdueSchedules.php
namespace App\Console\Commands;

use App\Models\ReadingSchedule;
use App\Services\Notification\NotificationService;
use Illuminate\Console\Command;

class NotifyOverdueSchedules extends Command
{
    protected $signature = 'billing:notify-overdue-schedules';
    protected $description = 'Send notifications for reading schedules past their end date';

    public function handle(NotificationService $notificationService): int
    {
        $overdueSchedules = ReadingSchedule::with(['area', 'period'])
            ->where('status', 'in_progress')
            ->whereDate('scheduled_end_date', '<', now()->toDateString())
            ->get();

        $notified = 0;
        foreach ($overdueSchedules as $schedule) {
            $notificationService->notifyScheduleOverdue(
                $schedule->area?->a_desc ?? 'Unknown',
                $schedule->period?->per_name ?? 'Unknown',
                $schedule->schedule_id
            );
            $notified++;
        }

        $this->info("Sent {$notified} overdue schedule notification(s).");
        return self::SUCCESS;
    }
}
```

**Step 2: Register in scheduler**

In `routes/console.php`, add:

```php
Schedule::command('billing:notify-overdue-schedules')
    ->daily()
    ->description('Notify admins about overdue reading schedules');
```

**Step 3: Commit**

```
feat(automation): add overdue reading schedule daily notification
```

---

## Task 6: Adjustment/Recompute Validation Guards

**Files:**
- Modify: `app/Services/Billing/BillAdjustmentService.php:63-79` (adjustConsumption - add open period guard)
- Modify: `app/Services/Billing/BillAdjustmentService.php:209-220` (adjustAmount - add open period guard)
- Modify: `app/Services/Billing/BillAdjustmentService.php:17-25` (update class docblock)
- Modify: `resources/views/components/ui/billing/adjustment-modal.blade.php` (update UI message)
- Modify: `resources/views/components/ui/billing/recompute-modal.blade.php` (update UI message)

**Step 1: Add period check to adjustConsumption()**

In `app/Services/Billing/BillAdjustmentService.php`, in `adjustConsumption()`, add after the `$connection` null check (after line 79):

```php
// Block adjustments on open periods - use recompute instead
$period = $bill->period;
if ($period && ! $period->is_closed) {
    return [
        'success' => false,
        'message' => 'This bill\'s period is still open. Use "Recompute Bill" to correct billing errors during an open period. Adjustments are for closed/finalized periods only.',
    ];
}
```

**Step 2: Add period check to adjustAmount()**

In `app/Services/Billing/BillAdjustmentService.php`, in `adjustAmount()`, add after loading the bill (after line 220):

```php
// Block adjustments on open periods - use recompute instead
$period = $bill->period;
if ($period && ! $period->is_closed) {
    return [
        'success' => false,
        'message' => 'This bill\'s period is still open. Use "Recompute Bill" to correct billing errors during an open period. Adjustments are for closed/finalized periods only.',
    ];
}
```

**Step 3: Update the class docblock**

Replace lines 17-25 with:

```php
/**
 * Service for handling bill adjustments and recomputation.
 *
 * BUSINESS RULES:
 * - Adjustments (consumption or amount) are ONLY for CLOSED periods.
 *   Use case: Customer files a dispute after billing is finalized.
 * - Recomputation is ONLY for OPEN periods.
 *   Use case: Customer disputes amount at MEEDO before period closes.
 * - These are mutually exclusive by period state, preventing conflicts.
 */
```

**Step 4: Update adjustment modal UI messaging**

In `resources/views/components/ui/billing/adjustment-modal.blade.php`, update the informational note to:

> "Note: Adjustments can only be applied to bills in closed periods. For open period corrections, use Recompute Bill."

**Step 5: Update recompute modal UI messaging**

In `resources/views/components/ui/billing/recompute-modal.blade.php`, ensure messaging states:

> "Note: Recompute is only available for open periods. For corrections after a period is closed, use Bill Adjustment."

**Step 6: Commit**

```
fix(billing): enforce adjustment/recompute mutual exclusivity by period state
```

---

## Task 7: Admin Automation Settings Page

**Files:**
- Create: `app/Http/Controllers/Admin/Config/AutomationSettingController.php`
- Create: `app/Services/Admin/Config/AutomationSettingService.php`
- Create: `resources/views/pages/admin/config/automation-settings/index.blade.php`
- Modify: `routes/web.php` (add routes in admin config group)
- Modify: `resources/views/components/sidebar-revamped.blade.php` (add menu item)

**Step 1: Create the service**

```php
// app/Services/Admin/Config/AutomationSettingService.php
namespace App\Services\Admin\Config;

use App\Models\SystemSetting;

class AutomationSettingService
{
    public function getAutomationSettings(): array
    {
        return SystemSetting::where('group', 'automation')
            ->get()
            ->mapWithKeys(fn ($s) => [$s->key => [
                'value' => match ($s->type) {
                    'boolean' => filter_var($s->value, FILTER_VALIDATE_BOOLEAN),
                    'integer' => (int) $s->value,
                    default => $s->value,
                },
                'type' => $s->type,
                'description' => $s->description,
            ]])
            ->toArray();
    }

    public function updateSetting(string $key, mixed $value): array
    {
        $setting = SystemSetting::where('key', $key)->where('group', 'automation')->first();

        if (! $setting) {
            return ['success' => false, 'message' => 'Setting not found.'];
        }

        $setting->update(['value' => (string) $value]);

        return ['success' => true, 'message' => 'Setting updated.'];
    }
}
```

**Step 2: Create the controller**

```php
// app/Http/Controllers/Admin/Config/AutomationSettingController.php
namespace App\Http\Controllers\Admin\Config;

use App\Http\Controllers\Controller;
use App\Services\Admin\Config\AutomationSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AutomationSettingController extends Controller
{
    public function __construct(private AutomationSettingService $service) {}

    public function index()
    {
        $settings = $this->service->getAutomationSettings();
        return view('pages.admin.config.automation-settings.index', compact('settings'));
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => 'required|string',
            'value' => 'required',
        ]);

        $result = $this->service->updateSetting($validated['key'], $validated['value']);
        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
```

**Step 3: Create the Blade view**

Create `resources/views/pages/admin/config/automation-settings/index.blade.php` with:
- Toggle switches for each automation setting using Alpine.js
- Description text for each toggle explaining what it does
- Follow existing admin config page patterns (same layout as document-signatories page)

Each toggle card should show:
- **Auto-Create Period**: "Automatically create next month's period on the last day of each month and copy rates from the current month."
- **Auto-Apply Penalties**: "Automatically apply late payment penalties daily to overdue bills past their grace period."
- **Auto-Close Reading Schedule**: "Automatically mark reading schedules as completed when all entries have been read."

**Step 4: Add routes**

In `routes/web.php`, within the admin config route group, add:

```php
Route::get('/automation-settings', [AutomationSettingController::class, 'index'])
    ->middleware('permission:settings.manage')
    ->name('config.automation-settings.index');
Route::post('/automation-settings', [AutomationSettingController::class, 'update'])
    ->middleware('permission:settings.manage')
    ->name('config.automation-settings.update');
```

**Step 5: Add sidebar menu item**

In `resources/views/components/sidebar-revamped.blade.php`, add an "Automation" link in the admin config section, pointing to the automation settings route.

**Step 6: Commit**

```
feat(admin): add automation settings configuration page
```

---

## Task 8: Write Tests

**Files:**
- Create: `tests/Feature/AutoCreatePeriodTest.php`
- Create: `tests/Feature/AutoApplyPenaltiesTest.php`
- Create: `tests/Feature/AutoCloseReadingScheduleTest.php`
- Create: `tests/Feature/AdjustmentRecomputeValidationTest.php`

**Step 1: Test auto-create period**

```php
// tests/Feature/AutoCreatePeriodTest.php
test('auto-create period is skipped when toggle is off', function () {
    // Set toggle off, run command, assert no new period created
});

test('auto-create period creates next month when toggle is on', function () {
    // Set toggle on, run command, assert period exists with correct per_code
});

test('auto-create period is idempotent', function () {
    // Set toggle on, create next month period manually, run command, assert only one period
});

test('auto-create period copies rates from current month', function () {
    // Set toggle on, create current month with rates, run command, assert rates exist on new period
});
```

**Step 2: Test auto-apply penalties**

```php
// tests/Feature/AutoApplyPenaltiesTest.php
test('auto-apply penalties is skipped when toggle is off', function () {
    // Set toggle off, create overdue bill, run command, assert no penalty
});

test('auto-apply penalties creates penalties for overdue bills', function () {
    // Set toggle on, create overdue bill, run command, assert penalty exists
});

test('auto-apply penalties skips bills that already have penalties', function () {
    // Set toggle on, create overdue bill with existing penalty, run command, assert no duplicate
});
```

**Step 3: Test adjustment/recompute validation**

```php
// tests/Feature/AdjustmentRecomputeValidationTest.php
test('consumption adjustment is blocked on open period', function () {
    // Create bill in open period, attempt adjustConsumption, assert failure with message
});

test('amount adjustment is blocked on open period', function () {
    // Create bill in open period, attempt adjustAmount, assert failure with message
});

test('consumption adjustment is allowed on closed period', function () {
    // Create bill in closed period, attempt adjustConsumption, assert success
});

test('recompute is blocked on closed period', function () {
    // Already tested by existing guard, but verify message
});

test('recompute is allowed on open period', function () {
    // Create bill in open period, attempt recompute, assert success
});
```

**Step 4: Test auto-close reading schedule**

```php
// tests/Feature/AutoCloseReadingScheduleTest.php
test('schedule auto-completes when all entries are completed', function () {
    // Create schedule with entries, mark all entries as COMPLETED, trigger upload, assert schedule is completed
});

test('schedule does not auto-complete with pending entries', function () {
    // Create schedule with entries, mark some as COMPLETED, trigger upload, assert schedule still in_progress
});

test('schedule does not auto-complete when toggle is off', function () {
    // Set toggle off, mark all entries, trigger upload, assert schedule still in_progress
});
```

**Step 5: Run all tests**

Run: `php artisan test --filter="AutoCreate|AutoApply|AutoClose|AdjustmentRecompute"`
Expected: All pass.

**Step 6: Commit**

```
test(automation): add tests for all four automated features
```

---

## Task 9: Final Integration and Code Quality

**Step 1: Run full test suite**

Run: `php artisan test`
Expected: All existing + new tests pass.

**Step 2: Run code formatter**

Run: `./vendor/bin/pint`

**Step 3: Final commit**

```
chore: format code with Pint
```

---

## Summary of Changes

| Area | Files Created | Files Modified |
|------|--------------|----------------|
| SystemSetting | Model, Migration, Seeder | - |
| Auto-Create Period | Command | console.php, NotificationService, Notification model |
| Auto-Apply Penalties | Command | console.php |
| Auto-Close Schedule | - | UploadedReadingService, ReadingScheduleService |
| Overdue Notification | Command | console.php, NotificationService |
| Adjustment Validation | - | BillAdjustmentService, adjustment/recompute modals |
| Admin Settings | Controller, Service, View | routes/web.php, sidebar |
| Tests | 4 test files | - |

## Scheduler Summary (routes/console.php)

```php
// Existing
Schedule::call(fn () => app(NotificationService::class)->cleanupOldNotifications(90))
    ->daily()->description('Clean up old notifications');

// New
Schedule::command('billing:auto-create-period')
    ->lastDayOfMonth('00:00')
    ->description('Auto-create next month billing period and copy rates');

Schedule::command('billing:auto-apply-penalties')
    ->daily()
    ->description('Auto-apply penalties to overdue bills');

Schedule::command('billing:notify-overdue-schedules')
    ->daily()
    ->description('Notify admins about overdue reading schedules');
```
