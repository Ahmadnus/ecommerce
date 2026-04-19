<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function store(Request $request) {
    $data = $request->validate(['content' => 'required|string|max:255']);
    Announcement::create($data);
    return back()->with('success', 'تمت الإضافة بنجاح');
}

public function update(Request $request, Announcement $announcement) {
    $data = $request->validate(['content' => 'required|string|max:255', 'is_active' => 'boolean']);
    $announcement->update($data);
    return back()->with('success', 'تم التحديث');
}

public function destroy(Announcement $announcement) {
    $announcement->delete();
    return back()->with('success', 'تم الحذف');
}
public function index()
    {
        $announcements = Announcement::orderBy('sort_order')->get();
        return view('admin.announcements.index', compact('announcements'));
    }
}
