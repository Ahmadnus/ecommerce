<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SplashSettingsController extends Controller
{
    public function edit()
    {
        $settings = Setting::whereIn('key', [
            'splash_title_main', 'splash_title_sub', 
            'splash_color_main', 'splash_color_sub', 'splash_loading_text'
        ])->pluck('value', 'key');

        return view('admin.splash.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'splash_title_main'   => 'required|string|max:20',
            'splash_title_sub'    => 'required|string|max:20',
            'splash_color_main'   => 'required|string|max:7',
            'splash_color_sub'    => 'required|string|max:7',
            'splash_loading_text' => 'required|string|max:100',
        ]);

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'تم تحديث إعدادات صفحة الدخول بنجاح');
    }
}