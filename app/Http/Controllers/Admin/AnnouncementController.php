<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Services\AnnouncementService;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function __construct(
        private readonly AnnouncementService $announcements,
    ) {}

    public function index()
    {
        $announcements = $this->announcements->getAnnouncements();
        return view('admin.announcements.index', compact('announcements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'content.ar' => 'required|string|max:255',
            'content.en' => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'boolean',
        ]);

        $this->announcements->create([
            'content'    => $request->input('content'),   // ['ar' => '...', 'en' => '...']
            'sort_order' => $request->input('sort_order', 0),
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'تمت الإضافة بنجاح');
    }

    public function update(Request $request, Announcement $announcement)
    {
        $request->validate([
            'content.ar' => 'required|string|max:255',
            'content.en' => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'boolean',
        ]);

        $this->announcements->update($announcement, [
            'content'    => $request->input('content'),
            'sort_order' => $request->input('sort_order', 0),
            'is_active'  => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'تم التحديث');
    }

    public function destroy(Announcement $announcement)
    {
        $this->announcements->delete($announcement);
        return back()->with('success', 'تم الحذف');
    }
}
