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
    AnnouncementController, AttributeController, AttributeValueController, CheckoutSettingsController, HomeSectionController, SiteFeatureController,
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


// ─── Public Routes ──────────────────────────────────────────────────────────
Route::get('/', function () {
    return view('splash.splash');
});

// ── CHECKOUT (replaces your existing checkout routes) ─────────────────────────
//
// BEFORE (your current routes):
//   Route::middleware('auth')->group(function () {
//       Route::get('/checkout', ...)->name('checkout.index');
//       Route::post('/checkout', ...)->name('checkout.place');
//   });
//
// AFTER: swap 'auth' for 'guest.checkout'
Route::middleware('guest.checkout')->group(function () {
    Route::get('/checkout',  [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
});
 
// Zone-selection routes (no auth required)
Route::get('/checkout/select-zone',  [CheckoutController::class, 'selectZone'])->name('checkout.select-zone');
Route::post('/checkout/confirm-zone',[CheckoutController::class, 'confirmZone'])->name('checkout.confirm-zone');
 
// Zones API (called by JS — no auth required)
Route::get('/api/shipping/zones/{country}', [CheckoutController::class, 'zonesForCountry'])
     ->name('checkout.zones');
 
// ── ADMIN: Checkout settings ──────────────────────────────────────────────────
// Add inside your existing admin middleware group:
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('settings/checkout',  [CheckoutSettingsController::class, 'show'])
         ->name('admin.settings.checkout');
    Route::post('settings/checkout', [CheckoutSettingsController::class, 'update'])
         ->name('admin.settings.checkout.update');
});
 use App\Http\Controllers\Auth\RegisterController; // تأكد من مسار الكنترولر عندك

// صفحة عرض واجهة إدخال الرمز
Route::get('/verify-otp', [AuthController::class, 'showVerifyForm'])->name('otp.verify');

// معالجة الرمز المرسل من المستخدم
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.verify.submit');

// ── Standard user auth (blocked for logged-in admins/users) ───────────────────

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// Existing auth routes
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register']);
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

// Password Reset
Route::get('/forgot-password',  [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'send'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
Route::post('/reset-password',        [ResetPasswordController::class, 'reset'])->name('password.update');

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
   
    
    // 1. مسار اختيار المدينة بعد الطلب (الجديد)
    Route::get('/orders/{orderNumber}/select-city', [OrderController::class, 'selectCity'])->name('orders.selectCity');
    Route::post('/orders/{orderNumber}/update-city', [OrderController::class, 'updateCity'])->name('orders.updateCity');

    // Orders Details
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/show/{order}', [OrderController::class, 'show'])->name('orders.show');
  
});
  Route::get('/orders/success/{orderNumber}', [OrderController::class, 'success'])->name('orders.success');
// ─── API Routes (Shipping) ────────────────────────────────────────────────────
Route::prefix('api/shipping')->name('api.shipping.')->group(function () {
    Route::get('countries', [ShippingApiController::class, 'countries'])->name('countries');
    Route::get('zones/{country}', [ShippingApiController::class, 'zones'])->name('zones');
});

// ─── Admin Routes ─────────────────────────────────────────────────────────────
Route::get('/adlogin', [AuthController::class, 'showAdminLogin'])->name('admin.login');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
   Route::resource('attributes', AttributeController::class);
    Route::resource('attribute-values', AttributeValueController::class);
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
 Route::resource('currencies', AdminCurrencyController::class);
    Route::get('settings/typography',  [\App\Http\Controllers\Admin\TypographySettingsController::class, 'index'])
         ->name('settings.typography');
    Route::post('settings/typography', [\App\Http\Controllers\Admin\TypographySettingsController::class, 'update'])
         ->name('settings.typography.update');
   
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

    $url = "https://gwjo1s.broadnet.me:8443/websmpp/websms?" . http_build_query($params);

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

/*
|--------------------------------------------------------------------------
| Diagnostic / Debug Routes
|--------------------------------------------------------------------------
| Add these to routes/web.php TEMPORARILY during development.
| REMOVE BEFORE PRODUCTION — these routes expose credentials.
|
| Usage:
|   /debug-otp-chain        → shows what each layer resolves to
|   /debug-send-test-sms    → sends a real SMS to the hardcoded number
*/

use App\Models\OtpSetting;
use App\Services\SmsService;
use Illuminate\Support\Facades\DB;

// ── 1. Check the full resolution chain ────────────────────────────────────────
Route::get('/debug-otp-chain', function () {
    $keys = ['sms_url', 'sms_user', 'sms_pass', 'sms_sid', 'sms_type', 'otp_ttl_minutes', 'otp_length'];

    $result = [];
    foreach ($keys as $key) {
        $dbRaw     = OtpSetting::where('key', $key)->value('value');
        $resolved  = get_otp_setting($key);

        $result[$key] = [
            'db_raw'     => $dbRaw,        // null = using config fallback
            'resolved'   => $key === 'sms_pass' ? (empty($resolved) ? '(empty)' : '***hidden***') : $resolved,
            'source'     => (!is_null($dbRaw) && $dbRaw !== '') ? 'database' : 'config/sms.php',
        ];
    }

    return response()->json($result, 200, ['Content-Type' => 'application/json']);
})->middleware('auth'); // Protect with auth — remove if testing before login

// ── 2. Send a real test SMS ────────────────────────────────────────────────────
Route::get('/debug-send-test-sms', function (SmsService $sms) {
    $testNumber = '962799400020'; // ← change to your own number for testing

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
            // pass intentionally omitted
        ],
    ]);
})->middleware('auth');


Route::post('/language/switch', function (\Illuminate\Http\Request $request) {
    $locale = $request->input('locale');
    if (in_array($locale, ['ar', 'en'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('language.switch');

// Admin input mode switcher (NEW)
// Stored separately from the storefront locale — admins need both languages
// available regardless of what the storefront is displaying.
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
Route::get('/admin/locale-mode', [\App\Http\Controllers\Admin\LocaleModeController::class, 'index'])
    ->name('admin.locale-mode');

Route::post('/admin/locale-mode', [\App\Http\Controllers\Admin\LocaleModeController::class, 'update'])
    ->name('admin.locale-mode.update');


    use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\ContactMessageController;

Route::get('/contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::get('/contact-messages/{contactMessage}', [ContactMessageController::class, 'show'])->name('contact-messages.show');
    Route::delete('/contact-messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
    Route::patch('/contact-messages/{contactMessage}/read', [ContactMessageController::class, 'markRead'])->name('contact-messages.read');
});

use App\Http\Controllers\Admin\FooterTextController;

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/footer-texts', [FooterTextController::class, 'index'])->name('footer-texts.index');
    Route::post('/footer-texts', [FooterTextController::class, 'store'])->name('footer-texts.store');
    Route::delete('/footer-texts/{footerText}', [FooterTextController::class, 'destroy'])->name('footer-texts.destroy');
});

// In routes/web.php — inside your admin middleware group

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    // SEO Settings — type-based (main / splash)
    Route::get('seo', [SeoSettingController::class, 'index'])
        ->name('seo.index');
    Route::get('seo/{type}/edit', [SeoSettingController::class, 'edit'])
        ->name('seo.edit');
    Route::put('seo/{type}', [SeoSettingController::class, 'update'])
        ->name('seo.update');

    // Footer Company Info CRUD
   Route::resource('footer-company', FooterCompanyInfoController::class)
    ->except(['show'])
    ->parameters(['footer-company' => 'footerCompanyInfo']); // ← matches $footerCompanyInfo
});

use App\Http\Controllers\Admin\AdminPasswordController;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/password', [AdminPasswordController::class, 'edit'])->name('password.edit');
    Route::put('/password', [AdminPasswordController::class, 'update'])->name('password.update');
});



// Paste these two lines inside your existing admin middleware group in routes/web.php
// Example location:
//
//   Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
//       ...your other settings routes...
//
//       Route::get('/settings/typography',  [\App\Http\Controllers\Admin\TypographySettingsController::class, 'index'])->name('settings.typography');
//       Route::post('/settings/typography', [\App\Http\Controllers\Admin\TypographySettingsController::class, 'update'])->name('settings.typography.update');
//   });

Route::get('/settings/typography',  [\App\Http\Controllers\Admin\TypographySettingsController::class, 'index'])
     ->name('settings.typography');

Route::post('/settings/typography', [\App\Http\Controllers\Admin\TypographySettingsController::class, 'update'])
     ->name('settings.typography.update');

  

/*
|--------------------------------------------------------------------------
| ADD TO routes/web.php — inside your admin middleware group
|--------------------------------------------------------------------------
|
| Place these after your existing zones routes.
|
*/

// Admin: Monthly delivery schedule management
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // ... all your existing routes ...

    Route::resource('countries.zones', ZoneController::class)->only(['index', 'store', 'update', 'destroy']);

    // ── ADD THESE 7 LINES ────────────────────────────────────────────────────
    Route::get('zones/{zone}/schedules',                       [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'index'])     ->name('zones.schedules.index');
    Route::get('zones/{zone}/schedules/create',                [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'create'])    ->name('zones.schedules.create');
    Route::post('zones/{zone}/schedules',                      [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'store'])     ->name('zones.schedules.store');
    Route::get('zones/{zone}/schedules/{schedule}/edit',       [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'edit'])      ->name('zones.schedules.edit');
    Route::put('zones/{zone}/schedules/{schedule}',            [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'update'])    ->name('zones.schedules.update');
    Route::delete('zones/{zone}/schedules/{schedule}',         [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'destroy'])   ->name('zones.schedules.destroy');
    Route::post('zones/{zone}/schedules/{schedule}/duplicate', [\App\Http\Controllers\Admin\ZoneScheduleController::class, 'duplicate'])->name('zones.schedules.duplicate');

});
/*
 * Because these are inside your admin group with ->name('admin.'), the full names are:
 *   admin.zones.schedules.index
 *   admin.zones.schedules.create
 *   admin.zones.schedules.store
 *   admin.zones.schedules.edit
 *   admin.zones.schedules.update
 *   admin.zones.schedules.destroy
 *   admin.zones.schedules.duplicate
 */


/*
|--------------------------------------------------------------------------
| ADD TO routes/api.php (if not already there)
|--------------------------------------------------------------------------
*/

Route::get('/shipping/zones/{country}', [\App\Http\Controllers\Api\ShippingZoneApiController::class, 'index'])
     ->name('api.shipping.zones');


/*
|--------------------------------------------------------------------------
| ALSO: Add a "Manage Schedules" link in admin/zones/index.blade.php
|--------------------------------------------------------------------------
|
| Inside the zone row actions, alongside the edit/delete buttons, add:
|
|   <a href="{{ route('admin.zones.schedules.index', $zone) }}"
|      class="p-1.5 text-brand hover:bg-brand/10 rounded-lg transition-colors"
|      title="جداول التوصيل الشهرية">
|       <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
|           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
|                 d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
|       </svg>
|   </a>
*/




/*
|--------------------------------------------------------------------------
| ADD TO routes/web.php
|--------------------------------------------------------------------------
*/

// ── Public: submit a review (authenticated or guest) ─────────────────────────
// Add in the public routes section (no auth middleware needed):

Route::post('/products/{product:slug}/reviews', [\App\Http\Controllers\ProductReviewController::class, 'store'])
     ->name('products.reviews.store');


// ── Admin: review management ──────────────────────────────────────────────────
// Add INSIDE your admin middleware group:
//
//   Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(...):
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // ... your existing routes ...

    // ── Product Reviews ───────────────────────────────────────────────────────
    Route::get(   'reviews',                   [\App\Http\Controllers\Admin\ReviewController::class, 'index'])     ->name('reviews.index');
    Route::get(   'reviews/{review}',          [\App\Http\Controllers\Admin\ReviewController::class, 'show'])      ->name('reviews.show');
    Route::patch( 'reviews/{review}/approve',  [\App\Http\Controllers\Admin\ReviewController::class, 'approve'])   ->name('reviews.approve');
    Route::patch( 'reviews/{review}/reject',   [\App\Http\Controllers\Admin\ReviewController::class, 'reject'])    ->name('reviews.reject');
    Route::patch( 'reviews/{review}/pin',      [\App\Http\Controllers\Admin\ReviewController::class, 'pin'])       ->name('reviews.pin');
    Route::delete('reviews/{review}',          [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])   ->name('reviews.destroy');
    Route::get(   'products/{product}/reviews',[\App\Http\Controllers\Admin\ReviewController::class, 'forProduct'])->name('products.reviews');

});

Route::prefix('customize')->name('customize.')->group(function () {
 
    // Product picker listing
    Route::get('/', [CustomizableProductsController::class, 'index'])
         ->name('index');
 
    // Customization UI — accepts slug OR numeric ID
    // {garment} is a plain string parameter, not model-bound
    Route::get('/{garment}', [CustomizationController::class, 'show'])
         ->name('show')
         ->where('garment', '[a-zA-Z0-9_-]+');  // allows slugs and IDs
 
    // Save customization
    Route::post('/{garment}', [CustomizationController::class, 'store'])
         ->name('store')
         ->where('garment', '[a-zA-Z0-9_-]+');
 
});
 
// ── Admin routes (add inside your existing admin middleware group) ────────────
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/customizations', [OrderCustomizationController::class, 'index'])
        ->name('customizations.index');

    Route::get('/customizations/{customization}', [OrderCustomizationController::class, 'show'])
        ->name('customizations.show');

    Route::get('/orders/{orderId}/customization', [OrderCustomizationController::class, 'embedded'])
        ->name('orders.customization.show');
});