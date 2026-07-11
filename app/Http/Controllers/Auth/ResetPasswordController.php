<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\PasswordResetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function __construct(
        private readonly PasswordResetService $passwordReset,
    ) {}

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

        $status = $this->passwordReset->reset(
            $request->only('email', 'password', 'password_confirmation', 'token')
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', __('app.auth.password_reset_success'));
        }

        return back()
            ->withErrors(['email' => __($status)])
            ->withInput($request->only('email'));
    }
}
