<?php

use App\Http\Controllers\CheckoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// routes/api.php is already prefixed with /api — the leading '/api' here
// produced /api/api/shipping/zones/{country}
Route::get('/shipping/zones/{country}', [CheckoutController::class, 'zonesForCountry']);