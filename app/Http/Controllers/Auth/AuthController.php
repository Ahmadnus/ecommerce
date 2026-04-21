<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Propaganistas\LaravelPhone\Rules\Phone as PhoneRule;

class AuthController extends Controller
{
    // تم تعديل الرقم هنا ليتطابق مع الرقم الذي أنشأناه في Tinker
    private string $bypassPhone = '962790000000';

    public function __construct(private SmsService $sms) {}

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
        $phone = $request->session()->get('otp_phone_display', '');
        return view('auth.verify-otp', compact('phone'));
    }

    // ── Login ──────────────────────────────────────────────────────────────────

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'phone_full' => ['required', 'string', new PhoneRule],
            'password'   => ['required', 'string'],
        ], [
            'phone_full.required' => 'رقم الهاتف مطلوب.',
            'phone_full.phone'    => 'رقم الهاتف غير صحيح.',
            'password.required'  => 'كلمة المرور مطلوبة.',
        ]);

        $phone       = $request->input('phone_full');
        $throttleKey = 'login|' . $phone . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors(['phone_full' => "محاولات كثيرة. انتظر {$seconds} ثانية."]);
        }

        $user = User::where('phone', $phone)->first();

        // فحص البيانات (رقم الهاتف وكلمة المرور)
        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors(['phone_full' => 'بيانات الدخول غير صحيحة.'])
                         ->withInput(['phone_full' => $phone]);
        }

        RateLimiter::clear($throttleKey);

        // --- التعديل الجوهري هنا ---
        // إذا كان الدخول من صفحة الإدارة (is_admin_login == 1)
        if ($request->input('is_admin_login') == '1') {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();
            
            // تحويله مباشرة للوحة التحكم
            return redirect()->to('/admin');
        }
        // ---------------------------

        // إذا لم يكن دخول إدارة، نطبق نظام الـ OTP العادي للمستخدمين
        try {
            $this->sms->sendOtp($user);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['phone_full' => $e->getMessage()]);
        }

        $request->session()->put([
            'otp_user_id'       => $user->id,
            'otp_phone_display' => $phone,
            'otp_intent'        => 'user',
            'otp_remember'      => $request->boolean('remember'),
        ]);

        return redirect()->route('otp.verify');
    }

    // ── OTP ────────────────────────────────────────────────────────────────────

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate(['otp' => ['required', 'string', 'min:4', 'max:10']],
                           ['otp.required' => 'رمز التحقق مطلوب.']);

        $userId = $request->session()->get('otp_user_id');
        $intent = $request->session()->get('otp_intent', 'user');

        if (!$userId) return redirect()->route('login');

        $user = User::find($userId);
        if (!$user) return redirect()->route('login');

        // حماية إضافية للرقم المستثنى
        $isBypass = ($user->phone === $this->bypassPhone);

        if (!$isBypass && !$this->sms->verifyOtp($user, $request->input('otp'))) {
            return back()->withErrors(['otp' => 'رمز التحقق غير صحيح أو منتهي الصلاحية.']);
        }

        $remember = (bool) $request->session()->pull('otp_remember', false);
        $request->session()->forget(['otp_user_id', 'otp_phone_display', 'otp_intent']);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        if ($intent === 'admin' && !$user->hasRole('admin')) {
            Auth::logout();
            return redirect()->route('admin.login')
                             ->withErrors(['phone_full' => 'هذه البوابة مخصصة للمسؤولين فقط.']);
        }

        return $user->hasRole('admin')
            ? redirect()->to('/admin')
            : redirect()->intended('/');
    }
    public function resendOtp(Request $request): RedirectResponse
    {
        $userId      = $request->session()->get('otp_user_id');
        $throttleKey = 'otp_resend|' . $userId;

        if (!$userId) return redirect()->route('login');

        if (RateLimiter::tooManyAttempts($throttleKey, 1)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors(['otp' => "انتظر {$seconds} ثانية قبل إعادة الإرسال."]);
        }

        $user = User::find($userId);
        if (!$user) return redirect()->route('login');

        // لا داعي لإعادة إرسال إذا كان يوزر متخطى (بالأساس لن يصل هنا)
        if ($user->phone === $this->bypassPhone) return back();

        try {
            $this->sms->sendOtp($user);
            RateLimiter::hit($throttleKey, 60);
        } catch (\RuntimeException $e) {
            return back()->withErrors(['otp' => $e->getMessage()]);
        }

        return back()->with('success', 'تم إعادة إرسال رمز التحقق.');
    }

    // ── Register ───────────────────────────────────────────────────────────────

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'phone_full' => ['required', 'string', 'unique:users,phone', new PhoneRule],
            'country_id' => [],
            'password'   => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required'       => 'الاسم الكامل مطلوب.',
            'phone_full.required' => 'رقم الهاتف مطلوب.',
            'phone_full.unique'   => 'رقم الهاتف هذا مسجل مسبقاً.',
            'phone_full.phone'    => 'رقم الهاتف غير صحيح.',
            'country_id.required' => 'الدولة مطلوبة.',
            'password.required'   => 'كلمة المرور مطلوبة.',
            'password.min'        => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.confirmed'  => 'كلمتا المرور غير متطابقتين.',
        ]);

        $user = User::create([
            'name'       => $validated['name'],
            'phone'      => $validated['phone_full'],
       
            'password'   => Hash::make($validated['password']),
        ]);

        // إذا كان رقم التسجيل هو نفسه رقم التخطي، سجله دخول فوراً
        if ($validated['phone_full'] === $this->bypassPhone) {
            Auth::login($user);
            return redirect()->intended('/');
        }

        try { $this->sms->sendOtp($user); } catch (\RuntimeException) {}

        $request->session()->put([
            'otp_user_id'       => $user->id,
            'otp_phone_display' => $validated['phone_full'],
            'otp_intent'        => 'user',
            'otp_remember'      => false,
        ]);

        return redirect()->route('otp.verify')
                         ->with('success', 'تم إنشاء حسابك! أدخل رمز التحقق المرسل لهاتفك.');
    }

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
}