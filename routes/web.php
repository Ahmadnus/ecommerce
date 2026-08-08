<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{ContactController, PageController, ProfileController};
use App\Http\Controllers\Auth\{AuthController, ForgotPasswordController, ResetPasswordController};
use App\Http\Controllers\Rental\{BookingController, FleetController};

use App\Http\Controllers\Admin\{
    AdminPasswordController,
    AnnouncementController,
    BookingController as AdminBookingController,
    ContactMessageController,
    CountryController,
    CurrencyController as AdminCurrencyController,
    DashboardController,
    FooterCompanyInfoController,
    FooterTextController,
    HeroBannerController,
    HomepageSectionController,
    PageController as AdminPageController,
    RentalLocationController,
    SeoSettingController,
    SettingController,
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

    // The parameter is named explicitly: the default would be {social_link},
    // which does not match the $socialLink argument the controller type-hints,
    // so implicit route-model binding would never resolve.
    Route::resource('social-links', SocialLinkController::class)
        ->parameters(['social-links' => 'socialLink']);
    // Enable/disable a link, or promote it to the floating contact button.
    Route::patch('social-links/{socialLink}/toggle', [SocialLinkController::class, 'toggle'])
        ->name('social-links.toggle');
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

Route::post('/language/switch', function (\Illuminate\Http\Request $request) {
    $globalMode = DB::table('settings')->where('key', 'langsetting')->value('value') ?? 'both';

    if ($globalMode === 'both') {
        $locale = $request->input('locale');

        if (in_array($locale, ['ar', 'en'], true)) {
            session(['locale' => $locale]);
        }
    }

    return back();
})->name('language.switch')->middleware('web');
