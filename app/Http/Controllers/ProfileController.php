<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * عرض صفحة البروفايل (بدلاً من edit)
     */
    public function show(Request $request): View
    {
        $user = $request->user();
        
        // جلب آخر 5 طلبات للمستخدم
        // تأكد أن علاقة orders موجودة في مودل User
        $orders = $user->orders()->latest()->take(5)->get() ?? collect();

        return view('myprofile.show', [
            'user' => $user,
            'orders' => $orders
        ]);
    }

    /**
     * تحديث بيانات المستخدم
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->name = $data['name'];
        $user->phone = $data['phone'];

        if (!empty($data['password'])) {
            $user->password = $data['password']; // Laravel 11+ سيقوم بعمل Hash تلقائياً بناءً على المودل
        }

        $user->save();

        return back()->with('success', 'تم تحديث بياناتك بنجاح');
    }

    /**
     * حذف الحساب (اختياري)
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['required', 'current_password']]);
        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}