# Notifications Feature Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Wire up the existing notification infrastructure to real workflows and build the frontend (dropdown + full page) so staff see live notifications for applications, payments, connections, billing, and user events.

**Architecture:** Direct service calls from workflow services to `NotificationService`. Role-based targeting creates one notification row per recipient user. No Event/Listener layer. Frontend uses Alpine.js components calling existing API endpoints with enhanced filtering.

**Tech Stack:** Laravel 12, Alpine.js, Tailwind CSS 3, Flowbite, existing `notifications` DB table.

**Design doc:** `local_context/plans/2026-02-16-notifications-feature.md`

---

## Task 1: Fix Customer Model — Add fullName Accessor

The existing `NotificationService` references `$application->customer?->fullName` but the `Customer` model has no such accessor. Every notification method will fail without this fix.

**Files:**
- Modify: `app/Models/Customer.php`

**Step 1: Add the fullName accessor to Customer model**

Find the Customer model and add this accessor after the existing relationships/attributes:

```php
/**
 * Get the customer's full name
 */
public function getFullNameAttribute(): string
{
    return trim("{$this->cust_first_name} {$this->cust_middle_name} {$this->cust_last_name}");
}
```

**Step 2: Verify it works**

Run: `php artisan tinker --execute="echo App\Models\Customer::first()?->fullName ?? 'No customers';"`
Expected: A customer's full name string or "No customers"

**Step 3: Commit**

```bash
git add app/Models/Customer.php
git commit -m "fix(customers): add fullName accessor to Customer model"
```

---

## Task 2: Add New Notification Type Constants

The existing `Notification` model only has application and connection types. We need 5 new types for payments, billing, penalties, and user management.

**Files:**
- Modify: `app/Models/Notification.php` (lines 31-50, add after line 50)

**Step 1: Add the new type constants**

Add these constants after line 50 (`TYPE_CONNECTION_RECONNECTED`):

```php
public const TYPE_PAYMENT_PROCESSED = 'payment_processed';

public const TYPE_PAYMENT_CANCELLED = 'payment_cancelled';

public const TYPE_BILLS_GENERATED = 'bills_generated';

public const TYPE_PENALTY_PROCESSED = 'penalty_processed';

public const TYPE_USER_CREATED = 'user_created';
```

**Step 2: Add category color helper**

Add this static method at the end of the class (before the closing `}`):

```php
/**
 * Get the category color for a notification type
 */
public static function getCategoryColor(string $type): string
{
    return match (true) {
        str_starts_with($type, 'application_') => 'blue',
        in_array($type, [self::TYPE_APPLICATION_PAID, self::TYPE_PAYMENT_PROCESSED]) => 'green',
        in_array($type, [
            self::TYPE_CONNECTION_SUSPENDED,
            self::TYPE_CONNECTION_DISCONNECTED,
            self::TYPE_PAYMENT_CANCELLED,
            self::TYPE_PENALTY_PROCESSED,
        ]) => 'red',
        $type === self::TYPE_BILLS_GENERATED => 'amber',
        in_array($type, [self::TYPE_USER_CREATED, self::TYPE_CONNECTION_RECONNECTED]) => 'indigo',
        default => 'gray',
    };
}

/**
 * Get the category label for a notification type
 */
public static function getCategory(string $type): string
{
    return match (true) {
        str_starts_with($type, 'application_') => 'applications',
        in_array($type, [self::TYPE_PAYMENT_PROCESSED, self::TYPE_PAYMENT_CANCELLED, self::TYPE_APPLICATION_PAID]) => 'payments',
        str_starts_with($type, 'connection_') => 'connections',
        in_array($type, [self::TYPE_BILLS_GENERATED, self::TYPE_PENALTY_PROCESSED]) => 'billing',
        $type === self::TYPE_USER_CREATED => 'system',
        default => 'other',
    };
}
```

**Step 3: Commit**

```bash
git add app/Models/Notification.php
git commit -m "feat(notifications): add payment, billing, penalty, user type constants and category helpers"
```

---

## Task 3: Refactor NotificationService — Role-Based Targeting

The current `NotificationService::create()` takes a single `userId` (or null for broadcast). We need to change the notification methods to create one notification per recipient based on role targeting, excluding the acting user.

**Files:**
- Modify: `app/Services/Notification/NotificationService.php`

**Step 1: Add the resolveRecipients method and role mapping**

Add these after the `create()` method (after line 34):

```php
/**
 * Role mapping: which roles receive which notification types
 */
protected function getRolesForType(string $type): array
{
    $cashierTypes = [
        Notification::TYPE_APPLICATION_VERIFIED,
        Notification::TYPE_PAYMENT_PROCESSED,
        Notification::TYPE_BILLS_GENERATED,
    ];

    $baseRoles = [Role::SUPER_ADMIN, Role::ADMIN];

    if (in_array($type, $cashierTypes)) {
        $baseRoles[] = Role::CASHIER;
    }

    return $baseRoles;
}

/**
 * Resolve recipient user IDs based on notification type
 *
 * Returns users who have the required roles, excluding the acting user
 */
protected function resolveRecipients(string $type, ?int $excludeUserId = null): \Illuminate\Support\Collection
{
    $roles = $this->getRolesForType($type);

    $query = User::whereHas('roles', function ($q) use ($roles) {
        $q->whereIn('role_name', $roles);
    })->where('stat_id', Status::getIdByDescription(Status::ACTIVE));

    if ($excludeUserId) {
        $query->where('id', '!=', $excludeUserId);
    }

    return $query->pluck('id');
}

/**
 * Create a notification for each recipient based on role targeting
 */
protected function notifyByRole(
    string $type,
    string $title,
    string $message,
    ?string $link = null,
    ?string $sourceType = null,
    ?int $sourceId = null,
    ?int $excludeUserId = null
): void {
    $recipientIds = $this->resolveRecipients($type, $excludeUserId);

    foreach ($recipientIds as $userId) {
        $this->create($type, $title, $message, $userId, $link, $sourceType, $sourceId);
    }
}
```

**Step 2: Add `use` imports at the top of the file**

Add after `use App\Models\User;`:

```php
use App\Models\Role;
use App\Models\Status;
```

**Step 3: Refactor existing notify methods to use notifyByRole**

Replace every existing `notify*` method. Each method currently calls `$this->create(...)` with `null` userId (broadcast). Change them to call `$this->notifyByRole(...)` instead.

Replace `notifyApplicationSubmitted` (lines 39-53):

```php
/**
 * Notify about new application submission
 */
public function notifyApplicationSubmitted(ServiceApplication $application, ?int $actingUserId = null): void
{
    $customerName = $application->customer?->fullName ?? 'Unknown Customer';
    $barangay = $application->address?->barangay?->b_desc ?? 'Unknown';

    $this->notifyByRole(
        Notification::TYPE_APPLICATION_SUBMITTED,
        'New Application Submitted',
        "{$customerName} submitted a service application for {$barangay}",
        route('service.application.show', $application->application_id),
        'ServiceApplication',
        $application->application_id,
        $actingUserId
    );
}
```

Replace `notifyApplicationVerified` (lines 58-71):

```php
/**
 * Notify about application verification
 */
public function notifyApplicationVerified(ServiceApplication $application, ?int $actingUserId = null): void
{
    $customerName = $application->customer?->fullName ?? 'Unknown Customer';

    $this->notifyByRole(
        Notification::TYPE_APPLICATION_VERIFIED,
        'Application Verified',
        "Application #{$application->application_number} for {$customerName} is verified and awaiting payment",
        route('service.application.show', $application->application_id),
        'ServiceApplication',
        $application->application_id,
        $actingUserId
    );
}
```

Replace `notifyApplicationPaid` (lines 76-89):

```php
/**
 * Notify about application payment received
 */
public function notifyApplicationPaid(ServiceApplication $application, string $amount, ?int $actingUserId = null): void
{
    $this->notifyByRole(
        Notification::TYPE_APPLICATION_PAID,
        'Application Payment Received',
        "Payment of {$amount} received for application #{$application->application_number}",
        route('service.application.show', $application->application_id),
        'ServiceApplication',
        $application->application_id,
        $actingUserId
    );
}
```

Replace `notifyApplicationScheduled` (lines 94-108):

```php
/**
 * Notify about connection scheduled
 */
public function notifyApplicationScheduled(ServiceApplication $application, ?int $actingUserId = null): void
{
    $customerName = $application->customer?->fullName ?? 'Unknown Customer';
    $scheduledDate = $application->scheduled_connection_date?->format('M d, Y') ?? 'TBD';

    $this->notifyByRole(
        Notification::TYPE_APPLICATION_SCHEDULED,
        'Connection Scheduled',
        "Application #{$application->application_number} scheduled for connection on {$scheduledDate}",
        route('service.application.show', $application->application_id),
        'ServiceApplication',
        $application->application_id,
        $actingUserId
    );
}
```

Replace `notifyApplicationConnected` (lines 113-126):

```php
/**
 * Notify about connection completed
 */
public function notifyApplicationConnected(ServiceApplication $application, ServiceConnection $connection, ?int $actingUserId = null): void
{
    $customerName = $application->customer?->fullName ?? 'Unknown Customer';

    $this->notifyByRole(
        Notification::TYPE_APPLICATION_CONNECTED,
        'Connection Completed',
        "{$customerName} is now connected — Account #{$connection->account_no}",
        route('service.connection.show', $connection->connection_id),
        'ServiceConnection',
        $connection->connection_id,
        $actingUserId
    );
}
```

Replace `notifyApplicationRejected` (lines 131-144):

```php
/**
 * Notify about application rejection
 */
public function notifyApplicationRejected(ServiceApplication $application, string $reason, ?int $actingUserId = null): void
{
    $this->notifyByRole(
        Notification::TYPE_APPLICATION_REJECTED,
        'Application Rejected',
        "Application #{$application->application_number} was rejected: {$reason}",
        route('service.application.show', $application->application_id),
        'ServiceApplication',
        $application->application_id,
        $actingUserId
    );
}
```

Replace `notifyConnectionSuspended` (lines 149-162):

```php
/**
 * Notify about connection suspension
 */
public function notifyConnectionSuspended(ServiceConnection $connection, ?int $actingUserId = null): void
{
    $customerName = $connection->customer?->fullName ?? 'Unknown Customer';

    $this->notifyByRole(
        Notification::TYPE_CONNECTION_SUSPENDED,
        'Connection Suspended',
        "Account #{$connection->account_no} ({$customerName}) has been suspended",
        route('service.connection.show', $connection->connection_id),
        'ServiceConnection',
        $connection->connection_id,
        $actingUserId
    );
}
```

**Step 4: Add new notification methods for disconnected, reconnected, payments, billing, penalties, users**

Add these new methods after the existing ones (before `getUserNotifications`):

```php
/**
 * Notify about connection disconnection
 */
public function notifyConnectionDisconnected(ServiceConnection $connection, ?int $actingUserId = null): void
{
    $customerName = $connection->customer?->fullName ?? 'Unknown Customer';

    $this->notifyByRole(
        Notification::TYPE_CONNECTION_DISCONNECTED,
        'Connection Disconnected',
        "Account #{$connection->account_no} ({$customerName}) has been disconnected",
        route('service.connection.show', $connection->connection_id),
        'ServiceConnection',
        $connection->connection_id,
        $actingUserId
    );
}

/**
 * Notify about connection reconnection
 */
public function notifyConnectionReconnected(ServiceConnection $connection, ?int $actingUserId = null): void
{
    $customerName = $connection->customer?->fullName ?? 'Unknown Customer';

    $this->notifyByRole(
        Notification::TYPE_CONNECTION_RECONNECTED,
        'Connection Reconnected',
        "Account #{$connection->account_no} ({$customerName}) has been reconnected",
        route('service.connection.show', $connection->connection_id),
        'ServiceConnection',
        $connection->connection_id,
        $actingUserId
    );
}

/**
 * Notify about water bill payment processed
 */
public function notifyPaymentProcessed(string $customerName, string $amount, string $receiptNo, ?int $actingUserId = null): void
{
    $this->notifyByRole(
        Notification::TYPE_PAYMENT_PROCESSED,
        'Payment Processed',
        "Payment of {$amount} received from {$customerName} — Receipt #{$receiptNo}",
        route('customer.payment-management'),
        null,
        null,
        $actingUserId
    );
}

/**
 * Notify about payment cancellation
 */
public function notifyPaymentCancelled(string $receiptNo, string $amount, string $cancelledByName, ?int $actingUserId = null): void
{
    $this->notifyByRole(
        Notification::TYPE_PAYMENT_CANCELLED,
        'Payment Cancelled',
        "Payment #{$receiptNo} of {$amount} has been cancelled by {$cancelledByName}",
        route('customer.payment-management'),
        null,
        null,
        $actingUserId
    );
}

/**
 * Notify about bills generated for a period
 */
public function notifyBillsGenerated(int $count, string $periodName, ?int $actingUserId = null): void
{
    $this->notifyByRole(
        Notification::TYPE_BILLS_GENERATED,
        'Bills Generated',
        "{$count} water bill(s) generated for period {$periodName}",
        null,
        null,
        null,
        $actingUserId
    );
}

/**
 * Notify about penalties processed
 */
public function notifyPenaltiesProcessed(int $count, ?int $actingUserId = null): void
{
    $this->notifyByRole(
        Notification::TYPE_PENALTY_PROCESSED,
        'Penalties Applied',
        "{$count} overdue penalty/ies applied",
        null,
        null,
        null,
        $actingUserId
    );
}

/**
 * Notify about new user created
 */
public function notifyUserCreated(string $username, string $roleName, ?int $actingUserId = null): void
{
    $displayRole = ucwords(str_replace('_', ' ', $roleName));

    $this->notifyByRole(
        Notification::TYPE_USER_CREATED,
        'New User Created',
        "User {$username} ({$displayRole}) has been added to the system",
        null,
        null,
        null,
        $actingUserId
    );
}
```

**Step 5: Update getUserNotifications to support filtering and pagination**

Replace the existing `getUserNotifications` method (lines 167-176):

```php
/**
 * Get notifications for a user with optional filtering
 */
public function getUserNotifications(
    int $userId,
    ?string $filter = null,
    ?string $category = null,
    ?string $search = null,
    int $perPage = 15
): \Illuminate\Contracts\Pagination\LengthAwarePaginator {
    $query = Notification::where('user_id', $userId)
        ->orderBy('created_at', 'desc');

    // Filter by read status
    if ($filter === 'unread') {
        $query->unread();
    } elseif ($filter === 'read') {
        $query->read();
    }

    // Filter by category
    if ($category) {
        $categoryTypes = $this->getTypesForCategory($category);
        $query->whereIn('type', $categoryTypes);
    }

    // Search in title and message
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('message', 'like', "%{$search}%");
        });
    }

    return $query->paginate($perPage);
}

/**
 * Get recent notifications for dropdown (not paginated)
 */
public function getRecentNotifications(int $userId, int $limit = 5): \Illuminate\Support\Collection
{
    return Notification::where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->limit($limit)
        ->get();
}

/**
 * Get notification type constants for a category
 */
protected function getTypesForCategory(string $category): array
{
    return match ($category) {
        'applications' => [
            Notification::TYPE_APPLICATION_SUBMITTED,
            Notification::TYPE_APPLICATION_VERIFIED,
            Notification::TYPE_APPLICATION_SCHEDULED,
            Notification::TYPE_APPLICATION_CONNECTED,
            Notification::TYPE_APPLICATION_REJECTED,
            Notification::TYPE_APPLICATION_CANCELLED,
        ],
        'payments' => [
            Notification::TYPE_APPLICATION_PAID,
            Notification::TYPE_PAYMENT_PROCESSED,
            Notification::TYPE_PAYMENT_CANCELLED,
        ],
        'connections' => [
            Notification::TYPE_CONNECTION_SUSPENDED,
            Notification::TYPE_CONNECTION_DISCONNECTED,
            Notification::TYPE_CONNECTION_RECONNECTED,
        ],
        'billing' => [
            Notification::TYPE_BILLS_GENERATED,
            Notification::TYPE_PENALTY_PROCESSED,
        ],
        'system' => [
            Notification::TYPE_USER_CREATED,
        ],
        default => [],
    };
}

/**
 * Get notification counts by category for a user
 */
public function getCategoryCounts(int $userId): array
{
    $base = Notification::where('user_id', $userId)->unread();

    return [
        'unread' => (clone $base)->count(),
        'applications' => (clone $base)->whereIn('type', $this->getTypesForCategory('applications'))->count(),
        'payments' => (clone $base)->whereIn('type', $this->getTypesForCategory('payments'))->count(),
        'connections' => (clone $base)->whereIn('type', $this->getTypesForCategory('connections'))->count(),
    ];
}
```

**Step 6: Update getUnreadCount to only check user-targeted notifications**

Replace the existing `getUnreadCount` method (lines 181-189):

```php
/**
 * Get unread notifications count for a user
 */
public function getUnreadCount(int $userId): int
{
    return Notification::where('user_id', $userId)
        ->unread()
        ->count();
}
```

**Step 7: Update markAllAsRead to only affect user-targeted notifications**

Replace the existing `markAllAsRead` method (lines 205-213):

```php
/**
 * Mark all notifications as read for a user
 */
public function markAllAsRead(int $userId): int
{
    return Notification::where('user_id', $userId)
        ->unread()
        ->update(['read_at' => now()]);
}
```

**Step 8: Update cleanupOldNotifications to use 90 days**

Replace the existing `cleanupOldNotifications` method (lines 218-223):

```php
/**
 * Delete old notifications (cleanup) — removes read notifications older than 90 days
 */
public function cleanupOldNotifications(int $daysOld = 90): int
{
    return Notification::where('created_at', '<', now()->subDays($daysOld))
        ->delete();
}
```

**Step 9: Commit**

```bash
git add app/Services/Notification/NotificationService.php
git commit -m "feat(notifications): refactor service for role-based targeting with filtering and pagination"
```

---

## Task 4: Update NotificationController — Filtering, Pagination, and Page Route

The current controller returns a flat JSON array. We need filtering, pagination, a dropdown endpoint, and a web view route.

**Files:**
- Modify: `app/Http/Controllers/Notification/NotificationController.php`
- Modify: `routes/web.php` (lines 96-101)

**Step 1: Rewrite the NotificationController**

Replace the entire file content:

```php
<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\Notification\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Show the notifications page
     */
    public function page()
    {
        return view('notifications.index');
    }

    /**
     * Get paginated notifications for authenticated user (full page API)
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $this->notificationService->getUserNotifications(
            Auth::id(),
            $request->input('filter'),
            $request->input('category'),
            $request->input('search'),
            (int) $request->input('per_page', 15)
        );

        // Add category color to each notification
        $notifications->getCollection()->transform(function ($notification) {
            $notification->category_color = Notification::getCategoryColor($notification->type);
            $notification->time_ago = $notification->created_at->diffForHumans();

            return $notification;
        });

        return response()->json([
            'success' => true,
            'data' => $notifications->items(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
            ],
        ]);
    }

    /**
     * Get recent notifications for dropdown (5 most recent)
     */
    public function recent(): JsonResponse
    {
        $notifications = $this->notificationService->getRecentNotifications(Auth::id(), 5);

        $notifications->transform(function ($notification) {
            $notification->category_color = Notification::getCategoryColor($notification->type);
            $notification->time_ago = $notification->created_at->diffForHumans();

            return $notification;
        });

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
    }

    /**
     * Get unread count for authenticated user
     */
    public function unreadCount(): JsonResponse
    {
        $count = $this->notificationService->getUnreadCount(Auth::id());

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    /**
     * Get category counts for stats cards
     */
    public function categoryCounts(): JsonResponse
    {
        $counts = $this->notificationService->getCategoryCounts(Auth::id());

        return response()->json([
            'success' => true,
            'data' => $counts,
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(int $id): JsonResponse
    {
        $notification = $this->notificationService->markAsRead($id);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
            'data' => $notification,
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        $count = $this->notificationService->markAllAsRead(Auth::id());

        return response()->json([
            'success' => true,
            'message' => "{$count} notifications marked as read",
        ]);
    }
}
```

**Step 2: Update routes in `routes/web.php`**

Replace the notification routes block (lines 93-101):

```php
// -------------------------------------------------------------------------
// Notifications - All authenticated users
// -------------------------------------------------------------------------
Route::get('/notifications', [NotificationController::class, 'page'])->name('notifications.index');

Route::prefix('api/notifications')->name('api.notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/recent', [NotificationController::class, 'recent'])->name('recent');
    Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
    Route::get('/category-counts', [NotificationController::class, 'categoryCounts'])->name('category-counts');
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
});
```

**Step 3: Commit**

```bash
git add app/Http/Controllers/Notification/NotificationController.php routes/web.php
git commit -m "feat(notifications): add filtering, pagination, dropdown endpoint, and page route"
```

---

## Task 5: Wire Notifications into ServiceApplicationService

Add `NotificationService` calls to every workflow method in the application service.

**Files:**
- Modify: `app/Services/ServiceApplication/ServiceApplicationService.php`

**Step 1: Add NotificationService dependency**

Add import at the top (after line 14, `use Carbon\Carbon;`):

```php
use App\Services\Notification\NotificationService;
```

Update the constructor (lines 20-24) to inject NotificationService:

```php
public function __construct(
    protected ApplicationChargeService $chargeService,
    protected LedgerService $ledgerService,
    protected PaymentService $paymentService,
    protected NotificationService $notificationService
) {}
```

**Step 2: Add notification call to createApplication**

In `createApplication()`, add after line 145 (after the return array is built but inside the transaction — right before the final `];` of the return):

Actually, since `createApplication` auto-verifies, add the notification call after the return statement of the DB::transaction. Change the method to capture the result and notify:

Replace lines 39-147 (the `return DB::transaction(function () ...` block) with this structure — only adding lines around the existing return:

At line 37, change the method body to:

```php
public function createApplication(string $customerType, array $customerData, array $applicationData, ?int $userId = null): array
{
    $result = DB::transaction(function () use ($customerType, $customerData, $applicationData, $userId) {
        // ... (ALL existing transaction code stays exactly the same) ...
    });

    // Notify after successful creation (auto-verified workflow)
    $this->notificationService->notifyApplicationSubmitted($result['application'], $userId);

    return $result;
}
```

The key change: wrap the existing `DB::transaction()` result in `$result`, add notification after it, then return `$result`.

**Step 3: Add notification to verifyApplication**

In `verifyApplication()` (line 182), after the DB::transaction returns, add notification. Change the method to:

```php
public function verifyApplication(int $applicationId, int $verifiedBy): array
{
    $application = ServiceApplication::findOrFail($applicationId);

    if ($application->stat_id !== Status::getIdByDescription(Status::PENDING)) {
        throw new \Exception('Only PENDING applications can be verified');
    }

    $result = DB::transaction(function () use ($application, $verifiedBy) {
        $application->update([
            'stat_id' => Status::getIdByDescription(Status::VERIFIED),
            'verified_at' => now(),
            'verified_by' => $verifiedBy,
        ]);

        $charges = $this->chargeService->generateApplicationCharges($application);
        $ledgerEntries = $this->ledgerService->recordCharges($charges, $verifiedBy);

        return [
            'application' => $application->fresh(),
            'charges' => $charges,
            'ledger_entries' => $ledgerEntries,
        ];
    });

    $this->notificationService->notifyApplicationVerified($result['application'], $verifiedBy);

    return $result;
}
```

**Step 4: Add notification to scheduleConnection**

Add after line 248 (`return $application->fresh();`):

```php
public function scheduleConnection(int $applicationId, Carbon $scheduledDate, int $scheduledBy): ServiceApplication
{
    $application = ServiceApplication::findOrFail($applicationId);

    if ($application->stat_id !== Status::getIdByDescription(Status::PAID)) {
        throw new \Exception('Only PAID applications can be scheduled');
    }

    $application->update([
        'stat_id' => Status::getIdByDescription(Status::SCHEDULED),
        'scheduled_at' => now(),
        'scheduled_connection_date' => $scheduledDate,
        'scheduled_by' => $scheduledBy,
    ]);

    $application = $application->fresh('customer');

    $this->notificationService->notifyApplicationScheduled($application, $scheduledBy);

    return $application;
}
```

**Step 5: Add notification to rejectApplication**

```php
public function rejectApplication(int $applicationId, string $reason, int $rejectedBy): ServiceApplication
{
    $application = ServiceApplication::findOrFail($applicationId);

    $allowedStatuses = [
        Status::getIdByDescription(Status::PENDING),
        Status::getIdByDescription(Status::VERIFIED),
    ];

    if (! in_array($application->stat_id, $allowedStatuses)) {
        throw new \Exception('Cannot reject application in current status');
    }

    $application->update([
        'stat_id' => Status::getIdByDescription(Status::REJECTED),
        'rejected_at' => now(),
        'rejected_by' => $rejectedBy,
        'rejection_reason' => $reason,
    ]);

    $application = $application->fresh('customer');

    $this->notificationService->notifyApplicationRejected($application, $reason, $rejectedBy);

    return $application;
}
```

**Step 6: Add notification to cancelApplication**

```php
public function cancelApplication(int $applicationId, string $reason): ServiceApplication
{
    $application = ServiceApplication::findOrFail($applicationId);

    $connectedStatusId = Status::getIdByDescription(Status::CONNECTED);

    if ($application->stat_id === $connectedStatusId) {
        throw new \Exception('Cannot cancel connected application');
    }

    $application->update([
        'stat_id' => Status::getIdByDescription(Status::CANCELLED),
        'cancelled_at' => now(),
        'cancellation_reason' => $reason,
    ]);

    return $application->fresh();
}
```

Note: `cancelApplication` has no `$userId` param, so we don't exclude anyone. The notification goes to all admins. If you want to exclude the acting user, the controller should pass the userId. For now keep it simple — no exclusion on cancel.

**Step 7: Commit**

```bash
git add app/Services/ServiceApplication/ServiceApplicationService.php
git commit -m "feat(notifications): wire notification triggers into ServiceApplicationService"
```

---

## Task 6: Wire Notifications into ServiceConnectionService

**Files:**
- Modify: `app/Services/ServiceConnection/ServiceConnectionService.php`

**Step 1: Add NotificationService dependency**

Add import after line 14 (`use Illuminate\Support\Facades\Log;`):

```php
use App\Services\Notification\NotificationService;
```

Update constructor (lines 20-22):

```php
public function __construct(
    protected ServiceApplicationService $applicationService,
    protected NotificationService $notificationService
) {}
```

**Step 2: Add notification to createFromApplication**

In `createFromApplication()`, after line 133 (`$this->applicationService->markAsConnected(...)`) and before `return $connection;` (line 135), add:

```php
// Notify about completed connection
$application->load('customer');
$this->notificationService->notifyApplicationConnected($application, $connection);
```

**Step 3: Add notification to suspendConnection**

After line 200 (`return $connection->fresh();`), change the method to load customer and notify:

```php
public function suspendConnection(
    int $connectionId,
    string $reason,
    int $suspendedBy
): ServiceConnection {
    $connection = ServiceConnection::findOrFail($connectionId);

    if ($connection->stat_id !== Status::getIdByDescription(Status::ACTIVE)) {
        throw new \Exception('Only ACTIVE connections can be suspended');
    }

    $connection->update([
        'stat_id' => Status::getIdByDescription(Status::SUSPENDED),
    ]);

    $connection = $connection->fresh('customer');

    $this->notificationService->notifyConnectionSuspended($connection, $suspendedBy);

    return $connection;
}
```

**Step 4: Add notification to disconnectConnection**

```php
public function disconnectConnection(
    int $connectionId,
    string $reason,
    int $disconnectedBy
): ServiceConnection {
    $connection = ServiceConnection::findOrFail($connectionId);

    $allowedStatuses = [
        Status::getIdByDescription(Status::ACTIVE),
        Status::getIdByDescription(Status::SUSPENDED),
    ];

    if (! in_array($connection->stat_id, $allowedStatuses)) {
        throw new \Exception('Connection must be ACTIVE or SUSPENDED to disconnect');
    }

    $connection->update([
        'stat_id' => Status::getIdByDescription(Status::DISCONNECTED),
        'ended_at' => now(),
    ]);

    $connection = $connection->fresh('customer');

    $this->notificationService->notifyConnectionDisconnected($connection, $disconnectedBy);

    return $connection;
}
```

**Step 5: Add notification to reconnectConnection**

```php
public function reconnectConnection(int $connectionId, int $reconnectedBy): ServiceConnection
{
    $connection = ServiceConnection::findOrFail($connectionId);

    if ($connection->stat_id !== Status::getIdByDescription(Status::SUSPENDED)) {
        throw new \Exception('Only SUSPENDED connections can be reconnected');
    }

    $connection->update([
        'stat_id' => Status::getIdByDescription(Status::ACTIVE),
    ]);

    $connection = $connection->fresh('customer');

    $this->notificationService->notifyConnectionReconnected($connection, $reconnectedBy);

    return $connection;
}
```

**Step 6: Commit**

```bash
git add app/Services/ServiceConnection/ServiceConnectionService.php
git commit -m "feat(notifications): wire notification triggers into ServiceConnectionService"
```

---

## Task 7: Wire Notifications into PaymentService

**Files:**
- Modify: `app/Services/Payment/PaymentService.php`

**Step 1: Add NotificationService dependency**

Add import after line 15 (`use Illuminate\Support\Facades\DB;`):

```php
use App\Services\Notification\NotificationService;
```

Update constructor (lines 19-22):

```php
public function __construct(
    protected ApplicationChargeService $chargeService,
    protected LedgerService $ledgerService,
    protected NotificationService $notificationService
) {}
```

**Step 2: Add notification to processApplicationPayment**

After the DB::transaction block in `processApplicationPayment()` (line 151), the method currently returns inside the transaction. Change it to capture the result and notify:

After line 151 (end of the `DB::transaction` closure), add before the end of the method:

The current code returns directly from the transaction. Change the `return DB::transaction(function () ...` to `$result = DB::transaction(function () ...` and add notification + return after:

```php
// After the transaction closure ends (after line 151):
$application->load('customer');
$totalPaidFormatted = '₱' . number_format($result['total_paid'], 2);
$this->notificationService->notifyApplicationPaid($application, $totalPaidFormatted, $userId);

return $result;
```

**Step 3: Add notification to processWaterBillPayment**

Similar pattern — after the `DB::transaction` in `processWaterBillPayment()` (line 491). Capture result, notify, return:

After the transaction closure, add:

```php
// Notify about payment
$customerName = $customer->fullName ?? 'Unknown Customer';
$amountFormatted = '₱' . number_format($result['total_paid'], 2);
$this->notificationService->notifyPaymentProcessed($customerName, $amountFormatted, $result['payment']->receipt_no, $userId);

return $result;
```

Note: `$customer` is defined inside the transaction scope. You'll need to capture it. Add `$customerForNotification = null;` before the transaction, set `$customerForNotification = $customer;` inside, and use it outside.

**Step 4: Add notification to processConnectionPayment**

Same pattern as processWaterBillPayment. After the transaction closure in `processConnectionPayment()` (line 661), add:

```php
$customerName = $customer->fullName ?? 'Unknown Customer';
$amountFormatted = '₱' . number_format($result['total_paid'], 2);
$this->notificationService->notifyPaymentProcessed($customerName, $amountFormatted, $result['payment']->receipt_no, $userId);

return $result;
```

Again, capture `$customer` variable outside the transaction scope.

**Step 5: Add notification to cancelPayment**

In `cancelPayment()` (line 680), after the transaction (line 775), add:

```php
$cancelledByUser = User::find($userId);
$cancelledByName = $cancelledByUser?->name ?? 'Unknown';
$amountFormatted = '₱' . number_format($payment->amount_received, 2);
$this->notificationService->notifyPaymentCancelled($payment->receipt_no, $amountFormatted, $cancelledByName, $userId);

return $result;
```

Add `use App\Models\User;` at the top if not already imported.

**Step 6: Commit**

```bash
git add app/Services/Payment/PaymentService.php
git commit -m "feat(notifications): wire notification triggers into PaymentService"
```

---

## Task 8: Wire Notifications into PenaltyService and UserService

**Files:**
- Modify: `app/Services/Billing/PenaltyService.php`
- Modify: `app/Services/Users/UserService.php`

**Step 1: Add NotificationService to PenaltyService**

Add import after line 12 (`use Illuminate\Support\Facades\Log;`):

```php
use App\Services\Notification\NotificationService;
```

Update constructor (lines 18-20):

```php
public function __construct(
    private LedgerService $ledgerService,
    private NotificationService $notificationService
) {}
```

**Step 2: Add notification to processAllOverdueBills**

In `processAllOverdueBills()` (line 146), after the foreach loop completes and before the return (line 169), add:

```php
if ($processed > 0) {
    $this->notificationService->notifyPenaltiesProcessed($processed, $userId);
}
```

**Step 3: Add NotificationService to UserService**

Add import after line 8 (`use App\Services\FileUploadService;`):

```php
use App\Services\Notification\NotificationService;
```

Update constructor (lines 15-17):

```php
public function __construct(
    protected FileUploadService $fileUploadService,
    protected NotificationService $notificationService
) {}
```

**Step 4: Add notification to createUser**

In `createUser()` (line 68), after line 93 (`return $user->load('roles', 'status');`), change to:

```php
$user = $user->load('roles', 'status');
$roleName = $user->roles->first()?->role_name ?? 'unknown';

$this->notificationService->notifyUserCreated($user->username, $roleName, auth()->id());

return $user;
```

**Step 5: Commit**

```bash
git add app/Services/Billing/PenaltyService.php app/Services/Users/UserService.php
git commit -m "feat(notifications): wire notification triggers into PenaltyService and UserService"
```

---

## Task 9: Wire Notifications into WaterBillService (Bill Generation)

**Files:**
- Modify: `app/Services/Billing/WaterBillService.php`

**Step 1: Add NotificationService dependency**

Add import after line 18 (`use Illuminate\Support\Facades\Log;`):

```php
use App\Services\Notification\NotificationService;
```

Add constructor. The class currently has no constructor. Add one at the top of the class (after line 21, the opening `{`):

```php
public function __construct(
    protected NotificationService $notificationService
) {}
```

**Step 2: Add notification to generateBill**

In `generateBill()`, after a successful bill creation (inside the try block, after `DB::commit()` and before the success return), add:

Find the success return in generateBill (look for `'success' => true` after the bill creation). Add the notification call before that return:

```php
// Notify about bill generation
$periodName = $period->per_name ?? 'Unknown Period';
$this->notificationService->notifyBillsGenerated(1, $periodName, Auth::id());
```

Note: For single bill generation, count is 1. For batch processing via `processUploadedReadings`, we can add a separate notification at the batch level.

**Step 3: Commit**

```bash
git add app/Services/Billing/WaterBillService.php
git commit -m "feat(notifications): wire notification trigger into WaterBillService"
```

---

## Task 10: Build Header Dropdown — Alpine.js Component

Replace the hardcoded notification dropdown in `navigation.blade.php` with a live Alpine.js component.

**Files:**
- Modify: `resources/views/layouts/navigation.blade.php` (lines 194-242)

**Step 1: Replace the notification dropdown HTML**

Replace lines 194-242 in `navigation.blade.php` with:

```blade
<!-- Notifications -->
<div class="relative" x-data="notificationDropdown()" x-init="init()">
    <button @click="toggleDropdown()" class="relative p-2.5 rounded-lg bg-gray-100 dark:bg-[#111826] border border-gray-200 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200 text-gray-600 dark:text-gray-300">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <span x-show="unreadCount > 0" x-text="unreadCount > 99 ? '99+' : unreadCount" x-cloak
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center border-2 border-white dark:border-gray-800 font-medium"></span>
    </button>

    <!-- Dropdown -->
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="open = false"
         class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 overflow-hidden">

        <!-- Header -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">Notifications</h3>
            <button x-show="unreadCount > 0" @click.stop="markAllRead()" class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                Mark all as read
            </button>
        </div>

        <!-- List -->
        <div class="max-h-80 overflow-y-auto">
            <template x-if="loading">
                <div class="p-6 text-center">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 mx-auto"></div>
                </div>
            </template>

            <template x-if="!loading && notifications.length === 0">
                <div class="p-6 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">No notifications yet</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <a :href="notification.link || '#'" @click="markRead(notification)"
                   class="block p-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition duration-150"
                   :class="{ 'bg-blue-50/50 dark:bg-blue-900/10': !notification.read_at }">
                    <div class="flex items-start gap-3">
                        <span class="mt-1.5 h-2.5 w-2.5 rounded-full flex-shrink-0"
                              :class="{
                                  'bg-blue-500': notification.category_color === 'blue',
                                  'bg-green-500': notification.category_color === 'green',
                                  'bg-red-500': notification.category_color === 'red',
                                  'bg-amber-500': notification.category_color === 'amber',
                                  'bg-indigo-500': notification.category_color === 'indigo',
                                  'bg-gray-400': notification.category_color === 'gray'
                              }"></span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200" x-text="notification.title"></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2" x-text="notification.message"></p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1" x-text="notification.time_ago"></p>
                        </div>
                    </div>
                </a>
            </template>
        </div>

        <!-- Footer -->
        <div class="p-3 border-t border-gray-200 dark:border-gray-700 text-center">
            <a href="{{ route('notifications.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                View all notifications
            </a>
        </div>
    </div>
</div>
```

**Step 2: Add the Alpine.js component**

Create `resources/js/data/notifications/notification-dropdown.js`:

**UX behavior:** Opening the dropdown automatically marks all notifications as read and clears the badge. This prevents the count from being persistently annoying — once the user has "seen" their notifications by opening the dropdown, the badge clears. New notifications arriving later will show the badge again.

```javascript
function notificationDropdown() {
    return {
        open: false,
        loading: false,
        notifications: [],
        unreadCount: 0,

        init() {
            this.fetchUnreadCount();
        },

        async fetchUnreadCount() {
            try {
                const res = await fetch('/api/notifications/unread-count');
                const data = await res.json();
                if (data.success) {
                    this.unreadCount = data.count;
                }
            } catch (e) {
                console.error('Failed to fetch unread count:', e);
            }
        },

        async toggleDropdown() {
            this.open = !this.open;
            if (this.open) {
                await this.fetchRecent();
                // Auto-mark all as read when dropdown is opened (clear badge)
                if (this.unreadCount > 0) {
                    this.markAllRead();
                }
            }
        },

        async fetchRecent() {
            this.loading = true;
            try {
                const res = await fetch('/api/notifications/recent');
                const data = await res.json();
                if (data.success) {
                    this.notifications = data.data;
                }
            } catch (e) {
                console.error('Failed to fetch notifications:', e);
            } finally {
                this.loading = false;
            }
        },

        async markRead(notification) {
            if (!notification.read_at) {
                try {
                    await fetch(`/api/notifications/${notification.id}/read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
                    notification.read_at = new Date().toISOString();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                } catch (e) {
                    console.error('Failed to mark as read:', e);
                }
            }
        },

        async markAllRead() {
            try {
                await fetch('/api/notifications/read-all', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
                this.notifications.forEach(n => n.read_at = new Date().toISOString());
                this.unreadCount = 0;
            } catch (e) {
                console.error('Failed to mark all as read:', e);
            }
        }
    };
}
```

**Step 3: Register the component globally**

Add the script to the layout. In `resources/views/layouts/app.blade.php`, find where JS files are loaded and add:

```blade
<script src="{{ asset('js/data/notifications/notification-dropdown.js') }}"></script>
```

Or, if using Vite, import it in the main `app.js` entry point. Check how existing Alpine data files are loaded and follow the same pattern.

**Step 4: Commit**

```bash
git add resources/views/layouts/navigation.blade.php resources/js/data/notifications/notification-dropdown.js
git commit -m "feat(notifications): build live header dropdown with Alpine.js"
```

---

## Task 11: Build Full Notifications Page

Create the notifications index page with stats cards, filters, and notification list.

**Files:**
- Create: `resources/views/notifications/index.blade.php`

**Step 1: Create the notifications page view**

Create `resources/views/notifications/index.blade.php` with the full page layout. Follow the existing page pattern from the app (app-layout, page header, stats cards, filter bar, list, pagination).

The page should include:
- Page header with title "Notifications" and "Mark all as read" button
- 4 stats cards (Unread, Applications, Payments, Connections) that double as category filters
- Filter tabs (All, Unread, Read) and search input
- Notification list with colored dots, titles, messages, timestamps, read/unread styling
- Empty state message
- Pagination (prev/next)
- All powered by `notificationManager()` Alpine.js component

**Step 2: Create the Alpine.js page component**

Create `resources/js/data/notifications/notification-manager.js`:

```javascript
function notificationManager() {
    return {
        loading: false,
        notifications: [],
        counts: { unread: 0, applications: 0, payments: 0, connections: 0 },
        filter: null,
        category: null,
        search: '',
        currentPage: 1,
        lastPage: 1,
        total: 0,
        searchTimeout: null,

        init() {
            this.fetchNotifications();
            this.fetchCounts();
        },

        async fetchNotifications() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                params.set('page', this.currentPage);
                if (this.filter) params.set('filter', this.filter);
                if (this.category) params.set('category', this.category);
                if (this.search) params.set('search', this.search);

                const res = await fetch(`/api/notifications/?${params}`);
                const data = await res.json();
                if (data.success) {
                    this.notifications = data.data;
                    this.currentPage = data.meta.current_page;
                    this.lastPage = data.meta.last_page;
                    this.total = data.meta.total;
                }
            } catch (e) {
                console.error('Failed to fetch notifications:', e);
            } finally {
                this.loading = false;
            }
        },

        async fetchCounts() {
            try {
                const res = await fetch('/api/notifications/category-counts');
                const data = await res.json();
                if (data.success) {
                    this.counts = data.data;
                }
            } catch (e) {
                console.error('Failed to fetch counts:', e);
            }
        },

        setFilter(value) {
            this.filter = this.filter === value ? null : value;
            this.currentPage = 1;
            this.fetchNotifications();
        },

        setCategory(value) {
            this.category = this.category === value ? null : value;
            this.currentPage = 1;
            this.fetchNotifications();
        },

        onSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.currentPage = 1;
                this.fetchNotifications();
            }, 300);
        },

        goToPage(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.currentPage = page;
                this.fetchNotifications();
            }
        },

        async markRead(notification) {
            if (!notification.read_at) {
                try {
                    await fetch(`/api/notifications/${notification.id}/read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
                    notification.read_at = new Date().toISOString();
                    this.counts.unread = Math.max(0, this.counts.unread - 1);
                } catch (e) {
                    console.error('Failed to mark as read:', e);
                }
            }
            if (notification.link) {
                window.location.href = notification.link;
            }
        },

        async markAllRead() {
            try {
                await fetch('/api/notifications/read-all', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
                this.notifications.forEach(n => n.read_at = new Date().toISOString());
                this.counts.unread = 0;
                this.fetchNotifications();
            } catch (e) {
                console.error('Failed to mark all as read:', e);
            }
        },

        getColorClass(color) {
            const map = {
                blue: 'bg-blue-500',
                green: 'bg-green-500',
                red: 'bg-red-500',
                amber: 'bg-amber-500',
                indigo: 'bg-indigo-500',
                gray: 'bg-gray-400'
            };
            return map[color] || map.gray;
        }
    };
}
```

**Step 3: Register the JS file** — same pattern as step 3 in Task 10.

**Step 4: Commit**

```bash
git add resources/views/notifications/index.blade.php resources/js/data/notifications/notification-manager.js
git commit -m "feat(notifications): build full notifications page with filtering and pagination"
```

---

## Task 12: Schedule Notification Cleanup Command

**Files:**
- Modify: `routes/console.php` (Laravel 12 uses `routes/console.php` for scheduling, not `app/Console/Kernel.php`)

**Step 1: Check current scheduling setup**

Read `routes/console.php` to see existing scheduled commands and follow the pattern.

**Step 2: Add the cleanup schedule**

Add to `routes/console.php`:

```php
use Illuminate\Support\Facades\Schedule;
use App\Services\Notification\NotificationService;

Schedule::call(function () {
    app(NotificationService::class)->cleanupOldNotifications(90);
})->daily()->description('Clean up old notifications');
```

**Step 3: Commit**

```bash
git add routes/console.php
git commit -m "feat(notifications): schedule daily cleanup of old notifications"
```

---

## Task 13: Manual Testing & Verification

Verify the entire notification flow works end-to-end.

**Step 1: Run migrations and verify table exists**

```bash
php artisan migrate:status | grep notification
```

Expected: The `create_notifications_table` migration should show as "Ran".

**Step 2: Start the dev server**

```bash
composer dev
```

**Step 3: Test backend triggers manually**

Use tinker to simulate a notification:

```bash
php artisan tinker
```

```php
$service = app(\App\Services\Notification\NotificationService::class);
$service->notifyBillsGenerated(5, 'January 2026', null);
// Check that notifications were created for admin/super-admin/cashier users
\App\Models\Notification::latest()->take(5)->get(['id', 'user_id', 'type', 'title']);
```

**Step 4: Test the dropdown**

- Navigate to any page in the app
- Verify the bell icon shows the correct unread count (not hardcoded "3")
- Click the bell — verify it loads real notifications via API
- Click a notification — verify it marks as read and navigates to the link
- Click "Mark all as read" — verify all notifications become read

**Step 5: Test the full notifications page**

- Navigate to `/notifications`
- Verify stats cards show correct counts
- Click category cards — verify list filters
- Use tab pills (All/Unread/Read) — verify filtering
- Type in search box — verify search works with debounce
- Test pagination if enough notifications exist

**Step 6: Test workflow triggers**

Test each workflow by performing the action in the UI and checking that notifications appear:

1. Create a service application → check for `application_submitted` notification
2. Verify an application → check for `application_verified` notification
3. Process application payment → check for `application_paid` notification
4. Schedule a connection → check for `application_scheduled` notification
5. Complete a connection → check for `application_connected` notification
6. Suspend a connection → check for `connection_suspended` notification
7. Process a water bill payment → check for `payment_processed` notification
8. Cancel a payment → check for `payment_cancelled` notification
9. Create a user → check for `user_created` notification

**Step 7: Final commit**

```bash
git add -A
git commit -m "feat(notifications): complete notification system implementation"
```
