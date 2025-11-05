<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\PaymentController;
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

// Payment Management Page (updated to use PaymentController)
Route::get('/customer/payment-management', [PaymentController::class, 'index'])
    ->name('payment.management');

// Customer Approval Page
Route::get('/customer/approve-customer', function () {
    session(['active_menu' => 'approve-customer']);
    return view('pages.customer.approve-customer');
})->name('approve-customer');

// Additional routes for other menu items
Route::get('/user/add', function () {
    session(['active_menu' => 'user-add']);
    return view('pages.user.add-user');
})->name('user.add');

Route::get('/user/list', function () {
    session(['active_menu' => 'user-list']);
    return view('pages.user.user-list');
})->name('user.list');

Route::get('/consumer/list', function () {
    session(['active_menu' => 'consumer-list']);
    return view('pages.consumer.consumer-list');
})->name('consumer.list');

Route::get('/billing/management', function () {
    session(['active_menu' => 'billing-management']);
    return view('pages.billing.management');
})->name('billing.management');

Route::get('/meter/management', function () {
    session(['active_menu' => 'meter-management']);
    return view('pages.meter.management');
})->name('meter.management');

Route::get('/rate/management', function () {
    session(['active_menu' => 'rate-management']);
    return view('pages.rate.management');
})->name('rate.management');

Route::get('/ledger/management', function () {
    session(['active_menu' => 'ledger-management']);
    return view('pages.ledger.management');
})->name('ledger.management');

Route::get('/analytics', function () {
    session(['active_menu' => 'analytics']);
    return view('pages.analytics');
})->name('analytics');

// Profile routes (still protected)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth routes
require __DIR__.'/auth.php';
