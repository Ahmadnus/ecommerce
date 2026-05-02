<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SocialLinkController extends Controller
{
    public function index() {
    $links = \App\Models\SocialLink::orderBy('sort_order')->get();
    return view('admin.social_links.index', compact('links'));
}

public function store(Request $request)
{
    $request->validate([
        'platform_name'   => 'required|string',
        'url'             => 'nullable|url',
        'whatsapp_number' => 'nullable|string',
        'icon'            => 'nullable|image|mimes:png,jpg,jpeg,webp',
    ]);

    $data = $request->all();
    $data['is_floating'] = $request->has('is_floating') ? 1 : 0;

    $link = \App\Models\SocialLink::create($data);

    if ($request->hasFile('icon')) {
        $link->addMediaFromRequest('icon')
            ->toMediaCollection('icons');
    }

    return back()->with('success', 'تمت إضافة الرابط بنجاح');
}

public function destroy(\App\Models\SocialLink $socialLink) {
    $socialLink->delete();
    return back()->with('success', 'تم الحذف');
}
}
