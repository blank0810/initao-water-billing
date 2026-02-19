# Notifications Feature Design

**Date:** 2026-02-16
**Branch:** `notifications-dev`
**Status:** Approved

---

## Overview

Add a real-time notification system that keeps staff informed about application workflows, payments, connections, billing events, and user management. Replaces the current hardcoded dummy notifications in the header dropdown with live data, and adds a full notifications page at `/notifications`.

---

## Design Decisions

- **Targeting:** Role-based. Each notification type routes to users with relevant roles. Admin and Super-Admin see all notifications regardless.
- **Delivery:** Polling on page load. The dropdown fetches fresh data when opened. No WebSocket infrastructure required.
- **Architecture:** Direct service calls (no Event/Listener layer). `NotificationService` is called from existing workflow services where actions happen.
- **YAGNI:** No email/SMS notifications, no user subscription preferences, no real-time push. Can be added later if needed.

---

## 1. Header Dropdown (Revamped)

The existing bell icon in `navigation.blade.php` gets wired to real data.

**Badge:**
- Fetched via `GET /api/notifications/unread-count` on page load
- Shows count only when > 0, hidden otherwise
- Maxes out at "99+"

**Dropdown (on click):**
- Shows the 5 most recent notifications (fetched via API on click)
- **Opening the dropdown automatically marks all as read and clears the badge** — once the user has "seen" their notifications, the count resets. New notifications arriving later will show the badge again.
- Each item:
  - Colored dot on left edge (category color)
  - Title in semibold
  - Brief message in `text-sm` gray
  - Relative timestamp in `text-xs`
- "Mark all as read" text link in header (kept as manual fallback)
- "View all notifications" footer link routes to `/notifications`
- Clicking a notification navigates to its linked page

**Alpine.js component:** `notificationDropdown()` — manages badge count, fetches recent notifications, handles mark-read actions.

---

## 2. Full Notifications Page

**Route:** `GET /notifications`
**Layout:** Standard `x-app-layout` with sidebar + header

### Page Header
- Title: "Notifications" with bell icon
- Subtitle: "Stay updated on applications, payments, and system activity"
- Right side: "Mark all as read" button (outline style, only visible when unread exist)

### Stats Cards (grid of 4)
Clickable — clicking one filters the list to that category. Active filter gets a ring/border highlight.

| Card | Accent | Description |
|------|--------|-------------|
| Unread | Blue | Total unread count |
| Applications | Amber | Application-related notification count |
| Payments | Green | Payment-related notification count |
| Connections | Indigo | Connection-related notification count |

### Filter Bar
- **Tab pills:** All | Unread | Read — text buttons, active gets bottom border
- **Search input** on the right — searches titles and messages, debounced 300ms

### Notification List
- White card (`bg-white dark:bg-gray-800 rounded-lg`) with vertical list divided by borders
- Each notification row:
  - **Left:** Colored category dot
  - **Center:** Title (semibold `text-sm`), message (`text-sm text-gray-500`), source link text
  - **Right:** Relative timestamp (`text-xs text-gray-400`), "..." menu on hover for mark read/unread
  - Unread rows get subtle blue-tinted background
  - Clicking the row marks it read and navigates to linked resource
- **Empty state:** Centered "You're all caught up!" message
- **Pagination:** Standard prev/next, 15 items per page

### No modals — clicking navigates directly to the relevant detail page.

---

## 3. Notification Types & Triggers

### Service Application Workflow

| Trigger | Type | Recipients | Title | Message |
|---------|------|------------|-------|---------|
| Application submitted | `application_submitted` | Admin, Super-Admin | New Application Submitted | {customer_name} submitted a service application for {barangay} |
| Application verified | `application_verified` | Admin, Super-Admin, Cashier | Application Verified | Application #{app_number} for {customer_name} is verified and awaiting payment |
| Application payment received | `application_paid` | Admin, Super-Admin | Application Payment Received | Payment of {amount} received for application #{app_number} |
| Connection scheduled | `application_scheduled` | Admin, Super-Admin | Connection Scheduled | Application #{app_number} scheduled for connection on {date} |
| Connection completed | `application_connected` | Admin, Super-Admin | Connection Completed | {customer_name} is now connected — Account #{account_number} |
| Application rejected | `application_rejected` | Admin, Super-Admin | Application Rejected | Application #{app_number} was rejected: {reason} |
| Application cancelled | `application_cancelled` | Admin, Super-Admin | Application Cancelled | Application #{app_number} was cancelled |

### Service Connection Lifecycle

| Trigger | Type | Recipients | Title | Message |
|---------|------|------------|-------|---------|
| Connection suspended | `connection_suspended` | Admin, Super-Admin | Connection Suspended | Account #{account_number} ({customer_name}) has been suspended |
| Connection disconnected | `connection_disconnected` | Admin, Super-Admin | Connection Disconnected | Account #{account_number} ({customer_name}) has been disconnected |
| Connection reconnected | `connection_reconnected` | Admin, Super-Admin | Connection Reconnected | Account #{account_number} ({customer_name}) has been reconnected |

### Payment Workflow

| Trigger | Type | Recipients | Title | Message |
|---------|------|------------|-------|---------|
| Water bill payment processed | `payment_processed` | Admin, Super-Admin, Cashier | Payment Processed | Payment of {amount} received from {customer_name} — Receipt #{receipt_number} |
| Payment cancelled | `payment_cancelled` | Admin, Super-Admin | Payment Cancelled | Payment #{receipt_number} of {amount} has been cancelled by {cancelled_by} |

### Billing

| Trigger | Type | Recipients | Title | Message |
|---------|------|------------|-------|---------|
| Bills generated | `bills_generated` | Admin, Super-Admin, Cashier | Bills Generated | {count} water bills generated for period {period_name} |
| Penalties processed | `penalty_processed` | Admin, Super-Admin | Penalties Applied | {count} overdue penalties applied for period {period_name} |

### User Management

| Trigger | Type | Recipients | Title | Message |
|---------|------|------------|-------|---------|
| User created | `user_created` | Admin, Super-Admin | New User Created | User {username} ({role}) has been added to the system |

### Category Colors

| Color | Category | Types |
|-------|----------|-------|
| Blue | Applications | submitted, verified, scheduled, connected, rejected, cancelled |
| Green | Payments | paid, processed |
| Red | Alerts | suspended, disconnected, payment cancelled, penalties |
| Amber | Billing | bills generated |
| Indigo | System | user created, reconnected |

---

## 4. Technical Architecture

### Flow
```
Existing Service (e.g., ServiceApplicationService::verifyApplication())
  → performs business logic (existing code)
  → calls NotificationService::notifyApplicationVerified($application)
  → NotificationService resolves recipients by role
  → creates Notification row per recipient (excluding acting user)
```

### Recipient Resolution

`NotificationService::resolveRecipients(string $type, ?int $excludeUserId): Collection`

| Type | Roles |
|------|-------|
| `application_submitted` | admin, super-admin |
| `application_verified` | admin, super-admin, cashier |
| `application_paid` | admin, super-admin |
| `payment_processed` | admin, super-admin, cashier |
| `payment_cancelled` | admin, super-admin |
| `bills_generated` | admin, super-admin, cashier |
| Everything else | admin, super-admin |

The acting user (who triggered the action) is always excluded from receiving their own notification.

### API Endpoints (existing, need frontend wiring + filter enhancement)

| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/api/notifications?filter={all\|unread\|read}&category={type}&search={query}&page={n}` | List with filters |
| GET | `/api/notifications/unread-count` | Badge count |
| POST | `/api/notifications/{id}/read` | Mark single as read |
| POST | `/api/notifications/read-all` | Mark all as read |

### Frontend Components

| Component | Purpose |
|-----------|---------|
| `notificationDropdown()` | Header bell — badge count, recent 5, mark all read |
| `notificationManager()` | Full page — fetch, filter, paginate, search, mark read |

### Database

Existing `notifications` table — no migration needed:
- `id`, `user_id`, `type`, `title`, `message`, `link`, `source_type`, `source_id`, `read_at`, `created_at`, `updated_at`

### Cleanup

Wire `NotificationService::cleanupOldNotifications()` to a scheduled command. Runs daily, removes notifications older than 90 days.

---

## 5. Files to Create/Modify

### Backend
- `app/Services/NotificationService.php` — Add `resolveRecipients()`, add new notification types (`payment_processed`, `payment_cancelled`, `bills_generated`, `penalty_processed`, `user_created`), update existing methods to use role-based targeting
- `app/Http/Controllers/NotificationController.php` — Add filter/search/category query params to `index()`, add web route for notifications page
- `app/Models/Notification.php` — Add new type constants (`TYPE_PAYMENT_PROCESSED`, `TYPE_PAYMENT_CANCELLED`, `TYPE_BILLS_GENERATED`, `TYPE_PENALTY_PROCESSED`, `TYPE_USER_CREATED`)
- `app/Console/Kernel.php` — Schedule daily cleanup command

### Service Integration Points (add NotificationService calls)
- `app/Services/Customers/ServiceApplicationService.php` — All workflow methods
- `app/Services/Customers/ServiceConnectionService.php` — suspend, disconnect, reconnect
- `app/Services/PaymentService.php` — processWaterBillPayment, cancelPayment
- `app/Services/WaterBillService.php` — generateBill
- `app/Services/PenaltyService.php` — processAllOverdueBills
- `app/Services/Users/UserService.php` — createUser

### Frontend
- `resources/views/notifications/index.blade.php` — Full notifications page
- `resources/views/layouts/navigation.blade.php` — Revamp dropdown to use API
- `resources/js/data/notifications/notification-dropdown.js` — Dropdown Alpine component
- `resources/js/data/notifications/notification-manager.js` — Page Alpine component
- `routes/web.php` — Add `GET /notifications` route

---

## 6. Implementation Order

1. **Backend: Model & Service updates** — Add new types, resolveRecipients(), update notification methods
2. **Backend: Controller updates** — Add filters/search to index, add web route
3. **Backend: Wire triggers** — Add NotificationService calls to all workflow services
4. **Frontend: Dropdown** — Replace hardcoded dropdown with Alpine component + API
5. **Frontend: Full page** — Build notifications index page
6. **Backend: Cleanup command** — Schedule daily cleanup
7. **Testing** — Verify all trigger points create correct notifications
