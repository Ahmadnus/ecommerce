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
    AnnouncementController, AttributeController, AttributeValueController, HomeSectionController, SiteFeatureController,
    CountryController, ZoneController, CurrencyController,
    ProductController as AdminProductController,
    OrderController as AdminOrderController,
    PageController as AdminPageController,
    SmsSettingsController,
    SplashSettingsController
};

// ─── Public Routes ──────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('splash.splash');
});



// ── Standard user auth (blocked for logged-in admins/users) ───────────────────
Route::middleware('user.route.only')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// ── Admin-only login portal ────────────────────────────────────────────────────
Route::middleware('admin.route.only')->group(function () {
    Route::get('/adlogin',  [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/adlogin', [AuthController::class, 'login']);
});

// ── OTP ────────────────────────────────────────────────────────────────────────
Route::get('/verify-otp',  [AuthController::class, 'showVerifyOtp'])->name('otp.verify');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.submit');
Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('otp.resend');

// ── Logout ─────────────────────────────────────────────────────────────────────
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Admin SMS Settings (add inside your admin route group) ─────────────────────
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('settings/sms',       [SmsSettingsController::class, 'show'])->name('admin.settings.sms');
    Route::post('settings/sms',      [SmsSettingsController::class, 'update'])->name('admin.settings.sms.update');
    Route::post('settings/sms/test', [SmsSettingsController::class, 'test'])->name('admin.settings.sms.test');
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
Route::prefix('admin/splash')->name('admin.splash.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [SplashSettingsController::class, 'edit'])->name('edit');
    Route::put('/update', [SplashSettingsController::class, 'update'])->name('update');
});



use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
Route::get('/test-sms-raw', function () {
    $url = "https://gwjo1s.broadnet.me:8443/websmpp/websms?user=JbuyApp1&pass=429J@NewY&sid=Jbuy.App&mno=962782237460&type=4&text=test_raw";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // تجاهل الشهادة
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // تجاهل المضيف
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);    // مهلة الاتصال 10 ثواني
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);           // مهلة التنفيذ 20 ثانية
    
    // أهم سطرين لحل مشكلة Windows Handshake
    curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return "❌ Error: " . $error;
    }

    return "✅ Response: " . $response;
});
Route::resource('attributes', AttributeController::class)->names('admin.attributes');
Route::resource('attribute-values', AttributeValueController::class)->names('admin.attribute-values');