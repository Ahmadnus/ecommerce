<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\ProductController as admin;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home → redirect to products listing
Route::get('/', fn() => redirect()->route('products.index'))->name('home');

// ── Products ──────────────────────────────────────────────────────────────────
Route::get('/products',        [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// ── Cart ──────────────────────────────────────────────────────────────────────
Route::get('/cart',                      [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add',                 [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update',             [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{productId}',[CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/count',                [CartController::class, 'count'])->name('cart.count');

// ── Checkout & Orders ─────────────────────────────────────────────────────────
Route::get('/checkout',  [OrderController::class, 'checkout'])->name('checkout.index');
Route::post('/checkout', [OrderController::class, 'placeOrder'])->name('checkout.place');
Route::get('/orders/{orderNumber}/confirmation', [OrderController::class, 'confirmation'])->name('orders.confirmation');

// ── Auth (Laravel Breeze/Jetstream can replace this stub) ─────────────────────
// Route::middleware('guest')->group(function () {
//     Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
//     Route::post('/login', [AuthController::class, 'login']);
//     Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
//     Route::post('/register', [AuthController::class, 'register']);
// });
// Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CheckoutController;

Route::prefix('admin')->name('admin.')->group(function () {
    
    // الصفحة الرئيسية للوحة التحكم
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD المنتجات كامل (index, create, store, edit, update, destroy)
    Route::resource('products', admin::class);
Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    // CRUD التصنيفات كامل (هذا هو السطر الذي ينقصك)
    Route::resource('categories', CategoryController::class);


  

    // الإعدادات
    Route::get('settings', [SettingController::class, 'index'])->name('settings');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

});
Route::middleware('guest')->group(function () {
 
    // Login
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
 
    // Register
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
 
});
 
// ─── Authenticated routes ─────────────────────────────────────────────────────
 
Route::middleware('auth')->group(function () {
 
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
 
    // Add your protected routes here, for example:
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Route::get('/orders',    [OrderController::class, 'index'])->name('orders.index');
 
});
use App\Http\Controllers\WishlistController;
 
Route::middleware('auth')->group(function () {
 
    // Wishlist page
    Route::get('/wishlist', [WishlistController::class, 'index'])
         ->name('wishlist.index');
 
    // Toggle (AJAX) — POST with product model binding
    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])
         ->name('wishlist.toggle');
 
});

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/',              [CartController::class, 'index'])->name('index');
    Route::post('/add',          [CartController::class, 'add'])->name('add');
    Route::patch('/update',      [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{itemKey}', [CartController::class, 'remove'])->name('remove');
});
 
// ─── Checkout + Orders (auth required) ───────────────────────────────────────
Route::middleware('auth')->group(function () {
 
    // Unified cart + checkout page
    Route::get('/checkout',  [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
 
    // Order success page
    Route::get('/orders/success/{orderNumber}', [OrderController::class, 'success'])
         ->name('orders.success');
 
    // User's order history
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
 
});
Route::middleware(['auth'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});