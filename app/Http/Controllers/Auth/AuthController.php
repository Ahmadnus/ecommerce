<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class AuthController extends Controller
{
    // --- عرض صفحات الدخول ---
    public function showLogin(): View { return view('auth.login'); }
    public function showAdminLogin(): View { return view('auth.admin-login'); }

    // --- منطق تسجيل الدخول (Login) ---
    public function login(Request $request): RedirectResponse
    {
        // 1. الفالديشن الخاص بالدخول (هاتف وكلمة مرور فقط)
        $credentials = $request->validate([
            'phone'    => ['required', 'string', 'regex:/^0962\d+$/'],
            'password' => ['required', 'string'],
        ], [
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.regex'    => 'يجب أن يبدأ رقم الهاتف بـ 0962.',
        ]);

        $throttleKey = 'login|' . $request->input('phone') . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors(['phone' => "محاولات كثيرة. انتظر {$seconds} ثانية."]);
        }

        // 2. محاولة تسجيل الدخول
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withErrors(['phone' => 'بيانات الدخول غير صحيحة.']);
        }

        // 3. النجاح
        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();
        $user = Auth::user();

        // 4. التوجيه
        if ($user->hasRole('admin')) {
            return redirect()->to('/admin');
        }

        if ($request->has('is_admin_login')) {
            Auth::logout();
            return back()->withErrors(['phone' => 'هذه البوابة مخصصة للمسؤولين فقط.']);
        }

        return redirect()->intended('/');
    }

    // --- منطق إنشاء الحساب (Register) ---
    public function register(Request $request): RedirectResponse
    {
        // 1. الفالديشن الخاص بالتسجيل (كامل مع التحقق من التكرار)
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'phone'    => [
                'required', 
                'string', 
                'unique:users,phone', 
                'regex:/^0962\d+$/', 
                'min:10', 
                'max:15'
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required'      => 'الاسم الكامل مطلوب.',
            'phone.required'     => 'رقم الهاتف مطلوب.',
            'phone.unique'       => 'رقم الهاتف هذا مسجل مسبقاً.',
            'phone.regex'        => 'يجب أن يبدأ رقم الهاتف بـ 0962.',
            'password.required'  => 'كلمة المرور مطلوبة.',
            'password.min'       => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'password.confirmed' => 'كلمتا المرور غير متطابقتين.',
        ]);

        // 2. إنشاء المستخدم
        $user = User::create([
            'name'     => $validated['name'],
            'phone'    => $validated['phone'],
            'password' => $validated['password'], // يتم التشفير تلقائياً في الموديل
        ]);

        // 3. الدخول التلقائي
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('products.index')
                         ->with('success', 'تم إنشاء حسابك بنجاح، مرحباً ' . $user->name . '!');
    }

    // --- تسجيل الخروج ---
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