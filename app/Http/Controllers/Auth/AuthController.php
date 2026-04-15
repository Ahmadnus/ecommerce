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

  // داخل الكلاس AuthController

public function showAdminLogin(): View
{
    return view('auth.admin-login'); // تأكد أن اسم الملف هو admin-login.blade.php
}

public function login(Request $request): RedirectResponse
{
    // 1. Validate input
    $credentials = $request->validate([
        'phone'    => ['required', 'string'],
        'password' => ['required', 'string', 'min:6'],
    ]);

    $throttleKey = 'login|' . $request->input('phone') . '|' . $request->ip();

    if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
        $seconds = RateLimiter::availableIn($throttleKey);
        return back()->withErrors(['phone' => "محاولات كثيرة. انتظر {$seconds} ثانية."]);
    }

    // 2. Attempt authentication
    if (!Auth::attempt($credentials, $request->boolean('remember'))) {
        RateLimiter::hit($throttleKey, 60);
        return back()->withErrors(['phone' => 'بيانات الدخول غير صحيحة.']);
    }

    // 3. النجاح وتحديد التوجيه
    RateLimiter::clear($throttleKey);
    $request->session()->regenerate();

    $user = Auth::user();

    // إذا كان الطلب قادم من صفحة تسجيل دخول الإدارة (adlogin)
    if ($request->has('is_admin_login')) {
        if ($user->hasRole('admin')) {
            return redirect()->to('/admin'); // توجيه مباشر ومؤكد للوحة التحكم
        } else {
            Auth::logout();
            return back()->withErrors(['phone' => 'هذه البوابة مخصصة للمسؤولين فقط.']);
        }
    }

    // التوجيه الطبيعي للمستخدمين العاديين (أو إذا دخل الأدمن من صفحة المتجر العادية)
    if ($user->hasRole('admin')) {
        return redirect()->to('/admin');
    }

    return redirect()->intended('/'); 
}
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

        return redirect()->route('products.index')
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