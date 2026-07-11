<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use App\Services\SocialLinkService;
use Illuminate\Http\Request;

class SocialLinkController extends Controller
{
    public function __construct(
        private readonly SocialLinkService $socialLinks,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $links = $this->socialLinks->getLinks();
        return view('admin.social_links.index', compact('links'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'platform_name'   => 'required|string',
            'url'             => 'nullable|url',
            'whatsapp_number' => 'nullable|string',
            'icon_svg'        => 'nullable|string',
            'is_floating'     => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_floating'] = $request->has('is_floating') ? 1 : 0;

        $this->socialLinks->create($data);

        return back()->with('success', 'تمت إضافة الرابط بنجاح');
    }

    public function destroy(SocialLink $socialLink)
    {
        $this->socialLinks->delete($socialLink);
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
}
