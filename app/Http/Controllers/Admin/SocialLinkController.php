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
     * Update an existing link. The two booleans are read with has() rather than
     * from the validated array because an unchecked box is simply absent.
     */
    public function update(Request $request, SocialLink $socialLink)
    {
        $data = $request->validate([
            'platform_name'   => 'required|string|max:60',
            'url'             => 'nullable|url|max:255',
            'whatsapp_number' => 'nullable|string|max:40',
            'icon_svg'        => 'nullable|string|max:120',
            'sort_order'      => 'nullable|integer|min:0',
        ]);

        $data['is_floating'] = $request->boolean('is_floating');
        $data['is_active']   = $request->boolean('is_active');

        $this->socialLinks->update($socialLink, $data);

        return back()->with('success', 'تم تحديث الرابط بنجاح');
    }

    /** Enable/disable a link, or make it the floating button, from the list. */
    public function toggle(Request $request, SocialLink $socialLink)
    {
        $this->socialLinks->toggle($socialLink, (string) $request->input('column', 'is_active'));

        return back()->with('success', 'تم تحديث الحالة');
    }
}
