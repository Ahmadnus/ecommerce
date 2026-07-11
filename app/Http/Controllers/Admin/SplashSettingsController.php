<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SplashSettingsController extends Controller
{
    public function __construct(
        private readonly SettingService $settings,
    ) {}

    public function edit()
    {
        $settings = $this->settings->getSplashSettings();

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

        $this->settings->saveSplashSettings($data);

        return back()->with('success', 'تم الحفظ بنجاح!');
    }
}
