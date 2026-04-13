<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\ShippingApiController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\ProfileController;

// ─── Public Routes ──────────────────────────────────────────────────────────
//Route::get('/', fn() => redirect()->route('products.index'))->name('home');
Route::get('/', function () {
    return view('splash.splash');
});

Route::middleware('auth')->group(function () {
    Route::get('/myprofile', [ProfileController::class, 'show'])->name('myprofile.show');
    Route::put('/myprofile', [ProfileController::class, 'update'])->name('myprofile.update');
});
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// ─── Cart Routes (Unified) ────────────────────────────────────────────────────
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/update', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{itemKey}', [CartController::class, 'remove'])->name('remove');
    Route::get('/count', [CartController::class, 'count'])->name('count');
});

// ─── Auth Routes ─────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
});

// ─── Authenticated Routes ─────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.place');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/show/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/success/{orderNumber}', [OrderController::class, 'success'])->name('orders.success');
});

// ─── Admin Routes ─────────────────────────────────────────────────────────────

Route::get('/p/{slug}', [PageController::class, 'show'])->name('pages.show');
 
 Route::prefix('api/shipping')->name('api.shipping.')->group(function () {
    Route::get('countries',       [ShippingApiController::class, 'countries'])->name('countries');
    Route::get('zones/{country}', [ShippingApiController::class, 'zones'])->name('zones');
});
// ─── Admin pages routes (inside your existing auth middleware group) ──────────
// Add these lines inside: Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(...)
// المجموعة الأب: تسمح للـ Super Admin والـ Admin بالدخول (عشان الداشبورد والمنتجات)
Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // 1. مسارات مشتركة (متاحة للطرفين)
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', AdminProductController::class);
    Route::resource('categories', CategoryController::class);

    // 2. مسارات محصورة "فقط" بالسوبر أدمن (Super Admin Only)
    Route::middleware(['role:admin'])->group(function () {
        
        // الطلبات
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
        
        // الصفحات والدول والعملات والمناطق
        Route::resource('pages', AdminPageController::class);
        Route::resource('countries', CountryController::class);
        Route::resource('currencies', CurrencyController::class);
        Route::resource('countries.zones', ZoneController::class)->only(['index', 'store', 'update', 'destroy']);

        // الإعدادات العامة
        Route::get('settings', [SettingController::class, 'index'])->name('settings');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    });
});