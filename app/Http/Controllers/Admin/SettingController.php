<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index() {
        return view('admin.settings'); // سننشئ هذه الصفحة
    }

    public function update(Request $request) {
        // تحديث اللون
        if ($request->has('primary_color')) {
            Setting::updateOrCreate(['key' => 'primary_color'], ['value' => $request->primary_color]);
        }

        // تحديث الشعار
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('site', 'public');
            Setting::updateOrCreate(['key' => 'site_logo'], ['value' => $path]);
        }

        return back()->with('success', 'تم تحديث الإعدادات بنجاح!');
    }
}