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
 * Registration (Session Based):
 * • Data stored in Session. DB is NOT touched until OTP is verified.
 * * Login (DB Based):
 * • Regular user → OTP based on existing stored user.
 */
class AuthController extends Controller
{
    private string $bypassPhone = '962790000000';
    private int $emailOtpLength = 4;
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
        // التحقق من وجود جلسة صالحة (سواء للتسجيل أو تسجيل الدخول)
        if (!$request->session()->has('otp_intent')) {
            return redirect()->route('login');
        }

        $phone   = $request->session()->get('otp_phone_display', '');
        $email   = $request->session()->get('otp_email_display', '');
        $channel = $request->session()->get('otp_channel', 'sms');

        return view('auth.verify-otp', compact('phone', 'email', 'channel'));
    }

    // ─── Login ────────────────────────────────────────────────────────────────

    public function login(Request $request): RedirectResponse
    {
        $identity    = trim($request->input('phone_full') ?? $request->input('identity') ?? '');
        $isEmail     = $this->looksLikeEmail($identity);
        $isPhone     = !$isEmail;

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

        if ($request->input('is_admin_login') == '1') {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->to('/admin');
        }

        // إرسال الـ OTP للمستخدم الحالي
        try {
            $channel = $this->sendOtpToExistingUser($user, $isEmail ? 'email' : 'sms');
        } catch (\RuntimeException $e) {
            return back()->withErrors([$errorField => $e->getMessage()]);
        }

        $request->session()->put([
            'otp_user_id'       => $user->id,
            'otp_phone_display' => $isPhone ? $identity : '',
            'otp_email_display' => $isEmail ? $identity : '',
            'otp_channel'       => $channel,
            'otp_intent'        => 'login', // تحديد النية كـ تسجيل دخول
            'otp_remember'      => $request->boolean('remember'),
        ]);

        return redirect()->route('otp.verify');
    }

    // ─── Register (SESSION BASED - NO DB INSERT) ──────────────────────────────

    public function register(Request $request): RedirectResponse
    {
        $hasPhone = $request->filled('phone_full');
        $hasEmail = $request->filled('email');

        if (!$hasPhone && !$hasEmail) {
            return back()->withErrors(['identity' => 'يرجى إدخال رقم الهاتف أو البريد الإلكتروني.'])->withInput();
        }

        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        if ($hasPhone) {
            $rules['phone_full'] = ['required', 'string', 'unique:users,phone', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'];
            $rules['country_id'] = ['nullable', 'exists:countries,id'];
        }
        if ($hasEmail) {
            $rules['email'] = ['required', 'email', 'unique:users,email', 'max:255'];
        }

        $validated = $request->validate($rules);

        // إنشاء الرمز وتخزينه في الجلسة بدلاً من قاعدة البيانات
        $channel = $hasPhone ? 'sms' : 'email';
        $otpCode = $this->generateNumericOtp($this->emailOtpLength);
        
        $pendingUser = [
            'name'       => $validated['name'],
            'phone'      => $hasPhone ? ($validated['phone_full'] ?? null) : null,
            'email'      => $hasEmail ? ($validated['email'] ?? null) : null,
            'country_id' => $validated['country_id'] ?? null,
            'password'   => Hash::make($validated['password']),
        ];

        // التحقق من رقم الـ Bypass
        if ($hasPhone && $pendingUser['phone'] === $this->bypassPhone) {
            $user = User::create($pendingUser); // حفظ في قاعدة البيانات لأن الرقم معفي
            Auth::login($user);
            return redirect()->intended('/');
        }

        try {
            // إرسال الرمز للزائر الجديد (بدون حفظ في DB)
            $this->sendOtpToNewUser($pendingUser, $otpCode, $channel);
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['identity' => 'خطأ في الإرسال: ' . $e->getMessage()]);
        }

        // تخزين كل شيء في الـ Session
        $request->session()->put([
            'pending_user'      => $pendingUser,
            'otp_code'          => $otpCode,
            'otp_expires_at'    => now()->addMinutes($this->emailOtpTtl),
            'otp_phone_display' => $hasPhone ? $pendingUser['phone'] : '',
            'otp_email_display' => $hasEmail ? $pendingUser['email'] : '',
            'otp_channel'       => $channel,
            'otp_intent'        => 'register', // تحديد النية كـ تسجيل جديد
            'otp_remember'      => false,
        ]);

        return redirect()->route('otp.verify')->with('success', 'أدخل رمز التحقق المرسل إليك لإتمام التسجيل.');
    }

    // ─── OTP verification (Handles both Login & Register) ─────────────────────

    public function verifyOtp(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate(
            ['otp' => ['required', 'string', 'min:4', 'max:10']],
            ['otp.required' => 'رمز التحقق مطلوب.']
        );

        $intent  = $request->session()->get('otp_intent');
        $channel = $request->session()->get('otp_channel', 'sms');
        $submittedOtp = $request->input('otp');

        // 1. مسار تسجيل الدخول (يوجد يوزر في قاعدة البيانات)
        if ($intent === 'login') {
            $userId = $request->session()->get('otp_user_id');
            $user = User::find($userId);

            if (!$user) return $this->otpSessionExpiredResponse($request);

            $isBypass = $user->phone === $this->bypassPhone;
            $verified = $isBypass || $this->sms->verifyOtp($user, $submittedOtp);

            if (!$verified) {
                return $this->invalidOtpResponse($request);
            }

            return $this->finalizeLogin($user, $request);
        }

        // 2. مسار التسجيل الجديد (لا يوجد يوزر في قاعدة البيانات بعد)
        if ($intent === 'register') {
            $pendingUser = $request->session()->get('pending_user');
            $expectedOtp = $request->session()->get('otp_code');
            $expiresAt   = $request->session()->get('otp_expires_at');

            if (!$pendingUser || !$expectedOtp || now()->greaterThan($expiresAt)) {
                return $this->otpSessionExpiredResponse($request);
            }

            if ($submittedOtp !== (string)$expectedOtp) {
                return $this->invalidOtpResponse($request);
            }

            // تمت المصادقة بنجاح! الآن فقط نقوم بالـ Insert في قاعدة البيانات
            $user = User::create($pendingUser);

            return $this->finalizeLogin($user, $request);
        }

        return $this->otpSessionExpiredResponse($request);
    }

    // ─── Resend OTP ───────────────────────────────────────────────────────────

    public function resendOtp(Request $request): RedirectResponse
    {
        $intent  = $request->session()->get('otp_intent');
        $channel = $request->session()->get('otp_channel', 'sms');
        
        $throttleKey = 'otp_resend|' . request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 1)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors(['otp' => "انتظر {$seconds} ثانية قبل إعادة الإرسال."]);
        }

        try {
            if ($intent === 'login') {
                $userId = $request->session()->get('otp_user_id');
                $user = User::find($userId);
                if (!$user) return redirect()->route('login');
                $this->sendOtpToExistingUser($user, $channel);

            } elseif ($intent === 'register') {
                $pendingUser = $request->session()->get('pending_user');
                if (!$pendingUser) return redirect()->route('register');
                
                $otpCode = $this->generateNumericOtp($this->emailOtpLength);
                $request->session()->put('otp_code', $otpCode);
                $request->session()->put('otp_expires_at', now()->addMinutes($this->emailOtpTtl));
                
                $this->sendOtpToNewUser($pendingUser, $otpCode, $channel);
            }
            
            RateLimiter::hit($throttleKey, 60);
        } catch (\Exception $e) {
            return back()->withErrors(['otp' => $e->getMessage()]);
        }

        return back()->with('success', 'تم إعادة إرسال رمز التحقق.');
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

    private function finalizeLogin(User $user, Request $request)
    {
        $remember = $request->session()->get('otp_remember', false);
        Auth::login($user, $remember);
        
        $request->session()->regenerate();
        $request->session()->save();
        
        // تنظيف كل جلسات الـ OTP
        $request->session()->forget(['otp_user_id', 'otp_phone_display', 'otp_email_display', 'otp_channel', 'otp_intent', 'pending_user', 'otp_code', 'otp_expires_at']);

        $redirectTo = $user->hasRole('admin') ? '/admin' : '/';

        return $request->expectsJson()
            ? response()->json(['verified' => true, 'redirect' => $redirectTo, 'status' => 200])
            : redirect()->intended($redirectTo);
    }

    private function invalidOtpResponse(Request $request)
    {
        $error = 'رمز التحقق غير صحيح أو منتهي الصلاحية.';
        return $request->expectsJson()
            ? response()->json(['verified' => false, 'message' => $error, 'error' => 'otp_invalid', 'status' => 422], 422)
            : back()->withErrors(['otp' => $error]);
    }

    private function otpSessionExpiredResponse(Request $request): RedirectResponse|JsonResponse
    {
        $msg = 'انتهت جلسة التحقق. يرجى تسجيل الدخول أو التسجيل مجدداً.';
        return $request->expectsJson()
            ? response()->json(['verified' => false, 'message' => $msg, 'error' => 'session_expired', 'status' => 401], 401)
            : redirect()->route('login')->withErrors(['phone_full' => $msg]);
    }

    private function looksLikeEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function generateNumericOtp(int $length = 4): string
    {
        $min = (int) str_pad('1', $length, '0');
        $max = (int) str_repeat('9', $length);
        return (string) random_int($min, $max);
    }

    // إرسال OTP لمستخدم موجود (تسجيل الدخول)
    private function sendOtpToExistingUser(User $user, string $channel): string
    {
        if ($channel === 'email') {
            $otp = $this->generateNumericOtp($this->emailOtpLength);
            $user->forceFill([
                'otp'            => $otp,
                'otp_expires_at' => now()->addMinutes($this->emailOtpTtl),
            ])->saveQuietly();

            Mail::to($user->email)->send(new OtpMail($user, $otp, $this->emailOtpTtl));
            return 'email';
        }

        $this->sms->sendOtp($user);
        return 'sms';
    }

    // إرسال OTP لشخص لم يتم حفظه في قاعدة البيانات بعد (التسجيل)
    private function sendOtpToNewUser(array $pendingUser, string $otp, string $channel): void
    {
        // ننشئ نسخة وهمية من الموديل فقط لكي نقدر نمررها للكلاسات الأخرى بدون حفظها بالـ DB
        $dummyUser = new User($pendingUser);
        $dummyUser->otp = $otp; 

        if ($channel === 'email') {
            Mail::to($pendingUser['email'])->send(new OtpMail($dummyUser, $otp, $this->emailOtpTtl));
        } else {
            // نمرر الموديل الوهمي لخدمة الـ SMS
            $this->sms->sendOtp($dummyUser);
        }
    }
}