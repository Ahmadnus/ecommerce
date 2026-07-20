<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    CartController, OrderController, ProductController, WishlistController,
    PageController, ShippingApiController, CheckoutController, ProfileController
};
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\{
    DashboardController, CategoryController, HeroBannerController, TopHeroMediaController, SettingController,
    AnnouncementController, AttributeController, AttributeValueController, CheckoutSettingsController, HomeSectionController, SiteFeatureController,
    HomepageSectionController,
    CountryController, ZoneController,
    SocialLinkController,
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
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\FooterTextController;
use App\Http\Controllers\Admin\AdminPasswordController;
use Illuminate\Support\Facades\DB;


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

// ═══════════════════════════════════════════════════════════════════════════
// AUTH (login / register / password reset / OTP) — DEDUPLICATED
// ═══════════════════════════════════════════════════════════════════════════

// ── Guest-only auth routes (login / register) ─────────────────────────────
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

// ── Admin-only login portal ────────────────────────────────────────────────
Route::middleware('admin.route.only')->group(function () {
    Route::get('/adlogin',  [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/adlogin', [AuthController::class, 'login']);
});

// ── OTP verification ─────────────────────────────────────────────────────
Route::get('/verify-otp',  [AuthController::class, 'showVerifyOtp'])->name('otp.verify');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.submit');
Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('otp.resend');

// ── Logout ───────────────────────────────────────────────────────────────
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
    Route::get('zones/{country}', [ShippingApiController::class, 'zones'])->name('zones');
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
    // ── Customization pricing settings ──────────────────────────────────────
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
    // Direct entry to the merchant's Jordanian COD shipping matrix
    Route::get('shipping', [\App\Http\Controllers\Admin\ShippingMatrixController::class, 'index'])->name('shipping.index');
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
// ADMIN LOCALE MODE
// ═══════════════════════════════════════════════════════════════════════════

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/locale-mode', [\App\Http\Controllers\Admin\LocaleModeController::class, 'index'])
        ->name('admin.locale-mode');
    Route::post('/admin/locale-mode', [\App\Http\Controllers\Admin\LocaleModeController::class, 'update'])
        ->name('admin.locale-mode.update');
});


// ═══════════════════════════════════════════════════════════════════════════
// CURRENCY SWITCHING
// ═══════════════════════════════════════════════════════════════════════════

Route::post('/set-user-currency', function (Illuminate\Http\Request $request) {
    // Unified session key: the whole engine reads session('currency_code')
    // via CurrencyService (the old 'display_currency' key was read by nothing).
    app(\App\Services\CurrencyService::class)->switchTo((string) $request->currency_code);
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

Route::post('/language/switch', function (\Illuminate\Http\Request $request) {
    $globalMode = \App\Models\Setting::where('key', 'langsetting')->value('value') ?? 'both';

    if ($globalMode === 'both') {
        $locale = $request->input('locale');
        if (in_array($locale, ['ar', 'en'])) {
            session(['locale' => $locale]);
        }
    }
    return back();
})->name('language.switch')->middleware('web');
