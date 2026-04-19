<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CartController, OrderController, ProductController, WishlistController,
    PageController, ShippingApiController, CheckoutController, ProfileController,
    SocialLinkController
};
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\{
    DashboardController, CategoryController, HeroBannerController, SettingController,
    AnnouncementController, HomeSectionController, SiteFeatureController,
    CountryController, ZoneController, CurrencyController,
    ProductController as AdminProductController,
    OrderController as AdminOrderController,
    PageController as AdminPageController
};

// ─── Public Routes ──────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('splash.splash');
});

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/p/{slug}', [PageController::class, 'show'])->name('pages.show');

// ─── Cart Routes ────────────────────────────────────────────────────────────
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
    
    // Profile
    Route::get('/myprofile', [ProfileController::class, 'show'])->name('myprofile.show');
    Route::put('/myprofile', [ProfileController::class, 'update'])->name('myprofile.update');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Checkout & Order Flow
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
    
    // 1. مسار اختيار المدينة بعد الطلب (الجديد)
    Route::get('/orders/{orderNumber}/select-city', [OrderController::class, 'selectCity'])->name('orders.selectCity');
    Route::post('/orders/{orderNumber}/update-city', [OrderController::class, 'updateCity'])->name('orders.updateCity');

    // Orders Details
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/show/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/success/{orderNumber}', [OrderController::class, 'success'])->name('orders.success');
});

// ─── API Routes (Shipping) ────────────────────────────────────────────────────
Route::prefix('api/shipping')->name('api.shipping.')->group(function () {
    Route::get('countries', [ShippingApiController::class, 'countries'])->name('countries');
    Route::get('zones/{country}', [ShippingApiController::class, 'zones'])->name('zones');
});

// ─── Admin Routes ─────────────────────────────────────────────────────────────
Route::get('/adlogin', [AuthController::class, 'showAdminLogin'])->name('admin.login');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // المحتوى والمنتجات
    Route::resource('categories', CategoryController::class);
    Route::resource('products', AdminProductController::class);
    Route::patch('products/{product}/stock', [AdminProductController::class, 'updateStock'])->name('products.stock');
    Route::resource('announcements', AnnouncementController::class);
    Route::resource('hero-banners', HeroBannerController::class);
    Route::resource('home-sections', HomeSectionController::class);
    Route::post('home-sections/reorder', [HomeSectionController::class, 'reorder'])->name('home-sections.reorder');

    // الطلبات (مع عرض التفاصيل واللون/القياس)
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // الإعدادات المتقدمة
    Route::get('settings', [SettingController::class, 'index'])->name('settings');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::resource('social-links', SocialLinkController::class);
    Route::resource('site-features', SiteFeatureController::class);
    Route::resource('pages', AdminPageController::class);
    Route::resource('countries', CountryController::class);
    Route::resource('currencies', CurrencyController::class);
    Route::resource('countries.zones', ZoneController::class)->only(['index', 'store', 'update', 'destroy']);
});
Route::post('/set-user-currency', function (Illuminate\Http\Request $request) {
    session(['display_currency' => $request->currency_code]);
    return back();
})->name('currency.switch');
Route::get('/select-currency/{code}', function ($code) {
    // 1. نتأكد أن العملة موجودة ومفعلة
    $exists = \App\Models\Currency::active()->where('code', $code)->exists();
    
    if ($exists) {
        // 2. نخزنها في السيشين (بنفس الاسم الذي تستخدمه في السيرفس)
        session(['currency_code' => strtoupper($code)]);
    }

    return back();
})->name('currency.user.switch');