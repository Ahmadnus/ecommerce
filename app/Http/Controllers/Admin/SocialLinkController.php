<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SocialLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index() {
    $links = \App\Models\SocialLink::orderBy('sort_order')->get();
    return view('admin.social_links.index', compact('links'));
}

public function store(Request $request) {
    $request->validate([
        'platform_name'   => 'required|string',
        'url'             => 'nullable|url', // جعلناه nullable لأن الواتساب قد يكتفي بالرقم
        'whatsapp_number' => 'nullable|string',
        'icon_svg'        => 'nullable|string'
    ]);

    // نأخذ كل البيانات ونضمن معالجة الـ checkbox
    $data = $request->all();
    $data['is_floating'] = $request->has('is_floating') ? 1 : 0;

    \App\Models\SocialLink::create($data);

    return back()->with('success', 'تمت إضافة الرابط بنجاح');
}

public function destroy(\App\Models\SocialLink $socialLink) {
    $socialLink->delete();
    return back()->with('success', 'تم الحذف');
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
  
}
