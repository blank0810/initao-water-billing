<?php

use App\Http\Controllers\Api\AddressController;
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
});
