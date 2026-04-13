<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
  public function index() {
    // جلب جميع الإعدادات وتحويلها لمصفوفة مفتاح وقيمة لسهولة الاستخدام
    $siteSettings = Setting::pluck('value', 'key'); 
    return view('admin.settings', compact('siteSettings'));
}


public function update(Request $request) {
    // أضفنا footer_bg_color هنا
    $keys = [
        'primary_color', 
        'bg_color', 
        'nav_bg_color', 
        'card_bg_color', 
        'footer_bg_color', 
        'site_name'
    ];

    foreach ($keys as $key) {
        if ($request->has($key)) {
            \App\Models\Setting::updateOrCreate(['key' => $key], ['value' => $request->$key]);
        }
    }

    if ($request->hasFile('logo')) {
        $path = $request->file('logo')->store('site', 'public');
        \App\Models\Setting::updateOrCreate(['key' => 'site_logo'], ['value' => $path]);
    }

    return back()->with('success', 'تم تحديث الإعدادات بنجاح!');
}
}