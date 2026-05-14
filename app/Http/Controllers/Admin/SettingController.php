<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $siteSettings = Setting::pluck('value', 'key');
        $holder       = Setting::mediaHolder();
        $logoUrl      = $holder->getFirstMediaUrl('logo');
        $faviconUrl   = $holder->getFirstMediaUrl('favicon');

        return view('admin.settings', compact('siteSettings', 'logoUrl', 'faviconUrl'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo'    => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'favicon' => 'nullable|file|mimes:ico,png,svg|max:512',
        ]);

        $keys = [
            'primary_color', 'bg_color', 'nav_bg_color',
            'card_bg_color', 'footer_bg_color', 'footer_text_color',
            'footer_link_color', 'footer_bottom_text_color',
            'footer_text_size', 'site_name',
            'splash_title_main', 'splash_title_sub',
            'splash_color_main', 'splash_color_sub',
            'splash_font_size', 'splash_font_family',
            'font_ar', 'font_en',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $request->input($key)]
                );
            }
        }

        $holder = Setting::mediaHolder();

        if ($request->hasFile('logo')) {
            $holder->addMediaFromRequest('logo')
                   ->toMediaCollection('logo');
        }

        if ($request->hasFile('favicon')) {
            $holder->addMediaFromRequest('favicon')
                   ->toMediaCollection('favicon');
        }

        return back()->with('success', 'تم تحديث الإعدادات بنجاح!');
    }
}