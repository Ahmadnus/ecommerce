<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CartController, OrderController, ProductController, WishlistController,
    PageController, ShippingApiController, CheckoutController, ProfileController,
    SocialLinkController
};
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\{
    DashboardController, CategoryController, HeroBannerController, TopHeroMediaController, SettingController,
    AnnouncementController, AttributeController, AttributeValueController, CheckoutSettingsController, HomeSectionController, SiteFeatureController,
    HomepageSectionController,
    CountryController, ZoneController,
    CurrencyController as AdminCurrencyController,
    FooterCompanyInfoController,
    ProductController as AdminProductController,
    OrderController as AdminOrderController,
    PageController as AdminPageController,
    SeoSettingController,
    SmsSettingsController,
    SplashSettingsController
};

use App\Http\Controllers\CustomizableProductsController;
use App\Http\Controllers\CustomizationController;
use App\Http\Controllers\Admin\OrderCustomizationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\FooterTextController;
use App\Http\Controllers\Admin\AdminPasswordController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\OtpSetting;
use App\Services\SmsService;


// ═══════════════════════════════════════════════════════════════════════════
// PUBLIC ROUTES
// ═══════════════════════════════════════════════════════════════════════════

Route::get('/', function () {
    return view('splash.splash');
});

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/p/{slug}', [PageController::class, 'show'])->name('pages.show');

Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::post('/products/{product:slug}/reviews', [\App\Http\Controllers\ProductReviewController::class, 'store'])
     ->name('products.reviews.store');


// ═══════════════════════════════════════════════════════════════════════════
// CHECKOUT
// ═══════════════════════════════════════════════════════════════════════════

Route::middleware('guest.checkout')->group(function () {
    Route::get('/checkout',  [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
});

// Zone-selection routes (no auth required)
Route::get('/checkout/select-zone',  [CheckoutController::class, 'selectZone'])->name('checkout.select-zone');
Route::post('/checkout/confirm-zone',[CheckoutController::class, 'confirmZone'])->name('checkout.confirm-zone');

// Zones API for checkout: served by the api.shipping.zones route below.


// ═══════════════════════════════════════════════════════════════════════════
// AUTH (login / register / password reset / OTP) — DEDUPLICATED
// ═══════════════════════════════════════════════════════════════════════════

// ── Guest-only auth routes (login / register) ─────────────────────────────
// NOTE: this is the ONLY place '/login' and '/register' are registered.
// The previous file had a second, unguarded copy of these — removed.
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
});

// ── Password reset ──────────────────────────────────────────────────────────
Route::get('/forgot-password',  [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'send'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
Route::post('/reset-password',        [ResetPasswordController::class, 'reset'])->name('password.reset.update');
// NOTE: renamed from 'password.update' → 'password.reset.update'. Your
// original file used 'password.update' here AND for the separate admin
// "change my password" route below — same final name, two different
// controllers. Whichever was registered last silently won for any
// route('password.update') call. If anything in your Blade views calls
// route('password.update') expecting THIS reset-password action (not the
// admin one), update that call to route('password.reset.update').

// ── Admin-only login portal ────────────────────────────────────────────────
Route::middleware('admin.route.only')->group(function () {
    Route::get('/adlogin',  [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/adlogin', [AuthController::class, 'login']);
});

// ── OTP — registered ONCE (previous file had this block twice with
//    conflicting route names: otp.verify.submit vs otp.submit) ─────────────
Route::get('/verify-otp',  [AuthController::class, 'showVerifyOtp'])->name('otp.verify');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.submit');
Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('otp.resend');

// ── Logout — registered ONCE (previous file had this 3 times) ──────────────
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


// ═══════════════════════════════════════════════════════════════════════════
// CART
// ═══════════════════════════════════════════════════════════════════════════

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/update', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{itemKey}', [CartController::class, 'remove'])->name('remove');
    Route::get('/count', [CartController::class, 'count'])->name('count');
});


// ═══════════════════════════════════════════════════════════════════════════
// AUTHENTICATED USER ROUTES
// ═══════════════════════════════════════════════════════════════════════════

Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/myprofile', [ProfileController::class, 'show'])->name('myprofile.show');
    Route::put('/myprofile', [ProfileController::class, 'update'])->name('myprofile.update');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Order city selection (post-purchase)
    Route::get('/orders/{orderNumber}/select-city', [OrderController::class, 'selectCity'])->name('orders.selectCity');
    Route::post('/orders/{orderNumber}/update-city', [OrderController::class, 'updateCity'])->name('orders.updateCity');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/show/{order}', [OrderController::class, 'show'])->name('orders.show');

});

Route::get('/orders/success/{orderNumber}', [OrderController::class, 'success'])->name('orders.success');


// ═══════════════════════════════════════════════════════════════════════════
// API ROUTES (Shipping)
// ═══════════════════════════════════════════════════════════════════════════

Route::prefix('api/shipping')->name('api.shipping.')->group(function () {
    Route::get('countries', [ShippingApiController::class, 'countries'])->name('countries');
    // Rich payload (zones + monthly delivery schedules) consumed by the checkout JS.
    Route::get('zones/{country}', [\App\Http\Controllers\Api\ShippingZoneApiController::class, 'index'])->name('zones');
});


// ═══════════════════════════════════════════════════════════════════════════
// CUSTOMIZATION (customer-facing product designer)
// ═══════════════════════════════════════════════════════════════════════════

Route::prefix('customize')->name('customize.')->group(function () {

    Route::get('/', [CustomizableProductsController::class, 'index'])
         ->name('index');

    Route::get('/{garment}', [CustomizationController::class, 'show'])
         ->name('show')
         ->where('garment', '[a-zA-Z0-9_-]+');

    Route::post('/{garment}', [CustomizationController::class, 'store'])
         ->name('store')
         ->where('garment', '[a-zA-Z0-9_-]+');

});


// ═══════════════════════════════════════════════════════════════════════════
// MAIN ADMIN ROUTE GROUP
// ═══════════════════════════════════════════════════════════════════════════
//
// Every admin-only route in the app lives inside THIS group (or one of the
// smaller dedicated admin groups further down that share the same
// middleware/prefix/name pattern). This is the group that gives every
// route inside it the "admin." name prefix and "/admin" URL prefix.
//
// NOTE: '/adlogin' → name 'admin.login' is registered ONCE, above, inside
// the 'admin.route.only' middleware group. Your original file registered
// it a second time right here — removed, since both pointed at the exact
// same controller method and the same final route name.

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::resource('attributes', AttributeController::class);
    Route::resource('attribute-values', AttributeValueController::class);
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ── المحتوى والمنتجات ──────────────────────────────────────────────────
    Route::resource('categories', CategoryController::class);
    Route::resource('products', AdminProductController::class);
    Route::patch('products/{product}/stock', [AdminProductController::class, 'updateStock'])->name('products.stock');
    Route::resource('announcements', AnnouncementController::class);
    Route::resource('hero-banners', HeroBannerController::class);
    Route::resource('top-hero-media', TopHeroMediaController::class)->except(['show', 'create', 'edit']);
    Route::resource('home-sections', HomeSectionController::class);
    Route::post('home-sections/reorder', [HomeSectionController::class, 'reorder'])->name('home-sections.reorder');
    Route::resource('homepage-sections', HomepageSectionController::class)->except(['show', 'create', 'edit']);

    // ── الطلبات ─────────────────────────────────────────────────────────────
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // ── الإعدادات المتقدمة ──────────────────────────────────────────────────
    Route::get('settings', [SettingController::class, 'index'])->name('settings');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::resource('social-links', SocialLinkController::class);
    Route::resource('site-features', SiteFeatureController::class);
    Route::resource('pages', AdminPageController::class);
    Route::resource('countries', CountryController::class);
    Route::resource('currencies', AdminCurrencyController::class);

    Route::get('settings/typography',  [\App\Http\Controllers\Admin\TypographySettingsController::class, 'index'])
         ->name('settings.typography');
    Route::post('settings/typography', [\App\Http\Controllers\Admin\TypographySettingsController::class, 'update'])
         ->name('settings.typography.update');

    // ── Checkout settings ───────────────────────────────────────────────────
    Route::get('settings/checkout',  [CheckoutSettingsController::class, 'show'])
         ->name('settings.checkout');
    Route::post('settings/checkout', [CheckoutSettingsController::class, 'update'])
         ->name('settings.checkout.update');

    // ── SMS settings ─────────────────────────────────────────────────────────
    Route::get('settings/sms',       [SmsSettingsController::class, 'show'])->name('settings.sms');
    Route::post('settings/sms',      [SmsSettingsController::class, 'update'])->name('settings.sms.update');
    Route::post('settings/sms/test', [SmsSettingsController::class, 'test'])->name('settings.sms.test');

    // ── Customization pricing settings ──────────────────────────────────────
    // FIX: this used to be a standalone Route::prefix('settings') block
    // sitting OUTSIDE this admin group, registering as
    // "settings.customization-pricing.*" instead of
    // "admin.settings.customization-pricing.*" — which is what the
    // customization-pricing.blade.php view and its controller actually call.
    // Moved inside this group so the names now match.
    Route::get('settings/customization-pricing',
        [\App\Http\Controllers\Admin\CustomizationPricingSettingsController::class, 'edit'])
        ->name('settings.customization-pricing.edit');
    Route::put('settings/customization-pricing',
        [\App\Http\Controllers\Admin\CustomizationPricingSettingsController::class, 'update'])
        ->name('settings.customization-pricing.update');

    // ── SEO settings ─────────────────────────────────────────────────────────
    Route::get('seo', [SeoSettingController::class, 'index'])->name('seo.index');
    Route::get('seo/{type}/edit', [SeoSettingController::class, 'edit'])->name('seo.edit');
    Route::put('seo/{type}', [SeoSettingController::class, 'update'])->name('seo.update');

    // ── Footer company info ──────────────────────────────────────────────────
    Route::resource('footer-company', FooterCompanyInfoController::class)
        ->except(['show'])
        ->parameters(['footer-company' => 'footerCompanyInfo']);

    // ── Admin password ───────────────────────────────────────────────────────
    Route::get('/password', [AdminPasswordController::class, 'edit'])->name('password.edit');
    Route::put('/password', [AdminPasswordController::class, 'update'])->name('password.update');

    // ── Zones + monthly delivery schedules ───────────────────────────────────
    Route::resource('countries.zones', ZoneController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('zones/{zone}/schedules',                       [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'index'])     ->name('zones.schedules.index');
    Route::get('zones/{zone}/schedules/create',                [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'create'])    ->name('zones.schedules.create');
    Route::post('zones/{zone}/schedules',                      [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'store'])     ->name('zones.schedules.store');
    Route::get('zones/{zone}/schedules/{schedule}/edit',       [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'edit'])      ->name('zones.schedules.edit');
    Route::put('zones/{zone}/schedules/{schedule}',            [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'update'])    ->name('zones.schedules.update');
    Route::delete('zones/{zone}/schedules/{schedule}',         [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'destroy'])   ->name('zones.schedules.destroy');
    Route::post('zones/{zone}/schedules/{schedule}/duplicate', [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'duplicate'])->name('zones.schedules.duplicate');

    // ── Product reviews ──────────────────────────────────────────────────────
    Route::get(   'reviews',                   [\App\Http\Controllers\Admin\ReviewController::class, 'index'])     ->name('reviews.index');
    Route::get(   'reviews/{review}',          [\App\Http\Controllers\Admin\ReviewController::class, 'show'])      ->name('reviews.show');
    Route::patch( 'reviews/{review}/approve',  [\App\Http\Controllers\Admin\ReviewController::class, 'approve'])   ->name('reviews.approve');
    Route::patch( 'reviews/{review}/reject',   [\App\Http\Controllers\Admin\ReviewController::class, 'reject'])    ->name('reviews.reject');
    Route::patch( 'reviews/{review}/pin',      [\App\Http\Controllers\Admin\ReviewController::class, 'pin'])       ->name('reviews.pin');
    Route::delete('reviews/{review}',          [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])   ->name('reviews.destroy');
    Route::get(   'products/{product}/reviews',[\App\Http\Controllers\Admin\ReviewController::class, 'forProduct'])->name('products.reviews');

    // ── Garment customization orders ─────────────────────────────────────────
    Route::get('/customizations', [OrderCustomizationController::class, 'index'])
        ->name('customizations.index');
    Route::get('/customizations/{customization}', [OrderCustomizationController::class, 'show'])
        ->name('customizations.show');
    Route::get('/orders/{orderId}/customization', [OrderCustomizationController::class, 'embedded'])
        ->name('orders.customization.show');

    // ── Contact messages ─────────────────────────────────────────────────────
    Route::get('/contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::get('/contact-messages/{contactMessage}', [ContactMessageController::class, 'show'])->name('contact-messages.show');
    Route::delete('/contact-messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
    Route::patch('/contact-messages/{contactMessage}/read', [ContactMessageController::class, 'markRead'])->name('contact-messages.read');

    // ── Footer texts ──────────────────────────────────────────────────────────
    Route::get('/footer-texts', [FooterTextController::class, 'index'])->name('footer-texts.index');
    Route::post('/footer-texts', [FooterTextController::class, 'store'])->name('footer-texts.store');
    Route::delete('/footer-texts/{footerText}', [FooterTextController::class, 'destroy'])->name('footer-texts.destroy');

});


// ═══════════════════════════════════════════════════════════════════════════
// ADMIN SPLASH SETTINGS — separate group (own prefix/name pattern)
// ═══════════════════════════════════════════════════════════════════════════

Route::prefix('admin/splash')->name('admin.splash.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [SplashSettingsController::class, 'edit'])->name('edit');
    Route::put('/update', [SplashSettingsController::class, 'update'])->name('update');
});


// ═══════════════════════════════════════════════════════════════════════════
// ADMIN LOCALE MODE — standalone (kept as-is, not part of role:admin group
// in the original file, left unchanged to avoid altering its auth behavior)
// ═══════════════════════════════════════════════════════════════════════════

Route::get('/admin/locale-mode', [\App\Http\Controllers\Admin\LocaleModeController::class, 'index'])
    ->name('admin.locale-mode');
Route::post('/admin/locale-mode', [\App\Http\Controllers\Admin\LocaleModeController::class, 'update'])
    ->name('admin.locale-mode.update');


// ═══════════════════════════════════════════════════════════════════════════
// CURRENCY SWITCHING
// ═══════════════════════════════════════════════════════════════════════════

Route::post('/set-user-currency', function (Illuminate\Http\Request $request) {
    session(['display_currency' => $request->currency_code]);
    return back();
})->name('currency.switch');

Route::get('/select-currency/{code}', function ($code) {
    $exists = \App\Models\Currency::active()->where('code', $code)->exists();

    if ($exists) {
        session(['currency_code' => strtoupper($code)]);
    }

    return back();
})->name('currency.user.switch');


// ═══════════════════════════════════════════════════════════════════════════
// LANGUAGE SWITCHING — registered ONCE
// ═══════════════════════════════════════════════════════════════════════════
//
// FIX: the previous file defined POST /language/switch TWICE with the same
// name 'language.switch'. The second definition silently overwrote the
// first, making the first block's simpler logic permanently dead code.
// Keeping only the second (more complete) version, which respects the
// admin-controlled "langsetting" toggle.

Route::post('/language/switch', function (\Illuminate\Http\Request $request) {
    $globalMode = DB::table('settings')->where('key', 'langsetting')->value('value') ?? 'both';

    if ($globalMode === 'both') {
        $locale = $request->input('locale');
        if (in_array($locale, ['ar', 'en'])) {
            session(['locale' => $locale]);
        }
    }
    return back();
})->name('language.switch')->middleware('web');


// ═══════════════════════════════════════════════════════════════════════════
// SMS TEST / DEBUG ROUTES
// ⚠ REMOVE BEFORE PRODUCTION — these expose credentials and send real SMS.
// ═══════════════════════════════════════════════════════════════════════════

Route::get('/send-test-sms', function () {

    $number = "962799400020";
    $text   = "مرحبا";

    function normalize_msisdn($input) {
        $digits = preg_replace('/\D+/', '', $input);
        if (strpos($digits, '962') === 0) return $digits;
        if (strpos($digits, '0') === 0)   return '962' . substr($digits, 1);
        return $digits;
    }

    $params = [
        "user" => "JbuyApp1",
        "pass" => "429J@NewY",
        "sid"  => "Jbuy.App",
        "mno"  => normalize_msisdn($number),
        "type" => 4,
        "text" => $text,
    ];

    $ch = curl_init("https://gwjo1s.broadnet.me:8443/websmpp/websms");

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($params),
    ]);
    $response = curl_exec($ch);
    $err = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        "sent_to" => $number,
        "message" => $text,
        "response" => $response,
        "error" => $err,
        "status" => $code,
    ];
});

Route::get('/debug-otp-chain', function () {
    $keys = ['sms_url', 'sms_user', 'sms_pass', 'sms_sid', 'sms_type', 'otp_ttl_minutes', 'otp_length'];

    $result = [];
    foreach ($keys as $key) {
        $dbRaw     = OtpSetting::where('key', $key)->value('value');
        $resolved  = get_otp_setting($key);

        $result[$key] = [
            'db_raw'     => $dbRaw,
            'resolved'   => $key === 'sms_pass' ? (empty($resolved) ? '(empty)' : '***hidden***') : $resolved,
            'source'     => (!is_null($dbRaw) && $dbRaw !== '') ? 'database' : 'config/sms.php',
        ];
    }

    return response()->json($result, 200, ['Content-Type' => 'application/json']);
})->middleware('auth');

Route::get('/debug-send-test-sms', function (SmsService $sms) {
    $testNumber = '962799400020';

    $result = $sms->send($testNumber, 'اختبار النظام — إذا وصلتك هذه الرسالة فالنظام يعمل ✓');

    return response()->json([
        'sent_to'   => $testNumber,
        'success'   => $result['success'],
        'response'  => $result['response'],
        'resolved_credentials' => [
            'url'  => get_otp_setting('sms_url'),
            'user' => get_otp_setting('sms_user'),
            'sid'  => get_otp_setting('sms_sid'),
            'type' => get_otp_setting('sms_type'),
        ],
    ]);
})->middleware('auth');