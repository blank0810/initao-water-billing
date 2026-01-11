<?php

use App\Http\Controllers\Address\AddressController;
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
