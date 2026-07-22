 <?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// NOTE: the checkout shipping-zones endpoint lives in routes/web.php under
// Route::prefix('api/shipping') (ShippingApiController) — it is a public,
// session-aware storefront endpoint, not a Sanctum API route, so it does not
// belong in this file. A duplicate registration here previously shadowed
// under Laravel's "/api" route-group prefix and was unreachable dead code.