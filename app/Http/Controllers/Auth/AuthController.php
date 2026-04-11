<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    // ─── Login ────────────────────────────────────────────────────────────────

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        // 1. Validate input
        $credentials = $request->validate([
            'phone'    => ['required', 'string', 'regex:/^05\d{8}$/'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.regex'    => 'رقم الهاتف يجب أن يبدأ بـ 05 ويتكون من 10 أرقام.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min'   => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل.',
        ]);

        // 2. Rate-limit: max 5 attempts per phone per minute
        $throttleKey = 'login|' . $request->input('phone') . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->withInput($request->only('phone'))
                ->withErrors([
                    'phone' => "تم تجاوز عدد المحاولات. حاول مجدداً بعد {$seconds} ثانية.",
                ]);
        }

        // 3. Attempt authentication
        $attempted = Auth::attempt(
            ['phone' => $credentials['phone'], 'password' => $credentials['password']],
            $request->boolean('remember')
        );

        if (! $attempted) {
            RateLimiter::hit($throttleKey, 60);

            return back()
                ->withInput($request->only('phone'))
                ->withErrors([
                    'phone' => 'رقم الهاتف أو كلمة المرور غير صحيحة.',
                ]);
        }

        // 4. Success — regenerate session to prevent fixation
        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->intended(route('home'))
                         ->with('success', 'مرحباً بعودتك، ' . Auth::user()->name . '!');
    }

    // ─── Register ─────────────────────────────────────────────────────────────

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        // 1. Validate
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'phone'    => ['required', 'string', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required'         => 'الاسم الكامل مطلوب.',
            'name.max'              => 'الاسم يجب ألا يتجاوز 255 حرفاً.',
            'phone.required'        => 'رقم الهاتف مطلوب.',
            'phone.regex'           => 'رقم الهاتف يجب أن يبدأ بـ 05 ويتكون من 10 أرقام.',
            'phone.unique'          => 'رقم الهاتف هذا مسجل مسبقاً.',
            'password.required'     => 'كلمة المرور مطلوبة.',
            'password.min'          => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.confirmed'    => 'كلمتا المرور غير متطابقتين.',
        ]);

        // 2. Create the user
        // Password is auto-hashed by the 'hashed' cast in User model
        $user = User::create([
            'name'     => $validated['name'],
            'phone'    => $validated['phone'],
            'password' => $validated['password'],
        ]);

        // 3. Log in immediately after registration
        Auth::login($user, remember: false);
        $request->session()->regenerate();

        return redirect()->route('home')
                         ->with('success', 'تم إنشاء حسابك بنجاح، مرحباً ' . $user->name . '!');
    }

    // ─── Logout ───────────────────────────────────────────────────────────────

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        // Invalidate & regenerate session token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
                         ->with('success', 'تم تسجيل الخروج بنجاح.');
    }
}