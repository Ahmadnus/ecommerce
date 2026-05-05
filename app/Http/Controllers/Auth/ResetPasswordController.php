<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function showForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

  public function reset(Request $request): RedirectResponse
{
    $request->validate(
        [
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ],
        [
            'email.required'    => __('app.auth.email_required'),
            'email.email'       => __('app.auth.email_invalid'),
            'password.required' => __('app.auth.password_required'),
            'password.min'      => __('app.auth.password_min'),
            'password.confirmed'=> __('app.auth.password_confirmed'),
        ]
    );

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password'       => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        }
    );

    if ($status === Password::PASSWORD_RESET) {
        return redirect()->route('login')->with('success', __('app.auth.password_reset_success'));
    }

    return back()
        ->withErrors(['email' => __($status)])
        ->withInput($request->only('email'));
}
}