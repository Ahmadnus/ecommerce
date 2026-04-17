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
        return view('admin.site-features.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'icon' => 'required|string|max:10',
            'title' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        SiteFeature::create($data);

        return redirect()->route('admin.site-features.index')->with('success', 'تمت الإضافة');
    }

    public function edit(SiteFeature $site_feature)
    {
        return view('admin.site-features.edit', compact('site_feature'));
    }

    public function update(Request $request, SiteFeature $site_feature)
    {
        $data = $request->validate([
            'icon' => 'required|string|max:10',
            'title' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean'
        ]);

        $site_feature->update($data);

        return back()->with('success', 'تم التحديث');
    }

    public function destroy(SiteFeature $site_feature)
    {
        $site_feature->delete();
        return back()->with('success', 'تم الحذف');
    }
}