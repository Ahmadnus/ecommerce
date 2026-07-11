<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct(
        private readonly SettingService $settings,
    ) {}

    public function index()
    {
        return view('admin.settings', $this->settings->getIndexData());
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo'    => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'favicon' => 'nullable|file|mimes:ico,png,svg|max:512',
        ]);

        $this->settings->saveSettings(
            $request->only(SettingService::KEYS),
            $request->hasFile('logo') ? $request->file('logo') : null,
            $request->hasFile('favicon') ? $request->file('favicon') : null,
        );

        return back()->with('success', 'تم تحديث الإعدادات بنجاح!');
    }
}
