<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroBanner;
use Illuminate\Http\Request;

class HeroBannerController extends Controller
{
    // عرض قائمة البانرات
    public function index()
    {
        $banners = HeroBanner::orderBy('sort_order')->get();
        return view('admin.hero-banners.index', compact('banners'));
    }

    public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'image' => 'nullable|image|max:2048',
        'background_color' => 'nullable|string|max:20',
        'text_color' => 'nullable|string|max:20',
    ]);

    $banner = HeroBanner::create([
        'title' => $request->title,
        'subtitle' => $request->subtitle,
        'badge' => $request->badge,
        'description' => $request->description,
        'button_text' => $request->button_text ?? 'اكتشف الآن',
        'button_url' => $request->button_url,
        'sort_order' => $request->sort_order ?? 0,
        'is_active' => true,
        'position' => $request->position,

        'background_color' => $request->background_color,
        'text_color' => $request->text_color ,
    ]);

 if ($request->filled('image')) {
    $banner->addMediaFromRequest('image')->toMediaCollection('banner_image');
}

    return back()->with('success', 'تم إضافة البانر بنجاح');
}

 public function update(Request $request, HeroBanner $heroBanner)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'background_color' => 'nullable|string|max:20',
        'text_color' => 'nullable|string|max:20',
    ]);

    $heroBanner->update([
        'title' => $request->title,
        'subtitle' => $request->subtitle,
        'badge' => $request->badge,
        'description' => $request->description,

        // ✅ الحل هون
        'button_text' => $request->button_text ?? 'اكتشف الآن',
        'button_url' => $request->button_url,

        'sort_order' => $request->sort_order ?? 0,
        'position' => $request->position ?? $heroBanner->position,

        'is_active' => $request->has('is_active'),

        'background_color' => $request->background_color ?? '#0ea5e9',
        'text_color' => $request->text_color ?? '#ffffff',
    ]);

    if ($request->hasFile('image')) {
        $heroBanner->addMediaFromRequest('image')->toMediaCollection('banner_image');
    }

    return back()->with('success', 'تم التحديث بنجاح');
}  // حذف البانر مع صورته
    public function destroy(HeroBanner $heroBanner)
    {
        // Spatie ستقوم تلقائياً بحذف الملفات من القرص عند حذف الموديل
        $heroBanner->delete();

        return back()->with('success', 'تم حذف البانر بنجاح');
    }
}