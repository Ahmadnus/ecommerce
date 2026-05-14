<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminPasswordController extends Controller
{
    public function edit(): View
    {
        return view('admin.password.edit');
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ], [
            'current_password.required' => 'الرجاء إدخال كلمة السر الحالية.',
            'current_password.current_password' => 'كلمة السر الحالية غير صحيحة.',
            'password.required' => 'الرجاء إدخال كلمة السر الجديدة.',
            'password.confirmed' => 'تأكيد كلمة السر غير مطابق.',
        ]);

        $user = $request->user();

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'تم تغيير كلمة السر بنجاح.');
    }
}