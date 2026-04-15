<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SocialLinkController extends Controller
{
    public function index() {
    $links = \App\Models\SocialLink::orderBy('sort_order')->get();
    return view('admin.social_links.index', compact('links'));
}

public function store(Request $request) {
    $request->validate([
        'platform_name' => 'required|string',
        'url' => 'required|url',
        'icon_svg' => 'nullable|string'
    ]);

    \App\Models\SocialLink::create($request->all());
    return back()->with('success', 'تمت إضافة الرابط بنجاح');
}

public function destroy(\App\Models\SocialLink $socialLink) {
    $socialLink->delete();
    return back()->with('success', 'تم الحذف');
}
}
