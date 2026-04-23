<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\Country;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

/**
 * AuthController — Dual Authentication (Phone SMS + Email OTP)
 * ─────────────────────────────────────────────────────────────────────────────
 * Registration:
 *   • Phone provided → SMS OTP via SmsService
 *   • Email provided → Email OTP via OtpMail / Gmail SMTP
 *
 * Login:
 *   • Admin portal (/adlogin) → direct login, no OTP
 *   • Regular user → OTP based on which identifier they have stored
 *
 * No external phone library — native regex validation only.
 */
class AuthController extends Controller
{
    /** Dev bypass phone — skips OTP. Remove in production. */
    private string $bypassPhone = '962790000000';

    /** OTP code length for email (SMS length comes from OtpSetting) */
    private int $emailOtpLength = 4;

    /** Minutes an email OTP stays valid */
    private int $emailOtpTtl = 5;

    public function __construct(private SmsService $sms) {}

    // ─── Show pages ───────────────────────────────────────────────────────────

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showAdminLogin(): View
    {
        return view('auth.admin-login');
    }

    public function showRegister(): View
    {
        $countries = Country::active()->ordered()->get();
        return view('auth.register', compact('countries'));
    }

    public function showVerifyOtp(Request $request): View|RedirectResponse
    {
        if (!$request->session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        $phone   = $request->session()->get('otp_phone_display', '');
        $email   = $request->session()->get('otp_email_display', '');
        $channel = $request->session()->get('otp_channel', 'sms'); // 'sms' | 'email'

        return view('auth.verify-otp', compact('phone', 'email', 'channel'));
    }

    // ─── Login ────────────────────────────────────────────────────────────────

    public function login(Request $request): RedirectResponse
    {
        // Determine identifier type from the submitted value
        $identity    = trim($request->input('phone_full') ?? $request->input('identity') ?? '');
        $isEmail     = $this->looksLikeEmail($identity);
        $isPhone     = !$isEmail;

        // Validate
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if ($isEmail) {
            $request->validate(
                ['identity' => ['required', 'email']],
                ['identity.email' => 'البريد الإلكتروني غير صحيح.']
            );
        } else {
            $request->validate(
                ['phone_full' => ['required', 'string']],
                ['phone_full.required' => 'رقم الهاتف مطلوب.']
            );
        }

        $throttleKey = 'login|' . $identity . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $field   = $isEmail ? 'identity' : 'phone_full';
            return back()->withErrors([$field => "محاولات كثيرة. انتظر {$seconds} ثانية."]);
        }

        // Find user
        $user = $isEmail
            ? User::where('email', $identity)->first()
            : User::where('phone', $identity)->first();

        $errorField = $isEmail ? 'identity' : 'phone_full';

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            return back()
                ->withErrors([$errorField => 'بيانات الدخول غير صحيحة.'])
                ->withInput(['phone_full' => $identity, 'identity' => $identity]);
        }

        RateLimiter::clear($throttleKey);

        // ── Admin portal: direct login, no OTP ─────────────────────────────
        if ($request->input('is_admin_login') == '1') {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->to('/admin');
        }

        // ── Regular user: send OTP ─────────────────────────────────────────
        try {
            $channel = $this->sendOtp($user, $isEmail ? 'email' : 'sms');
        } catch (\RuntimeException $e) {
            return back()->withErrors([$errorField => $e->getMessage()]);
        }

        $request->session()->put([
            'otp_user_id'        => $user->id,
            'otp_phone_display'  => $isPhone ? $identity : '',
            'otp_email_display'  => $isEmail ? $identity : '',
            'otp_channel'        => $channel,
            'otp_intent'         => 'user',
            'otp_remember'       => $request->boolean('remember'),
        ]);

        return redirect()->route('otp.verify');
    }

    // ─── OTP verification ─────────────────────────────────────────────────────

    public function verifyOtp(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate(
            ['otp' => ['required', 'string', 'min:4', 'max:10']],
            ['otp.required' => 'رمز التحقق مطلوب.']
        );

        $userId  = $request->session()->get('otp_user_id');
        $intent  = $request->session()->get('otp_intent', 'user');
        $channel = $request->session()->get('otp_channel', 'sms');

        if (!$userId) {
            return $this->otpSessionExpiredResponse($request);
        }

        $user = User::find($userId);

        if (!$user) {
            return $request->expectsJson()
                ? response()->json(['verified' => false, 'error' => 'user_not_found', 'status' => 404], 404)
                : redirect()->route('login');
        }

        // ── Bypass (dev account) ───────────────────────────────────────────
        $isBypass = $user->phone === $this->bypassPhone;

        // ── Verify based on channel ────────────────────────────────────────
        $verified = $isBypass || $this->checkOtp($user, $channel, $request->input('otp'));

        if (!$verified) {
            $error = 'رمز التحقق غير صحيح أو منتهي الصلاحية.';

            return $request->expectsJson()
                ? response()->json(['verified' => false, 'message' => $error, 'error' => 'otp_invalid', 'status' => 422], 422)
                : back()->withErrors(['otp' => $error]);
        }

        // ── Success — clear session and log in ─────────────────────────────
      $remember = $request->session()->get('otp_remember', false);

// 2. تسجيل الدخول الفعلي
Auth::login($user, $remember);

// 3. تحديث الجلسة فوراً
$request->session()->regenerate();
$request->session()->save(); // <--- هام جداً لضمان حفظ الجلسة قبل الـ Redirect

// 4. تنظيف بيانات الـ OTP
$request->session()->forget(['otp_user_id', 'otp_phone_display', 'otp_email_display', 'otp_channel', 'otp_intent']);

// 5. التوجيه الذكي
if ($user->hasRole('admin')) {
    return redirect()->intended('/admin');
}
return redirect()->intended('/');
        $redirectTo = $user->hasRole('admin') ? '/admin' : '/';

        return $request->expectsJson()
            ? response()->json(['verified' => true, 'redirect' => $redirectTo, 'status' => 200])
            : redirect()->to($redirectTo);
    }

    // ─── Resend OTP ───────────────────────────────────────────────────────────

    public function resendOtp(Request $request): RedirectResponse
    {
        $userId      = $request->session()->get('otp_user_id');
        $channel     = $request->session()->get('otp_channel', 'sms');
        $throttleKey = 'otp_resend|' . $userId;

        if (!$userId) return redirect()->route('login');

        if (RateLimiter::tooManyAttempts($throttleKey, 1)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors(['otp' => "انتظر {$seconds} ثانية قبل إعادة الإرسال."]);
        }

        $user = User::find($userId);
        if (!$user) return redirect()->route('login');

        if ($user->phone === $this->bypassPhone) return back();

        try {
            $this->sendOtp($user, $channel);
            RateLimiter::hit($throttleKey, 60);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['otp' => $e->getMessage()]);
        }

        return back()->with('success', 'تم إعادة إرسال رمز التحقق.');
    }

    // ─── Register ─────────────────────────────────────────────────────────────

    public function register(Request $request): RedirectResponse
    {
        // Detect which identifier the user filled in
        $hasPhone = $request->filled('phone_full');
        $hasEmail = $request->filled('email');

        // At least one must be provided
        if (!$hasPhone && !$hasEmail) {
            return back()
                ->withErrors(['identity' => 'يرجى إدخال رقم الهاتف أو البريد الإلكتروني.'])
                ->withInput();
        }

        // ── Build validation rules dynamically ────────────────────────────
        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        $messages = [
            'name.required'        => 'الاسم الكامل مطلوب.',
            'password.required'    => 'كلمة المرور مطلوبة.',
            'password.min'         => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.confirmed'   => 'كلمتا المرور غير متطابقتين.',
        ];

        if ($hasPhone) {
            $rules['phone_full'] = [
                'required', 'string',
                'unique:users,phone',
                'regex:/^\+?[0-9\s\-\(\)]{7,20}$/',
            ];
            $messages['phone_full.unique']  = 'رقم الهاتف هذا مسجل مسبقاً.';
            $messages['phone_full.regex']   = 'رقم الهاتف غير صحيح.';
            $rules['country_id'] = ['nullable', 'exists:countries,id'];
        }

        if ($hasEmail) {
            $rules['email'] = ['required', 'email', 'unique:users,email', 'max:255'];
            $messages['email.unique'] = 'البريد الإلكتروني هذا مسجل مسبقاً.';
            $messages['email.email']  = 'البريد الإلكتروني غير صحيح.';
        }

        $validated = $request->validate($rules, $messages);

        // ── Create user ───────────────────────────────────────────────────
        $user = User::create([
            'name'       => $validated['name'],
            'phone'      => $hasPhone ? ($validated['phone_full'] ?? null) : null,
            'email'      => $hasEmail ? ($validated['email'] ?? null) : null,
            'country_id' => $validated['country_id'] ?? null,
            'password'   => Hash::make($validated['password']),
        ]);

        // ── Dev bypass: log in immediately ────────────────────────────────
        if ($hasPhone && $validated['phone_full'] === $this->bypassPhone) {
            Auth::login($user);
            return redirect()->intended('/');
        }

        // ── Send OTP via the appropriate channel ──────────────────────────
        $channel = $hasPhone ? 'sms' : 'email';

        try {
            $this->sendOtp($user, $channel);
        } catch (\RuntimeException $e) {
            // Registration succeeded — OTP sending failed.
            // Store the session and show the OTP page anyway (user can resend).
        }

        $request->session()->put([
            'otp_user_id'       => $user->id,
            'otp_phone_display' => $hasPhone ? ($validated['phone_full'] ?? '') : '',
            'otp_email_display' => $hasEmail ? ($validated['email'] ?? '') : '',
            'otp_channel'       => $channel,
            'otp_intent'        => 'user',
            'otp_remember'      => false,
        ]);

        return redirect()->route('otp.verify')
                         ->with('success', 'تم إنشاء حسابك! أدخل رمز التحقق المرسل إليك.');
    }

    // ─── Logout ───────────────────────────────────────────────────────────────

    public function logout(Request $request): RedirectResponse
    {
        $isAdmin = Auth::check() && Auth::user()->hasRole('admin');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $isAdmin
            ? redirect()->route('admin.login')->with('success', 'تم تسجيل خروج المسؤول.')
            : redirect()->route('login')->with('success', 'تم تسجيل الخروج.');
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    /**
     * Detect if a string looks like an email address.
     */
    private function looksLikeEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Generate a numeric OTP of the given length.
     */
    private function generateNumericOtp(int $length = 4): string
    {
        $min = (int) str_pad('1', $length, '0');
        $max = (int) str_repeat('9', $length);
        return (string) random_int($min, $max);
    }

    /**
     * Send an OTP via the specified channel and persist it on the user.
     * Returns the channel used ('sms' | 'email').
     *
     * @throws \RuntimeException if delivery fails
     */
    private function sendOtp(User $user, string $channel): string
    {
        if ($channel === 'email') {
            return $this->sendEmailOtp($user);
        }

        // SMS channel — delegate to SmsService (unchanged from existing system)
        $this->sms->sendOtp($user);
        return 'sms';
    }

    /**
     * Generate, persist, and mail an email OTP.
     * Stores the hashed OTP directly on the user row.
     */
    private function sendEmailOtp(User $user): string
    {
        $otp       = $this->generateNumericOtp($this->emailOtpLength);
        $expiresAt = now()->addMinutes($this->emailOtpTtl);

        // Reuse the same otp / otp_expires_at columns as the SMS flow
        $user->forceFill([
            'otp'            => $otp,
            'otp_expires_at' => $expiresAt,
        ])->saveQuietly();

        try {
            Mail::to($user->email)->send(new OtpMail($user, $otp, $this->emailOtpTtl));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('OtpMail send failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
            throw new \RuntimeException('فشل إرسال رمز التحقق على البريد الإلكتروني. يرجى المحاولة مرة أخرى.');
        }

        return 'email';
    }

    /**
     * Verify the submitted OTP against the stored value.
     * Works for both SMS and email channels (both use the same DB columns).
     */
    private function checkOtp(User $user, string $channel, string $submitted): bool
    {
        // Both channels store OTP the same way — SMS flow handled by SmsService,
        // email flow handled here. verifyOtp() from SmsService works for both
        // because they share the same otp / otp_expires_at columns.
        return $this->sms->verifyOtp($user, $submitted);
    }

    /**
     * Unified "session expired" response for both JSON and redirect.
     */
    private function otpSessionExpiredResponse(Request $request): RedirectResponse|JsonResponse
    {
        $msg = 'انتهت جلسة التحقق. يرجى تسجيل الدخول مجدداً.';

        return $request->expectsJson()
            ? response()->json(['verified' => false, 'message' => $msg, 'error' => 'session_expired', 'status' => 401], 401)
            : redirect()->route('login')->withErrors(['phone_full' => $msg]);
    }
}