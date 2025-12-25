<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CustomerApprovalController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConsumerController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\MeterController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

// Home page
Route::get('/', function () {
    return view('welcome');
});

// DEV-ONLY: Simulate login for frontend (optional)
Route::get('/dev-login', function () {
    if (!app()->environment('local')) {
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
    return view('pages.customer.add-customer');
})->name('customer.add');

Route::post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');

Route::get('/customer/list', [CustomerController::class, 'index'])->name('customer.list');

// Application Process Page
Route::get('/customer/application-process', function () {
    session(['active_menu' => 'application-process']);
    return view('pages.customer.application-process');
})->name('application.process');

// Payment Processing Page
Route::get('/customer/payment/{customerCode}', function ($customerCode) {
    session(['active_menu' => 'customer-list']);
    return view('pages.customer.payment-management', ['customerCode' => $customerCode]);
})->name('customer.payment');

// Payment Management Page
Route::get('/customer/payment-management', function () {
    return view('pages.customer.payment-management');
})->name('payment.management');

// Invoice List Page
Route::get('/customer/invoice-list', function () {
    session(['active_menu' => 'invoice-list']);
    return view('pages.customer.invoice-list');
})->name('invoice.list');

// Customer Approval Page (Phase 3: Enhanced Approval)
Route::get('/customer/approve-customer', function () {
    session(['active_menu' => 'approve-customer']);
    return view('pages.customer.approve-customer');
})->name('approve.customer');

// Declined Customer Page
Route::get('/customer/declined-customer', function () {
    session(['active_menu' => 'declined-customer']);
    return view('pages.customer.declined-customer');
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
    return view('pages.connection.service-application');
})->name('service.application');

// Service Connection Page
Route::get('/customer/service-connection', function () {
    session(['active_menu' => 'service-connection']);
    return view('pages.connection.service-connection');
})->name('service.connection');

// Billing Management Page
Route::get('/billing/management', function () {
    session(['active_menu' => 'billing-management']);
    return view('pages.billing.billing-index');
})->name('billing.management');

// Individual Consumer Billing Page
Route::get('/billing/consumer/{id}', function ($id) {
    session(['active_menu' => 'billing-management']);
    return view('pages.billing.consumer-view', ['connectionId' => $id]);
})->name('billing.consumer');

// Additional routes for other menu items
Route::get('/user/add', function () {
    session(['active_menu' => 'user-add']);
    return view('pages.user.add-user');
})->name('user.add');

Route::get('/user/list', [UserController::class, 'index'])->name('user.list');
Route::post('/user', [UserController::class, 'store'])->name('user.store');
Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy');

Route::get('/consumer/list', function () {
    session(['active_menu' => 'consumer-list']);
    return view('pages.consumer.consumer-list');
})->name('consumer.list');

Route::get('/consumer/details/{id}', function ($id) {
    session(['active_menu' => 'consumer-list']);
    return view('pages.consumer.consumer-details', ['consumer_id' => $id]);
})->name('consumer.details');

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
