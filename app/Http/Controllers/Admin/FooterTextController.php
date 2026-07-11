<?php
// app/Http/Controllers/Admin/FooterTextController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterText;
use App\Services\FooterTextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FooterTextController extends Controller
{
    public function __construct(
        private readonly FooterTextService $footerTexts,
    ) {}

    public function index(): View
    {
        $items = $this->footerTexts->getItems();

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

        $this->footerTexts->create([
            'slug'       => $validated['slug'],
            'text_ar'    => $validated['text_ar'],
            'text_en'    => $validated['text_en'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'تمت الإضافة بنجاح.');
    }

    public function destroy(FooterText $footerText): RedirectResponse
    {
        $this->footerTexts->delete($footerText);

        return back()->with('success', 'تم الحذف بنجاح.');
    }
}
