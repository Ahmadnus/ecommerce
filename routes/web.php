<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    CartController,
    CheckoutController,
    ContactController,
    CustomizableProductsController,
    CustomizationController,
    OrderController,
    PageController,
    ProductController,
    ProfileController,
    WishlistController
};
use App\Http\Controllers\Auth\{AuthController, ForgotPasswordController, ResetPasswordController};
use App\Http\Controllers\Rental\{BookingController, FleetController};

use App\Http\Controllers\Admin\{
    AdminPasswordController,
    AnnouncementController,
    AttributeController,
    AttributeValueController,
    BookingController as AdminBookingController,
    CategoryController,
    CheckoutSettingsController,
    ContactMessageController,
    CountryController,
    CurrencyController as AdminCurrencyController,
    CustomizationPricingSettingsController,
    DashboardController,
    FooterCompanyInfoController,
    FooterTextController,
    HeroBannerController,
    HomeSectionController,
    HomepageSectionController,
    OrderController as AdminOrderController,
    OrderCustomizationController,
    PageController as AdminPageController,
    ProductController as AdminProductController,
    RentalLocationController,
    ReviewController,
    SeoSettingController,
    SettingController,
    ShippingApiController,
    SiteFeatureController,
    SmsSettingsController,
    SocialLinkController,
    SplashSettingsController,
    TopHeroMediaController,
    UserController,
    VehicleCategoryController,
    VehicleController,
    ZoneController
};

use Illuminate\Support\Facades\DB;

// ═══════════════════════════════════════════════════════════════════════════
// CAR RENTAL STOREFRONT
// ═══════════════════════════════════════════════════════════════════════════

Route::get('/',            [FleetController::class, 'home'])->name('rental.home');
Route::get('/cars',        [FleetController::class, 'index'])->name('rental.fleet');
Route::get('/branches',    [FleetController::class, 'branches'])->name('rental.branches');
Route::get('/cars/{slug}', [FleetController::class, 'show'])->name('rental.vehicles.show');

// ── Booking flow ────────────────────────────────────────────────────────────
Route::get('/book/{slug}',  [BookingController::class, 'create'])->name('rental.booking.create');
Route::post('/book/{slug}', [BookingController::class, 'store'])->name('rental.booking.store');
Route::get('/booking/confirmed/{reference}', [BookingController::class, 'success'])
    ->name('rental.booking.success');

// ── Manage Booking (public reference + phone lookup) ────────────────────────
Route::get('/manage-booking',  [BookingController::class, 'manage'])->name('rental.booking.manage');
Route::post('/manage-booking', [BookingController::class, 'lookup'])->name('rental.booking.lookup');
Route::post('/manage-booking/{reference}/cancel', [BookingController::class, 'cancel'])
    ->name('rental.booking.cancel');

Route::get('/my-bookings', [BookingController::class, 'myBookings'])
    ->middleware('auth')->name('rental.my-bookings');


// ═══════════════════════════════════════════════════════════════════════════
// CONTENT PAGES
// ═══════════════════════════════════════════════════════════════════════════

Route::get('/p/{slug}', [PageController::class, 'show'])->name('pages.show');


// ═══════════════════════════════════════════════════════════════════════════
// LEGACY E-COMMERCE STOREFRONT
// ═══════════════════════════════════════════════════════════════════════════
//
// The demo build keeps the car-rental UI on '/', but the legacy store routes
// are registered again so the restored admin dashboard (and every legacy
// Blade view it renders) can resolve route names like products.show,
// cart.index or orders.index without throwing.

Route::get('/products',        [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::post('/products/{product:slug}/reviews', [\App\Http\Controllers\ProductReviewController::class, 'store'])
    ->name('products.reviews.store');

// ── Cart ────────────────────────────────────────────────────────────────────
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/',                    [CartController::class, 'index'])->name('index');
    Route::post('/add',                [CartController::class, 'add'])->name('add');
    Route::patch('/update',            [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{itemKey}', [CartController::class, 'remove'])->name('remove');
    Route::get('/count',               [CartController::class, 'count'])->name('count');
});

// ── Checkout ────────────────────────────────────────────────────────────────
Route::middleware('guest.checkout')->group(function () {
    Route::get('/checkout',  [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
});

Route::get('/checkout/select-zone',   [CheckoutController::class, 'selectZone'])->name('checkout.select-zone');
Route::post('/checkout/confirm-zone', [CheckoutController::class, 'confirmZone'])->name('checkout.confirm-zone');

Route::get('/api/shipping/zones/{country}', [CheckoutController::class, 'zonesForCountry'])
    ->name('checkout.zones-for-country');

Route::prefix('api/shipping')->name('api.shipping.')->group(function () {
    Route::get('countries',     [ShippingApiController::class, 'countries'])->name('countries');
    Route::get('zones/{country}', [ShippingApiController::class, 'zones'])->name('zones');
});

// ── Orders (customer) ───────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/wishlist',                    [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle/{product}',  [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::get('/orders/{orderNumber}/select-city',  [OrderController::class, 'selectCity'])->name('orders.selectCity');
    Route::post('/orders/{orderNumber}/update-city', [OrderController::class, 'updateCity'])->name('orders.updateCity');

    Route::get('/orders',             [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/show/{order}', [OrderController::class, 'show'])->name('orders.show');
});

Route::get('/orders/success/{orderNumber}', [OrderController::class, 'success'])->name('orders.success');

// ── Garment customization ───────────────────────────────────────────────────
Route::prefix('customize')->name('customize.')->group(function () {
    Route::get('/',          [CustomizableProductsController::class, 'index'])->name('index');
    Route::get('/{garment}', [CustomizationController::class, 'show'])
         ->name('show')->where('garment', '[a-zA-Z0-9_-]+');
    Route::post('/{garment}', [CustomizationController::class, 'store'])
         ->name('store')->where('garment', '[a-zA-Z0-9_-]+');
});

Route::get('/contact',  [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');


// ═══════════════════════════════════════════════════════════════════════════
// AUTH (login / register / password reset / OTP)
// ═══════════════════════════════════════════════════════════════════════════

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

// ── OTP ─────────────────────────────────────────────────────────────────────
Route::get('/verify-otp',  [AuthController::class, 'showVerifyOtp'])->name('otp.verify');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.submit');
Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('otp.resend');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


// ═══════════════════════════════════════════════════════════════════════════
// AUTHENTICATED CUSTOMER
// ═══════════════════════════════════════════════════════════════════════════

Route::middleware('auth')->group(function () {
    Route::get('/myprofile', [ProfileController::class, 'show'])->name('myprofile.show');
    Route::put('/myprofile', [ProfileController::class, 'update'])->name('myprofile.update');
});


// ═══════════════════════════════════════════════════════════════════════════
// MAIN ADMIN ROUTE GROUP
// ═══════════════════════════════════════════════════════════════════════════

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ── Legacy store: catalogue ─────────────────────────────────────────────
    Route::resource('categories', CategoryController::class);
    Route::resource('products', AdminProductController::class);
    Route::patch('products/{product}/stock', [AdminProductController::class, 'updateStock'])
        ->name('products.stock');
    Route::resource('attributes', AttributeController::class);
    Route::resource('attribute-values', AttributeValueController::class);
    Route::resource('home-sections', HomeSectionController::class);
    Route::post('home-sections/reorder', [HomeSectionController::class, 'reorder'])
        ->name('home-sections.reorder');

    // ── Legacy store: orders ────────────────────────────────────────────────
    Route::get('/orders',                  [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}',          [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // ── Legacy store: reviews ───────────────────────────────────────────────
    Route::get(   'reviews',                    [ReviewController::class, 'index'])     ->name('reviews.index');
    Route::get(   'reviews/{review}',           [ReviewController::class, 'show'])      ->name('reviews.show');
    Route::patch( 'reviews/{review}/approve',   [ReviewController::class, 'approve'])   ->name('reviews.approve');
    Route::patch( 'reviews/{review}/reject',    [ReviewController::class, 'reject'])    ->name('reviews.reject');
    Route::patch( 'reviews/{review}/pin',       [ReviewController::class, 'pin'])       ->name('reviews.pin');
    Route::delete('reviews/{review}',           [ReviewController::class, 'destroy'])   ->name('reviews.destroy');
    Route::get(   'products/{product}/reviews', [ReviewController::class, 'forProduct'])->name('products.reviews');

    // ── Legacy store: garment customization orders ──────────────────────────
    Route::get('/customizations',                 [OrderCustomizationController::class, 'index'])
        ->name('customizations.index');
    Route::get('/customizations/{customization}',  [OrderCustomizationController::class, 'show'])
        ->name('customizations.show');
    Route::get('/orders/{orderId}/customization',  [OrderCustomizationController::class, 'embedded'])
        ->name('orders.customization.show');

    // ── Legacy store: checkout + customization pricing settings ─────────────
    Route::get('settings/checkout',  [CheckoutSettingsController::class, 'show'])
        ->name('settings.checkout');
    Route::post('settings/checkout', [CheckoutSettingsController::class, 'update'])
        ->name('settings.checkout.update');

    Route::get('settings/customization-pricing',
        [CustomizationPricingSettingsController::class, 'edit'])->name('settings.customization-pricing.edit');
    Route::put('settings/customization-pricing',
        [CustomizationPricingSettingsController::class, 'update'])->name('settings.customization-pricing.update');

    // ── Fleet, bookings, branches, vehicle classes ──────────────────────────
    Route::resource('vehicles', VehicleController::class)->except(['show']);
    Route::patch('vehicles/{vehicle}/status', [VehicleController::class, 'updateStatus'])
        ->name('vehicles.status');

    Route::resource('vehicle-categories', VehicleCategoryController::class)->except(['show']);

    Route::resource('locations', RentalLocationController::class)
        ->except(['show'])
        ->parameters(['locations' => 'location']);

    Route::get('bookings',           [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::put('bookings/{booking}', [AdminBookingController::class, 'update'])->name('bookings.update');
    Route::patch('bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])
        ->name('bookings.status');
    Route::delete('bookings/{booking}', [AdminBookingController::class, 'destroy'])
        ->name('bookings.destroy');

    // ── Site content ────────────────────────────────────────────────────────
    Route::resource('announcements', AnnouncementController::class);
    Route::resource('hero-banners', HeroBannerController::class);
    Route::resource('top-hero-media', TopHeroMediaController::class)->except(['show', 'create', 'edit']);
    Route::resource('homepage-sections', HomepageSectionController::class)->except(['show', 'create', 'edit']);

    // ── Settings ────────────────────────────────────────────────────────────
    Route::get('settings',  [SettingController::class, 'index'])->name('settings');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

    Route::resource('social-links', SocialLinkController::class);
    Route::resource('site-features', SiteFeatureController::class);
    Route::resource('pages', AdminPageController::class);
    Route::resource('countries', CountryController::class);
    Route::resource('currencies', AdminCurrencyController::class);
    Route::get('users', [UserController::class, 'index'])->name('users.index');

    Route::get('settings/typography',  [\App\Http\Controllers\Admin\TypographySettingsController::class, 'index'])
         ->name('settings.typography');
    Route::post('settings/typography', [\App\Http\Controllers\Admin\TypographySettingsController::class, 'update'])
         ->name('settings.typography.update');

    // ── SMS settings ─────────────────────────────────────────────────────────
    Route::get('settings/sms',       [SmsSettingsController::class, 'show'])->name('settings.sms');
    Route::post('settings/sms',      [SmsSettingsController::class, 'update'])->name('settings.sms.update');
    Route::post('settings/sms/test', [SmsSettingsController::class, 'test'])->name('settings.sms.test');

    // ── SEO ──────────────────────────────────────────────────────────────────
    Route::get('seo', [SeoSettingController::class, 'index'])->name('seo.index');
    Route::get('seo/{type}/edit', [SeoSettingController::class, 'edit'])->name('seo.edit');
    Route::put('seo/{type}', [SeoSettingController::class, 'update'])->name('seo.update');

    // ── Footer ───────────────────────────────────────────────────────────────
    Route::resource('footer-company', FooterCompanyInfoController::class)
        ->except(['show'])
        ->parameters(['footer-company' => 'footerCompanyInfo']);

    Route::get('/footer-texts',                 [FooterTextController::class, 'index'])->name('footer-texts.index');
    Route::post('/footer-texts',                [FooterTextController::class, 'store'])->name('footer-texts.store');
    Route::delete('/footer-texts/{footerText}', [FooterTextController::class, 'destroy'])->name('footer-texts.destroy');

    // ── Admin password ───────────────────────────────────────────────────────
    Route::get('/password', [AdminPasswordController::class, 'edit'])->name('password.edit');
    Route::put('/password', [AdminPasswordController::class, 'update'])->name('password.update');

    // ── Zones + monthly delivery schedules ───────────────────────────────────
    Route::resource('countries.zones', ZoneController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('zones/{zone}/schedules',                       [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'index'])    ->name('zones.schedules.index');
    Route::get('zones/{zone}/schedules/create',                [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'create'])   ->name('zones.schedules.create');
    Route::post('zones/{zone}/schedules',                      [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'store'])    ->name('zones.schedules.store');
    Route::get('zones/{zone}/schedules/{schedule}/edit',       [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'edit'])     ->name('zones.schedules.edit');
    Route::put('zones/{zone}/schedules/{schedule}',            [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'update'])   ->name('zones.schedules.update');
    Route::delete('zones/{zone}/schedules/{schedule}',         [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'destroy'])  ->name('zones.schedules.destroy');
    Route::post('zones/{zone}/schedules/{schedule}/duplicate', [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'duplicate'])->name('zones.schedules.duplicate');

    // ── Contact messages ─────────────────────────────────────────────────────
    Route::get('/contact-messages',                        [ContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::get('/contact-messages/{contactMessage}',       [ContactMessageController::class, 'show'])->name('contact-messages.show');
    Route::delete('/contact-messages/{contactMessage}',    [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
    Route::patch('/contact-messages/{contactMessage}/read',[ContactMessageController::class, 'markRead'])->name('contact-messages.read');
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

Route::get('/admin/locale-mode',  [\App\Http\Controllers\Admin\LocaleModeController::class, 'index'])
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
    if (\App\Models\Currency::active()->where('code', $code)->exists()) {
        session(['currency_code' => strtoupper($code)]);
    }

    return back();
})->name('currency.user.switch');


// ═══════════════════════════════════════════════════════════════════════════
// LANGUAGE SWITCHING
// ═══════════════════════════════════════════════════════════════════════════

/*
 * GET is accepted alongside POST on purpose. The switcher posts a form, but a
 * bookmark, a shared link, a back-button restore or a browser prefetch can all
 * re-issue the URL as a plain GET — which used to 405. Switching the display
 * language changes nothing but a session value, so there is no state worth
 * protecting with a CSRF token here.
 */
Route::match(['get', 'post'], '/language/switch', function (\Illuminate\Http\Request $request) {
    $globalMode = DB::table('settings')->where('key', 'langsetting')->value('value') ?? 'both';

    if ($globalMode === 'both') {
        $locale = $request->input('locale');

        if (in_array($locale, ['ar', 'en'], true)) {
            session(['locale' => $locale]);
        }
    }

    // A direct hit has no referer, so back() would bounce to the same URL and
    // loop. Fall back to the homepage in that case.
    return $request->headers->has('referer') ? back() : redirect()->route('rental.home');
})->name('language.switch')->middleware('web');
