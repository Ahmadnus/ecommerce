<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class AuthController extends Controller
{
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

    public function login(Request $request): RedirectResponse
{
    $identity = trim((string) ($request->input('phone_full') ?? $request->input('identity') ?? ''));
    $isEmail  = filter_var($identity, FILTER_VALIDATE_EMAIL) !== false;

    $rules = [
        'password' => ['required', 'string'],
    ];

    $messages = [
        'password.required' => __('app.auth.password_required'),
    ];

    if ($isEmail) {
        $rules['identity'] = ['required', 'email'];
        $messages['identity.required'] = __('app.auth.email_required');
        $messages['identity.email'] = __('app.auth.email_invalid');
    } else {
        $rules['phone_full'] = ['required', 'string'];
        $messages['phone_full.required'] = __('app.auth.phone_required');
    }

    $request->validate($rules, $messages);

    $throttleKey = 'login|' . $identity . '|' . $request->ip();

    if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
        $seconds = RateLimiter::availableIn($throttleKey);
        $field = $isEmail ? 'identity' : 'phone_full';

        return back()->withErrors([
            $field => __('app.auth.too_many_attempts', ['seconds' => $seconds])
        ])->withInput();
    }

    $user = $isEmail
        ? User::where('email', $identity)->first()
        : User::where('phone', $identity)->first();

    $errorField = $isEmail ? 'identity' : 'phone_full';

    if (! $user || ! Hash::check($request->input('password'), $user->password)) {
        RateLimiter::hit($throttleKey, 60);

        return back()
            ->withErrors([$errorField => __('app.auth.failed')])
            ->withInput(['phone_full' => $identity, 'identity' => $identity]);
    }

    RateLimiter::clear($throttleKey);
    Auth::login($user, $request->boolean('remember'));
    $request->session()->regenerate();

    if ($request->input('is_admin_login') == '1' || $user->hasRole('admin')) {
        return redirect('/admin');
    }

    return redirect()->route('products.index');
}

public function register(Request $request): RedirectResponse
{
    $hasPhone = $request->filled('phone_full');
    $hasEmail = $request->filled('email');

    if (! $hasPhone && ! $hasEmail) {
        return back()
            ->withErrors(['identity' => __('app.auth.identity_missing')])
            ->withInput();
    }

    $rules = [
        'name'     => ['required', 'string', 'max:255'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ];

    $messages = [
        'name.required' => __('app.auth.name_required'),
        'password.required' => __('app.auth.password_required'),
        'password.min' => __('app.auth.password_min'),
        'password.confirmed' => __('app.auth.password_confirmed'),
    ];

    if ($hasPhone) {
        $rules['phone_full'] = ['required', 'string', 'unique:users,phone', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'];
        $rules['country_id'] = ['nullable', 'exists:countries,id'];

        $messages['phone_full.required'] = __('app.auth.phone_required');
        $messages['phone_full.unique'] = __('app.auth.phone_unique');
        $messages['phone_full.regex'] = __('app.auth.phone_invalid');
    }

    if ($hasEmail) {
        $rules['email'] = ['required', 'email', 'unique:users,email', 'max:255'];

        $messages['email.required'] = __('app.auth.email_required');
        $messages['email.email'] = __('app.auth.email_invalid');
        $messages['email.unique'] = __('app.auth.email_unique');
    }

    $validated = $request->validate($rules, $messages);

    $user = User::create([
        'name'       => $validated['name'],
        'phone'      => $hasPhone ? ($validated['phone_full'] ?? null) : null,
        'email'      => $hasEmail ? ($validated['email'] ?? null) : null,
        'country_id' => $validated['country_id'] ?? null,
        'password'   => Hash::make($validated['password']),
    ]);

    Auth::login($user);
    $request->session()->regenerate();

    return redirect()->route('products.index')->with('success', __('app.auth.register_success'));
}

public function logout(Request $request): RedirectResponse
{
    $isAdmin = Auth::check() && Auth::user()->hasRole('admin');

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return $isAdmin
        ? redirect()->route('admin.login')->with('success', __('app.auth.logout_admin'))
        : redirect()->route('login')->with('success', __('app.auth.logout_user'));
}
}