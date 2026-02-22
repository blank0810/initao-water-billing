<?php

use App\Http\Controllers\Address\AddressController;
use App\Http\Controllers\Api\MeterReadingDownloadController;
use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\UploadedReadingController;
use App\Http\Controllers\Customer\CustomerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check endpoint for mobile app connectivity
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
    ]);
});

// Address API endpoints for cascading dropdowns
Route::prefix('address')->group(function () {
    Route::get('/provinces', [AddressController::class, 'getProvinces']);
    Route::get('/towns', [AddressController::class, 'getTowns']);
    Route::get('/barangays', [AddressController::class, 'getBarangays']);
    Route::get('/puroks', [AddressController::class, 'getPuroks']);
    Route::get('/account-types', [AddressController::class, 'getAccountTypes']);
    Route::get('/water-rates', [AddressController::class, 'getWaterRates']);
    Route::get('/application-charges', [AddressController::class, 'getApplicationCharges']);
});

// Customer API endpoints for CRUD operations
Route::prefix('customers')->group(function () {
    // Search must come BEFORE /{id} to avoid "search" being captured as an ID
    Route::get('/search', [CustomerController::class, 'search']);
    Route::get('/{id}', [CustomerController::class, 'show'])->where('id', '[0-9]+');
    Route::get('/{id}/applications', [CustomerController::class, 'getApplications'])->where('id', '[0-9]+');
    Route::get('/{id}/can-delete', [CustomerController::class, 'canDelete'])->where('id', '[0-9]+');
    Route::put('/{id}', [CustomerController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/{id}', [CustomerController::class, 'destroy'])->where('id', '[0-9]+');
});

// Customer Ledger API endpoints (protected - requires customers.view permission)
Route::middleware(['auth:sanctum', 'permission:customers.view'])->group(function () {
    Route::get('/customer/{id}/ledger', [CustomerController::class, 'getLedger'])->where('id', '[0-9]+')->name('api.customer.ledger');
    Route::get('/customer/ledger/{entryId}', [CustomerController::class, 'getLedgerEntryDetails'])->where('entryId', '[0-9]+')->name('api.customer.ledger.entry');
    Route::get('/customer/{id}/ledger/export/pdf', [CustomerController::class, 'exportLedgerStatement'])->where('id', '[0-9]+')->name('api.customer.ledger.export.pdf');
    Route::get('/customer/{id}/ledger/export/csv', [CustomerController::class, 'exportLedgerCsv'])->where('id', '[0-9]+')->name('api.customer.ledger.export.csv');
});

// Meter Reading Download API endpoints
Route::prefix('meter-reading')->middleware('auth:sanctum')->group(function () {
    // Endpoint to retrieve consumer information for the authenticated user's active schedules.
    // Changed from '/user/{userId}/consumers' to '/me/consumers' to enforce
    // that a user can only access their own data, preventing unauthorized access
    // to other users' consumer information.
    Route::get('/me/consumers', [MeterReadingDownloadController::class, 'getConsumerInfo']);

    // Endpoint to retrieve water rates applicable for the current active period.
    Route::get('/rates/current', [MeterReadingDownloadController::class, 'getCurrentPeriodRates']);

    // Endpoint to retrieve water rates for a specific billing period, identified by periodId.
    Route::get('/rates/period/{periodId}', [MeterReadingDownloadController::class, 'getRatesByPeriod'])
        ->where('periodId', '[0-9]+');
});

// Mobile App Authentication API endpoints
Route::prefix('mobile')->group(function () {
    Route::post('/login', [MobileAuthController::class, 'login']);
});

// Uploaded Readings API endpoints
Route::prefix('uploaded-readings')->middleware('auth:sanctum')->group(function () {
    // Upload readings from mobile device
    Route::post('/upload', [UploadedReadingController::class, 'upload']);

    // Get authenticated user's uploaded readings (recommended)
    Route::get('/me', [UploadedReadingController::class, 'getMyReadings']);

    // Get uploaded readings by schedule (user must be assigned reader)
    Route::get('/schedule/{scheduleId}', [UploadedReadingController::class, 'getBySchedule'])
        ->where('scheduleId', '[0-9]+');

    // Get uploaded readings by user (user can only access their own)
    Route::get('/user/{userId}', [UploadedReadingController::class, 'getByUser'])
        ->where('userId', '[0-9]+');
});

// Phone submits scanned QR data (public, token-validated)
Route::post('/scan/{token}', [\App\Http\Controllers\Scan\ScanSessionController::class, 'submit'])->name('api.scan.submit');
