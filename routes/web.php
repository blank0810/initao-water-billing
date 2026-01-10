<?php

use App\Http\Controllers\CustomerApprovalController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

// Home page
Route::get('/', function () {
    return view('auth.login');
});

// DEV-ONLY: Simulate login for frontend (optional)
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

// Dashboard (bypass auth for frontend)
Route::get('/dashboard', function () {
    session(['active_menu' => 'dashboard']);

    return view('dashboard');
})->name('dashboard'); // removed ->middleware(['auth', 'verified'])

// Customer Pages
Route::get('/customer/add', function () {
    session(['active_menu' => 'customer-add']);

    return view('pages.application.add-customer');
})->name('customer.add');

// Customer Management – List
Route::get('/customer/list', function () {
    session(['active_menu' => 'customer-list']);

    return view('pages.customer.enhanced-customer-list');
})->name('customer.list');

// Application Management – Application List
Route::get('/application/list', function () {
    session(['active_menu' => 'application-list']);

    return view('pages.application.application-list');
})->name('application.list');

// Payment Management → Application Process (hierarchy)
Route::get('/customer/payment-management/application-process', function () {
    session(['active_menu' => 'payment-management']);

    return view('pages.payment.payment-management');
})->name('payment.application.process');

// Create Payment Page (Status-Based)
Route::get('/payment/create/{customerCode}', [PaymentController::class, 'create'])->name('payment.create');

// Payment Processing Page (Legacy/Backup)
Route::get('/customer/payment/{customerCode}', function ($customerCode) {
    session(['active_menu' => 'payment-management']);

    return view('pages.payment.payment-processing', ['customerCode' => $customerCode]);
})->name('customer.payment');

// Payment Management Page
Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
Route::get('/customer/payment-management', function () {
    session(['active_menu' => 'payment-management']);

    return view('pages.payment.payment-management');
})->name('payment.management');

// Invoice List Page
Route::get('/customer/invoice-list', function () {
    session(['active_menu' => 'approve-customer']);

    return view('pages.application.invoice-list');
})->name('invoice.list');

// Customer Approval Page (Phase 3: Enhanced Approval)
Route::get('/customer/approve-customer', function () {
    session(['active_menu' => 'approve-customer']);

    return view('pages.application.approve-customer');
})->name('approve.customer');

// Declined Customer Page
Route::get('/customer/declined-customer', function () {
    session(['active_menu' => 'approve-customer']);

    return view('pages.application.declined-customer');
})->name('declined.customer');

// Customer Approval Action
Route::post('/customer/approve', [CustomerApprovalController::class, 'approve'])->name('customer.approve');

// Customer Decline Action
Route::post('/customer/decline', [CustomerApprovalController::class, 'decline'])->name('customer.decline');

// Customer Restore Action
Route::post('/customer/restore', [CustomerApprovalController::class, 'restore'])->name('customer.restore');

// Customer CRUD Routes
Route::post('/customer', [CustomerController::class, 'store'])->name('customer.store');
Route::put('/customer/{id}', [CustomerController::class, 'update'])->name('customer.update');
Route::delete('/customer/{id}', [CustomerController::class, 'destroy'])->name('customer.destroy');
Route::get('/customer/{id}/print-count', [CustomerController::class, 'printCount'])->name('customer.print-count');

// Service Application Page
Route::get('/connection/service-application', function () {
    session(['active_menu' => 'service-application']);

    return view('pages.customer.service-application');
})->name('service.application');

// Service Connection Page
Route::get('/customer/service-connection', function () {
    session(['active_menu' => 'service-connection']);

    return view('pages.customer.service-connection');
})->name('service.connection');

// Billing Management Pages - Unified System
Route::get('/billing', function () {
    session(['active_menu' => 'billing-management']);
    return view('pages.billing.index');
})->name('billing.main');

Route::get('/billing/management', function () {
    session(['active_menu' => 'billing-management']);
    return view('pages.billing.index');
})->name('billing.management');

Route::get('/billing/consumer', function () {
    session(['active_menu' => 'billing-management']);
    return view('pages.billing.index');
})->name('billing.consumer');

Route::get('/billing/collections', function () {
    session(['active_menu' => 'billing-management']);
    return view('pages.billing.bill-generation');
})->name('billing.collections');

Route::get('/billing/generation', function () {
    session(['active_menu' => 'billing-management']);
    return view('pages.billing.bill-generation');
})->name('billing.generation');

Route::get('/billing/adjustments', function () {
    session(['active_menu' => 'billing-management']);
    return view('pages.billing.bill-generation');
})->name('billing.adjustments');

Route::get('/billing/rate-detail', function () {
    session(['active_menu' => 'billing-management']);
    return view('pages.billing.bill-generation');
})->name('billing.rate-detail');

Route::get('/billing/customer-details', function () {
    session(['active_menu' => 'billing-management']);
    return view('pages.billing.customer-details');
})->name('billing.customer-details');

Route::get('/billing/ledger', function () {
    session(['active_menu' => 'billing-management']);
    return view('pages.billing.bill-generation');
})->name('billing.ledger');

Route::get('/billing/download', function () {
    session(['active_menu' => 'billing-management']);
    return view('pages.billing.bill-generation');
})->name('billing.download');

Route::get('/billing/overall-data', function () {
    session(['active_menu' => 'billing-management']);
    return view('components.ui.billing.billing.overall-data.overall-data');
})->name('billing.overall-data');

Route::get('/billing/consumer/{connectionId}', function ($connectionId) {
    session(['active_menu' => 'billing-management']);
    return view('components.ui.billing.billing.consumer-view', ['connectionId' => $connectionId]);
})->name('billing.consumer.view');

// Legacy billing routes
Route::get('/billing/management', function () {
    return redirect()->route('billing.main');
})->name('billing.management');

Route::get('/billing/customer/{id}', function ($id) {
    session(['active_menu' => 'billing-management']);
    return view('pages.billing.customer-details', ['customer_id' => $id]);
})->name('billing.customer.details');

// Additional routes for other menu items
Route::get('/user/add', function () {
    session(['active_menu' => 'user-add']);

    return view('pages.user.add-user');
})->name('user.add');

Route::get('/user/list', [UserController::class, 'index'])->name('user.list');
Route::post('/user', [UserController::class, 'store'])->name('user.store');
Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');

Route::get('/customer/details/{id}', function ($id) {
    session(['active_menu' => 'customer-list']);

    return view('pages.customer.customer-details', ['consumer_id' => $id]);
})->name('customer.details');

Route::get('/meter/management', function () {
    session(['active_menu' => 'meter-management']);

    return view('pages.meter.management');
})->name('meter.management');

Route::get('/meter/assignment', function () {
    return view('pages.meter.meter-assignment');
})->name('meter.assignment');

Route::get('/rate/management', function () {
    session(['active_menu' => 'rate-management']);

    return view('pages.rate.management');
})->name('rate.management');

Route::get('/ledger/management', function () {
    session(['active_menu' => 'ledger-management']);

    return view('pages.ledger.management');
})->name('ledger.management');

// Overall Data Pages
Route::get('/billing/overall-data', function () {
    session(['active_menu' => 'billing-management']);

    return view('pages.billing.overall-data.overall-data');
})->name('billing.overall-data');

Route::get('/meter/overall-data', function () {
    session(['active_menu' => 'meter-management']);

    return view('pages.meter.overall-data.overall-data');
})->name('meter.overall-data');

Route::get('/ledger/overall-data', function () {
    session(['active_menu' => 'ledger-management']);

    return view('pages.ledger.overall-data.overall-data');
})->name('ledger.overall-data');

Route::get('/rate/overall-data', function () {
    session(['active_menu' => 'rate-management']);

    return view('pages.rate.overall-data.overall-data');
})->name('rate.overall-data');

Route::get('/analytics', function () {
    session(['active_menu' => 'analytics']);

    return view('pages.analytics.analytics');
})->name('analytics');

Route::get('/settings', function () {
    session(['active_menu' => 'settings']);

    return view('pages.info-pages.settings');
})->name('settings');

Route::get('/report', function () {
    session(['active_menu' => 'report']);

    return view('pages.info-pages.report');
})->name('report');

// Error Pages
Route::get('/404', function () {
    return view('pages.info-pages.page-not-found');
})->name('404');

Route::get('/no-internet-found', function () {
    return view('pages.info-pages.no-internet-found');
})->name('no-internet');

// Fallback route for 404
Route::fallback(function () {
    return view('pages.info-pages.page-not-found');
});

// Profile routes (still protected)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth routes
require __DIR__.'/auth.php';
