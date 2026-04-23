<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SplashSettingsController extends Controller
{
   public function edit()
{
    // أضفنا الخط والحجم هنا
    $settings = Setting::whereIn('key', [
        'splash_title_main', 'splash_title_sub', 
        'splash_color_main', 'splash_color_sub', 
        'splash_loading_text', 'splash_font_size', 'splash_font_family'
    ])->pluck('value', 'key');

    return view('admin.splash.edit', compact('settings'));
}

public function update(Request $request)
{
    // اجعل الحقول nullable لضمان أن الفورم لا يتعطل إذا نسينا حقل
    $data = $request->validate([
        'splash_title_main'   => 'nullable|string|max:255',
        'splash_title_sub'    => 'nullable|string|max:255',
        'splash_color_main'   => 'nullable|string|max:20',
        'splash_color_sub'    => 'nullable|string|max:20',
        'splash_loading_text' => 'nullable|string|max:255',
        'splash_font_size'    => 'nullable|string|max:255',
        'splash_font_family'  => 'nullable|string|max:255',
    ]);

    foreach ($data as $key => $value) {
        // نستخدم updateOrCreate بناءً على الـ key
        \App\Models\Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value ?? ''] // إذا كانت القيمة نل نضع نص فارغ
        );
    }

    return back()->with('success', 'تم الحفظ بنجاح!');
}
}