<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::orderBy('sort_order')->get();
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

        Announcement::create([
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

        $announcement->update([
            'content'    => $request->input('content'),
            'sort_order' => $request->input('sort_order', 0),
            'is_active'  => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'تم التحديث');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'تم الحذف');
    }
}