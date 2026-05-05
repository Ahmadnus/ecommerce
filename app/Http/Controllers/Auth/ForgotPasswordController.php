<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function showForm(): View
    {
        return view('auth.forgot-password');
    }

  public function send(Request $request): RedirectResponse
{
    $request->validate(
        ['email' => ['required', 'email']],
        [
            'email.required' => __('app.auth.email_required'), 
            'email.email'    => __('app.auth.email_invalid')
        ]
    );

    $status = Password::sendResetLink($request->only('email'));

    if ($status === Password::RESET_LINK_SENT) {
        return back()->with('success', __($status));
    }

    return back()->withErrors(['email' => __($status)])->withInput();
}
}