<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroBanner;
use Illuminate\Http\Request;

class HeroBannerController extends Controller
{
    public function index()
    {
        $banners = HeroBanner::orderBy('sort_order')->get();
        return view('admin.hero-banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // ── Translatable ─────────────────────────────────────────────
            'title.ar'       => 'required|string|max:255',
            'title.en'       => 'nullable|string|max:255',
            'subtitle.ar'    => 'nullable|string|max:255',
            'subtitle.en'    => 'nullable|string|max:255',
            'badge.ar'       => 'nullable|string|max:255',
            'badge.en'       => 'nullable|string|max:255',
            'description.ar' => 'nullable|string',
            'description.en' => 'nullable|string',
            'button_text.ar' => 'required|string|max:100',
            'button_text.en' => 'nullable|string|max:100',
            // ─────────────────────────────────────────────────────────────
            'button_url'       => 'nullable|string|max:500',
            'position'         => 'required|in:top,after_featured,after_products',
            'background_color' => 'nullable|string|max:20',
            'text_color'       => 'nullable|string|max:20',
            'image'            => 'nullable|image|max:2048',
        ], [
            'title.ar.required'       => 'العنوان الرئيسي بالعربية مطلوب',
            'button_text.ar.required' => 'نص الزر بالعربية مطلوب',
        ]);

        $banner = HeroBanner::create([
            'title'            => $request->input('title'),
            'subtitle'         => $request->input('subtitle'),
            'badge'            => $request->input('badge'),
            'description'      => $request->input('description'),
            'button_text'      => $request->input('button_text'),
            'button_url'       => $request->button_url,
            'position'         => $request->position,
            'sort_order'       => $request->input('sort_order', 0),
            'is_active'        => true,
            'background_color' => $request->input('background_color', '#0ea5e9'),
            'text_color'       => $request->input('text_color', '#ffffff'),
        ]);

        if ($request->hasFile('image')) {
            $banner->addMediaFromRequest('image')->toMediaCollection('banner_image');
        }

        return back()->with('success', 'تم إضافة البانر بنجاح');
    }

    public function update(Request $request, HeroBanner $heroBanner)
    {
        $request->validate([
            'title.ar'       => 'required|string|max:255',
            'title.en'       => 'nullable|string|max:255',
            'subtitle.ar'    => 'nullable|string|max:255',
            'subtitle.en'    => 'nullable|string|max:255',
            'badge.ar'       => 'nullable|string|max:255',
            'badge.en'       => 'nullable|string|max:255',
            'description.ar' => 'nullable|string',
            'description.en' => 'nullable|string',
            'button_text.ar' => 'required|string|max:100',
            'button_text.en' => 'nullable|string|max:100',
            'button_url'       => 'nullable|string|max:500',
            'position'         => 'required|in:top,after_featured,after_products',
            'background_color' => 'nullable|string|max:20',
            'text_color'       => 'nullable|string|max:20',
            'image'            => 'nullable|image|max:2048',
        ], [
            'title.ar.required'       => 'العنوان الرئيسي بالعربية مطلوب',
            'button_text.ar.required' => 'نص الزر بالعربية مطلوب',
        ]);

        $heroBanner->update([
            'title'            => $request->input('title'),
            'subtitle'         => $request->input('subtitle'),
            'badge'            => $request->input('badge'),
            'description'      => $request->input('description'),
            'button_text'      => $request->input('button_text'),
            'button_url'       => $request->button_url,
            'position'         => $request->input('position', $heroBanner->position),
            'sort_order'       => $request->input('sort_order', 0),
            'is_active'        => $request->boolean('is_active'),
            'background_color' => $request->input('background_color', '#0ea5e9'),
            'text_color'       => $request->input('text_color', '#ffffff'),
        ]);

        if ($request->hasFile('image')) {
            $heroBanner->addMediaFromRequest('image')->toMediaCollection('banner_image');
        }

        return back()->with('success', 'تم التحديث بنجاح');
    }

    public function destroy(HeroBanner $heroBanner)
    {
        $heroBanner->delete();
        return back()->with('success', 'تم حذف البانر بنجاح');
    }
}