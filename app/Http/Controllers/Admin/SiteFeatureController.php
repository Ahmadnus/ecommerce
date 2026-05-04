<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteFeature;
use Illuminate\Http\Request;

class SiteFeatureController extends Controller
{
    public function index()
    {
        $features = SiteFeature::orderBy('sort_order')->get();
        return view('admin.site-features.index', compact('features'));
    }

       public function create()
{
    $site_feature = new SiteFeature();

    return view('admin.site-features.create', compact('site_feature'));
}

public function store(Request $request)
{
    try {
        // نستخدم الإدخال اليدوي للتجربة
        $feature = new SiteFeature();
        $feature->icon = $request->icon;
        $feature->setTranslation('title', 'ar', $request->input('title.ar'));
        $feature->setTranslation('title', 'en', $request->input('title.en'));
        $feature->setTranslation('description', 'ar', $request->input('description.ar'));
        $feature->setTranslation('description', 'en', $request->input('description.en'));
        $feature->sort_order = $request->input('sort_order', 0);
        $feature->is_active = $request->has('is_active');
        $feature->save();

        return redirect()->route('admin.site-features.index')->with('success', 'تم الحفظ');
    } catch (\Exception $e) {
        // سيظهر لك سبب المشكلة الحقيقي هنا
        return $e->getMessage(); 
    }
}

    public function edit(SiteFeature $site_feature)
    {
        return view('admin.site-features.edit', compact('site_feature'));
    }

    public function update(Request $request, SiteFeature $site_feature)
    {
        $request->validate([
            'icon'              => 'required|string|max:10',
            'title.ar'          => 'required|string|max:100',
            'title.en'          => 'required|string|max:100',
            'description.ar'    => 'nullable|string|max:255',
            'description.en'    => 'nullable|string|max:255',
            'sort_order'        => 'nullable|integer',
            'is_active'         => 'boolean',
        ]);

        $site_feature->update([
            'icon'        => $request->icon,
            'title'       => $request->input('title'),
            'description' => $request->input('description'),
            'sort_order'  => $request->input('sort_order', 0),
            'is_active'   => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'تم التحديث');
    }

    public function destroy(SiteFeature $site_feature)
    {
        $site_feature->delete();
        return back()->with('success', 'تم الحذف');
    }
}