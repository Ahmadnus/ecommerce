<?php

namespace App\Http\Controllers;

use App\Models\SocialLink;
use App\Services\SocialLinkService;
use Illuminate\Http\Request;

class SocialLinkController extends Controller
{
    public function __construct(
        private readonly SocialLinkService $socialLinks,
    ) {}

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
            'icon'            => 'nullable|image|mimes:png,jpg,jpeg,webp',
        ]);

        $data = $request->all();
        $data['is_floating'] = $request->has('is_floating') ? 1 : 0;

        $this->socialLinks->createWithIcon(
            $data,
            $request->hasFile('icon') ? $request->file('icon') : null,
        );

        return back()->with('success', 'تمت إضافة الرابط بنجاح');
    }

    public function destroy(SocialLink $socialLink)
    {
        $this->socialLinks->delete($socialLink);
        return back()->with('success', 'تم الحذف');
    }
}
