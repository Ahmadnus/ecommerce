<?php
// app/Http/Controllers/Admin/FooterTextController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterText;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FooterTextController extends Controller
{
    public function index(): View
    {
        $items = FooterText::orderBy('sort_order')->latest()->get();

        return view('admin.footer-texts.index', compact('items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'slug'      => 'required|string|max:100|unique:footer_texts,slug',
            'text_ar'   => 'required|string|max:255',
            'text_en'   => 'required|string|max:255',
            'sort_order'=> 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

       $item = FooterText::create([
    'slug'       => $validated['slug'],
    'is_active'  => $request->boolean('is_active', true),
    'sort_order' => $validated['sort_order'] ?? 0,
    'text' => [
        'ar' => $validated['text_ar'],
        'en' => $validated['text_en'],
    ],
]);

        $item->save();

        return back()->with('success', 'تمت الإضافة بنجاح.');
    }

    public function destroy(FooterText $footerText): RedirectResponse
    {
        $footerText->delete();

        return back()->with('success', 'تم الحذف بنجاح.');
    }
}