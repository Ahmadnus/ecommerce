<?php

use App\Http\Controllers\Api\ShippingZoneApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Zones for a country — still used by the branch/zone admin tooling.
Route::get('/shipping/zones/{country}', [ShippingZoneApiController::class, 'index'])
    ->name('api.shipping.zones');
