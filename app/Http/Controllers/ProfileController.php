<?php

namespace App\Http\Controllers;

use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profile,
    ) {}

    /**
     * عرض صفحة البروفايل (بدلاً من edit)
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        // جلب آخر 5 طلبات للمستخدم
        $orders = $this->profile->getRecentOrders($user);

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

        $this->profile->updateProfile($user, $data);

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
        $this->profile->deleteAccount($user);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
