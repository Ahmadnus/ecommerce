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


Route::prefix('admin')->name('admin.')->group(function () {
    
    // الصفحة الرئيسية للوحة التحكم
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD المنتجات كامل (index, create, store, edit, update, destroy)
    Route::resource('products', admin::class);

    // CRUD التصنيفات كامل (هذا هو السطر الذي ينقصك)
    Route::resource('categories', CategoryController::class);

    // عرض المستخدمين (غالباً عرض فقط في البداية)
  

    // الإعدادات
    Route::get('settings', [SettingController::class, 'index'])->name('settings');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

});