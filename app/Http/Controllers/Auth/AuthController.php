<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Propaganistas\LaravelPhone\Rules\Phone as PhoneRule;

class AuthController extends Controller
{
    // ─── Display pages ────────────────────────────────────────────────────────

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
        return view('auth.register');
    }

    // ─── Login ────────────────────────────────────────────────────────────────

    public function login(Request $request): RedirectResponse
    {
        /*
         * The hidden input `phone_full` carries the E.164 value from intl-tel-input
         * (e.g. +9639xxxxxxxx). We validate that field, then use it as the credential.
         */
        $request->validate([
            'phone_full' => [
                'required',
                'string',
                new PhoneRule,          // propaganistas/laravel-phone — validates E.164 / any international
            ],
            'password'   => ['required', 'string'],
        ], [
            'phone_full.required' => 'رقم الهاتف مطلوب.',
            'phone_full.phone'    => 'رقم الهاتف غير صحيح أو غير مدعوم.',
            'password.required'  => 'كلمة المرور مطلوبة.',
        ]);

        $phone       = $request->input('phone_full');   // E.164 e.g. +9639...
        $throttleKey = 'login|' . $phone . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors(['phone_full' => "محاولات كثيرة. انتظر {$seconds} ثانية."]);
        }

        // Attempt login using the E.164 phone as identifier
        $credentials = [
            'phone'    => $phone,
            'password' => $request->input('password'),
        ];

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors(['phone_full' => 'بيانات الدخول غير صحيحة.'])->withInput(['phone_full' => $phone]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();
        $user = Auth::user();

        // Admin portal check
        if ($request->boolean('is_admin_login') && !$user->hasRole('admin')) {
            Auth::logout();
            return back()->withErrors(['phone_full' => 'هذه البوابة مخصصة للمسؤولين فقط.']);
        }

        if ($user->hasRole('admin')) {
            return redirect()->to('/admin');
        }

        return redirect()->intended('/');
    }

    // ─── Register ─────────────────────────────────────────────────────────────

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'phone_full' => [
                'required',
                'string',
                'unique:users,phone',
                new PhoneRule,          // international format validation
            ],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required'        => 'الاسم الكامل مطلوب.',
            'phone_full.required'  => 'رقم الهاتف مطلوب.',
            'phone_full.unique'    => 'رقم الهاتف هذا مسجل مسبقاً.',
            'phone_full.phone'     => 'رقم الهاتف غير صحيح أو غير مدعوم.',
            'password.required'   => 'كلمة المرور مطلوبة.',
            'password.min'        => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.confirmed'  => 'كلمتا المرور غير متطابقتين.',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'phone'    => $validated['phone_full'],   // store E.164 directly
            'password' => $validated['password'],     // auto-hashed via Model cast
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('products.index')
                         ->with('success', 'تم إنشاء حسابك بنجاح، مرحباً ' . $user->name . '!');
    }

    // ─── Logout ───────────────────────────────────────────────────────────────

    public function logout(Request $request): RedirectResponse
    {
        $isAdmin = Auth::check() && Auth::user()->hasRole('admin');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $isAdmin
            ? redirect()->to('/adlogin')->with('success', 'تم تسجيل خروج المسؤول.')
            : redirect()->route('login')->with('success', 'تم تسجيل الخروج.');
    }
}