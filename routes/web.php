<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Customer\CustomerApprovalController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\ServiceApplication\ServiceApplicationController;
use App\Http\Controllers\ServiceConnection\ServiceConnectionController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

// ============================================================================
// PUBLIC ROUTES (No authentication required)
// ============================================================================

// Home/Welcome page
Route::get('/', function () {
    return view('welcome');
});

// DEV-ONLY: Simulate login for frontend testing (only works in local environment)
Route::get('/dev-login', function () {
    if (! app()->environment('local')) {
        abort(403);
    }

    $user = User::firstOrCreate(
        ['email' => 'demo@example.com'],
        [
            'name' => 'Demo User',
            'password' => Hash::make('password123'),
        ]
    );

    Auth::login($user);

    return redirect()->route('dashboard');
});

// Error Pages (public)
Route::get('/404', function () {
    return view('pages.info-pages.page-not-found');
})->name('404');

Route::get('/no-internet-found', function () {
    return view('pages.info-pages.no-internet-found');
})->name('no-internet');

// Coming Soon page (for features under development)
Route::get('/coming-soon', function () {
    return view('pages.coming-soon');
})->name('coming-soon');

// Fallback route for 404
Route::fallback(function () {
    return view('pages.info-pages.page-not-found');
});

// ============================================================================
// PROTECTED ROUTES (Authentication required)
// ============================================================================

Route::middleware('auth')->group(function () {

    // Dashboard - All authenticated users can access
    Route::get('/dashboard', function () {
        session(['active_menu' => 'dashboard']);

        return view('dashboard');
    })->name('dashboard');

    // -------------------------------------------------------------------------
    // Profile Management - All authenticated users
    // -------------------------------------------------------------------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // -------------------------------------------------------------------------
    // Notifications - All authenticated users
    // -------------------------------------------------------------------------
    Route::prefix('api/notifications')->name('api.notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });

    // -------------------------------------------------------------------------
    // Customer Management - View (customers.view permission)
    // -------------------------------------------------------------------------
    Route::middleware(['permission:customers.view'])->group(function () {
        Route::get('/customer/list', [CustomerController::class, 'index'])->name('customer.list');
        Route::get('/customer/{id}/print-count', [CustomerController::class, 'printCount'])->name('customer.print-count');
        Route::get('/customer/invoice-list', function () {
            session(['active_menu' => 'invoice-list']);

            return view('pages.customer.invoice-list');
        })->name('invoice.list');
    });

    // Customer Management - Manage (customers.manage permission)
    Route::middleware(['permission:customers.manage'])->group(function () {
        Route::post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');
        Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store.alt');
        Route::put('/customer/{id}', [CustomerController::class, 'update'])->name('customer.update');
        Route::delete('/customer/{id}', [CustomerController::class, 'destroy'])->name('customer.destroy');

        // Application Process
        Route::get('/customer/application-process', function () {
            session(['active_menu' => 'application-process']);

            return view('pages.customer.application-process');
        })->name('application.process');

        // Customer Approval
        Route::get('/customer/approve-customer', function () {
            session(['active_menu' => 'approve-customer']);

            return view('pages.customer.approve-customer');
        })->name('approve.customer');

        Route::get('/customer/declined-customer', function () {
            session(['active_menu' => 'declined-customer']);

            return view('pages.customer.declined-customer');
        })->name('declined.customer');

        Route::post('/customer/approve', [CustomerApprovalController::class, 'approve'])->name('customer.approve');
        Route::post('/customer/decline', [CustomerApprovalController::class, 'decline'])->name('customer.decline');
        Route::post('/customer/restore', [CustomerApprovalController::class, 'restore'])->name('customer.restore');

        // Service Application Workflow
        Route::get('/connection/service-application', [ServiceApplicationController::class, 'index'])->name('connection.service-application.index');
        Route::get('/connection/service-application/create', [ServiceApplicationController::class, 'create'])->name('connection.service-application.create');
        Route::get('/api/service-application/fee-templates', [ServiceApplicationController::class, 'getFeeTemplates'])->name('api.service-application.fee-templates');
        Route::post('/connection/service-application', [ServiceApplicationController::class, 'store'])->name('connection.service-application.store');
        Route::get('/connection/service-application/{id}', [ServiceApplicationController::class, 'show'])->name('connection.service-application.show');
        Route::post('/connection/service-application/{id}/verify', [ServiceApplicationController::class, 'verify'])->name('service.application.verify');
        Route::get('/connection/service-application/{id}/charges', [ServiceApplicationController::class, 'getCharges'])->name('service.application.charges');
        Route::get('/connection/service-application/{id}/order-of-payment', [ServiceApplicationController::class, 'orderOfPayment'])->name('service.application.order-of-payment');
        Route::post('/connection/service-application/{id}/schedule', [ServiceApplicationController::class, 'schedule'])->name('service.application.schedule');
        Route::post('/connection/service-application/{id}/reject', [ServiceApplicationController::class, 'reject'])->name('service.application.reject');
        Route::post('/connection/service-application/{id}/cancel', [ServiceApplicationController::class, 'cancel'])->name('service.application.cancel');
        Route::get('/connection/service-application/{id}/timeline', [ServiceApplicationController::class, 'timeline'])->name('service.application.timeline');

        // Service Connection Workflow
        Route::get('/customer/service-connection', [ServiceConnectionController::class, 'index'])->name('service.connection');

        // Lookup routes - MUST come before {id} wildcard route
        Route::get('/customer/service-connection/meters/available', [ServiceConnectionController::class, 'availableMeters'])->name('service.connection.available-meters');
        Route::get('/customer/service-connection/account-types', [ServiceConnectionController::class, 'getAccountTypes'])->name('service.connection.account-types');
        Route::get('/customer/service-connection/water-rates', [ServiceConnectionController::class, 'getWaterRates'])->name('service.connection.water-rates');
        Route::post('/customer/service-connection/complete', [ServiceConnectionController::class, 'completeConnection'])->name('service.connection.complete');
        Route::post('/customer/service-connection/meter/{assignmentId}/remove', [ServiceConnectionController::class, 'removeMeter'])->name('service.connection.remove-meter');

        // Wildcard routes - MUST come after specific routes
        Route::get('/customer/service-connection/{id}', [ServiceConnectionController::class, 'show'])->name('service.connection.show');
        Route::post('/customer/service-connection/{id}/suspend', [ServiceConnectionController::class, 'suspend'])->name('service.connection.suspend');
        Route::post('/customer/service-connection/{id}/disconnect', [ServiceConnectionController::class, 'disconnect'])->name('service.connection.disconnect');
        Route::post('/customer/service-connection/{id}/reconnect', [ServiceConnectionController::class, 'reconnect'])->name('service.connection.reconnect');
        Route::get('/customer/service-connection/{id}/balance', [ServiceConnectionController::class, 'balance'])->name('service.connection.balance');
        Route::post('/customer/service-connection/{id}/assign-meter', [ServiceConnectionController::class, 'assignMeter'])->name('service.connection.assign-meter');
    });

    // -------------------------------------------------------------------------
    // Payment Processing - View (payments.view permission)
    // -------------------------------------------------------------------------
    Route::middleware(['permission:payments.view'])->group(function () {
        Route::get('/customer/payment-management', [PaymentController::class, 'index'])->name('payment.management');
        Route::get('/api/payments/pending', [PaymentController::class, 'getPendingPayments'])->name('api.payments.pending');
        Route::get('/api/payments/statistics', [PaymentController::class, 'getStatistics'])->name('api.payments.statistics');
        Route::get('/payment/process/application/{id}', [PaymentController::class, 'processApplicationPayment'])->name('payment.process.application');
        Route::get('/payment/receipt/{id}', [PaymentController::class, 'showReceipt'])->name('payment.receipt');
    });

    // Payment Processing - Process (payments.process permission)
    Route::middleware(['permission:payments.process'])->group(function () {
        Route::get('/customer/payment/{customerCode}', function ($customerCode) {
            session(['active_menu' => 'customer-list']);

            return view('pages.customer.payment-management', ['customerCode' => $customerCode]);
        })->name('customer.payment');

        // Service Application Payment Processing (for cashiers)
        Route::post('/connection/service-application/{id}/process-payment', [ServiceApplicationController::class, 'processPayment'])->name('service.application.process-payment');
    });

    // -------------------------------------------------------------------------
    // Billing Management - View (billing.view permission)
    // -------------------------------------------------------------------------
    Route::middleware(['permission:billing.view'])->group(function () {
        Route::get('/billing/management', function () {
            session(['active_menu' => 'billing-management']);

            return view('pages.billing.billing-index');
        })->name('billing.management');

        Route::get('/billing/consumer/{id}', function ($id) {
            session(['active_menu' => 'billing-management']);

            return view('pages.billing.consumer-view', ['connectionId' => $id]);
        })->name('billing.consumer');

        Route::get('/billing/overall-data', function () {
            session(['active_menu' => 'billing-management']);

            return view('pages.billing.overall-data.overall-data');
        })->name('billing.overall-data');

        // Ledger Management (part of billing)
        Route::get('/ledger/management', function () {
            session(['active_menu' => 'ledger-management']);

            return view('pages.ledger.management');
        })->name('ledger.management');

        Route::get('/ledger/overall-data', function () {
            session(['active_menu' => 'ledger-management']);

            return view('pages.ledger.overall-data.overall-data');
        })->name('ledger.overall-data');
    });

    // -------------------------------------------------------------------------
    // User Management - View (users.view permission)
    // -------------------------------------------------------------------------
    Route::middleware(['permission:users.view'])->group(function () {
        Route::get('/user/list', [UserController::class, 'index'])->name('user.list');
    });

    // User Management - Manage (users.manage permission)
    Route::middleware(['permission:users.manage'])->group(function () {
        Route::get('/user/add', [UserController::class, 'create'])->name('user.add');

        Route::post('/user', [UserController::class, 'store'])->name('user.store');
        Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');
    });

    // -------------------------------------------------------------------------
    // Consumer Management (Legacy) - View (customers.view permission)
    // -------------------------------------------------------------------------
    Route::middleware(['permission:customers.view'])->group(function () {
        Route::get('/consumer/list', function () {
            session(['active_menu' => 'consumer-list']);

            return view('pages.consumer.consumer-list');
        })->name('consumer.list');

        Route::get('/consumer/details/{id}', function ($id) {
            session(['active_menu' => 'consumer-list']);

            return view('pages.consumer.consumer-details', ['consumer_id' => $id]);
        })->name('consumer.details');
    });

    // -------------------------------------------------------------------------
    // Meter Management - View (meters.view permission)
    // -------------------------------------------------------------------------
    Route::middleware(['permission:meters.view'])->group(function () {
        Route::get('/meter/management', function () {
            session(['active_menu' => 'meter-management']);

            return view('pages.meter.management');
        })->name('meter.management');

        Route::get('/meter/overall-data', function () {
            session(['active_menu' => 'meter-management']);

            return view('pages.meter.overall-data.overall-data');
        })->name('meter.overall-data');
    });

    // Meter Management - Manage (meters.manage permission)
    Route::middleware(['permission:meters.manage'])->group(function () {
        Route::get('/meter/assignment', function () {
            return view('pages.meter.meter-assignment');
        })->name('meter.assignment');
    });

    // -------------------------------------------------------------------------
    // Rate Management - Settings (settings.manage permission)
    // -------------------------------------------------------------------------
    Route::middleware(['permission:settings.manage'])->group(function () {
        Route::get('/rate/management', function () {
            session(['active_menu' => 'rate-management']);

            return view('pages.rate.management');
        })->name('rate.management');

        Route::get('/rate/overall-data', function () {
            session(['active_menu' => 'rate-management']);

            return view('pages.rate.overall-data.overall-data');
        })->name('rate.overall-data');

        Route::get('/settings', function () {
            session(['active_menu' => 'settings']);

            return view('pages.info-pages.settings');
        })->name('settings');
    });

    // -------------------------------------------------------------------------
    // Reports & Analytics - View (reports.view permission)
    // -------------------------------------------------------------------------
    Route::middleware(['permission:reports.view'])->group(function () {
        Route::get('/analytics', function () {
            session(['active_menu' => 'analytics']);

            return view('pages.analytics.analytics');
        })->name('analytics');

        Route::get('/report', function () {
            session(['active_menu' => 'report']);

            return view('pages.info-pages.report');
        })->name('report');
    });

    // -------------------------------------------------------------------------
    // Admin RBAC Management - Settings (settings.manage permission)
    // -------------------------------------------------------------------------
    Route::middleware(['permission:settings.manage'])->prefix('admin')->name('admin.')->group(function () {

        // Role Management
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('/roles/{role}/users', [RoleController::class, 'getRoleUsers'])->name('roles.users');

        // Permission Management
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');
        Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');

        // Role-Permission Matrix
        Route::get('/role-permissions', [RolePermissionController::class, 'matrix'])->name('role-permissions.matrix');
        Route::put('/role-permissions/{role}', [RolePermissionController::class, 'updateRolePermissions'])->name('role-permissions.update');
        Route::post('/role-permissions/toggle', [RolePermissionController::class, 'togglePermission'])->name('role-permissions.toggle');
        Route::post('/role-permissions/bulk', [RolePermissionController::class, 'bulkUpdate'])->name('role-permissions.bulk');
    });

    // -------------------------------------------------------------------------
    // API Routes for RBAC (AJAX calls)
    // -------------------------------------------------------------------------
    Route::middleware(['permission:settings.manage'])->prefix('api/admin')->name('api.admin.')->group(function () {
        Route::get('/roles', [RoleController::class, 'apiIndex'])->name('roles.list');
        Route::get('/permissions', [PermissionController::class, 'apiIndex'])->name('permissions.list');
        Route::get('/permissions/grouped', [PermissionController::class, 'getGroupedPermissions'])->name('permissions.grouped');
        Route::get('/role-permissions/matrix', [RolePermissionController::class, 'getMatrixData'])->name('role-permissions.matrix-data');
    });

    // API Routes for User Management (accessible by users.view/manage permissions)
    Route::middleware(['permission:users.view'])->prefix('api')->name('api.')->group(function () {
        Route::get('/users', [UserController::class, 'apiIndex'])->name('users.list');
        Route::get('/users/stats', [UserController::class, 'stats'])->name('users.stats');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    });

    Route::middleware(['permission:users.manage'])->group(function () {
        Route::get('/api/roles/available', [RoleController::class, 'getAvailableRoles'])->name('api.roles.available');
    });

    // API Routes for Service Application/Connection Workflow
    Route::middleware(['permission:customers.manage'])->prefix('api')->name('api.')->group(function () {
        // Customer Search (for service application)
        Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');

        // Service Applications
        Route::get('/service-applications/status/{status}', [ServiceApplicationController::class, 'getByStatus'])->name('service-applications.by-status');

        // Service Connections
        Route::get('/service-connections/status/{status}', [ServiceConnectionController::class, 'getByStatus'])->name('service-connections.by-status');
        Route::get('/service-connections/customer/{customerId}', [ServiceConnectionController::class, 'customerConnections'])->name('service-connections.customer');
    });

    // -------------------------------------------------------------------------
    // Activity Log - Super Admin Only
    // -------------------------------------------------------------------------
    Route::middleware(['role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log');
    });

});

// Auth routes (login, register, password reset, etc.)
require __DIR__.'/auth.php';
