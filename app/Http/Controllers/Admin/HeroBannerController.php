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
            'image' => 'required|image|max:2048',
        ]);

        $banner = HeroBanner::create($request->all());

        if ($request->hasFile('image')) {
            $banner->addMediaFromRequest('image')->toMediaCollection('banner_image');
        }

        return back()->with('success', 'تم إضافة البانر بنجاح');
    }

    public function update(Request $request, HeroBanner $heroBanner)
    {
        $heroBanner->update($request->all());

        if ($request->hasFile('image')) {
            $heroBanner->addMediaFromRequest('image')->toMediaCollection('banner_image');
        }

        return back()->with('success', 'تم التحديث بنجاح');
    }

    // حذف البانر مع صورته
    public function destroy(HeroBanner $heroBanner)
    {
        // Spatie ستقوم تلقائياً بحذف الملفات من القرص عند حذف الموديل
        $heroBanner->delete();

        return back()->with('success', 'تم حذف البانر بنجاح');
    }
}