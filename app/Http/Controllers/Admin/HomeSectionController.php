<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HomeSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * HomeSectionController
 * ─────────────────────────────────────────────────────────────────────────────
 * Admin CRUD for the dynamic home page sections.
 * All routes are behind the 'manage-all' permission (super-admin only).
 *
 * Routes (add to admin route group):
 *   GET    /admin/home-sections                → index
 *   GET    /admin/home-sections/create         → create
 *   POST   /admin/home-sections                → store
 *   GET    /admin/home-sections/{id}/edit      → edit
 *   PUT    /admin/home-sections/{id}           → update
 *   DELETE /admin/home-sections/{id}           → destroy
 *   POST   /admin/home-sections/reorder        → reorder  (AJAX drag-drop)
 */
class HomeSectionController extends Controller
{
    public function index(): View
    {
        $sections = HomeSection::with('category')
            ->ordered()
            ->get();

        return view('admin.home-sections.index', compact('sections'));
    }

    public function create(): View
    {
        $categories = Category::active()->orderBy('sort_order')->get();
        $typeLabels = HomeSection::typeLabels();

        return view('admin.home-sections.create', compact('categories', 'typeLabels'));
    }

  

    public function edit(HomeSection $homeSection): View
    {
        $categories = Category::active()->orderBy('sort_order')->get();
        $typeLabels = HomeSection::typeLabels();

        return view('admin.home-sections.edit', compact('homeSection', 'categories', 'typeLabels'));
    }

  public function store(Request $request): RedirectResponse
{
    $validated = $this->validateSection($request);

    HomeSection::create($validated);

    return redirect()
        ->route('admin.home-sections.index')
        ->with('success', 'تم إضافة القسم بنجاح');
}

public function update(Request $request, HomeSection $homeSection): RedirectResponse
{
    $validated = $this->validateSection($request);

    $homeSection->update($validated);

    return redirect()
        ->route('admin.home-sections.index')
        ->with('success', 'تم تحديث القسم بنجاح');
}



private function validateSection(Request $request): array
{
    $validated = $request->validate([
        'title.ar'    => 'required|string|max:120',
        'title.en'    => 'required|string|max:120',
        'type'        => 'required|in:' . implode(',', array_keys(HomeSection::typeLabels())),
        'category_id' => 'nullable|exists:categories,id',
        'limit'       => 'nullable|integer|min:1|max:50',
        'sort_order'  => 'nullable|integer|min:0',
        'is_active'   => 'nullable|boolean',
    ]);

    // title arrives as ['ar' => '...', 'en' => '...'] — Spatie accepts this directly
    $validated['is_active']  = $request->boolean('is_active', true);
    $validated['sort_order'] = $validated['sort_order'] ?? 0;
    $validated['limit']      = $validated['limit'] ?? 10;

    if ($validated['type'] !== HomeSection::TYPE_CATEGORY) {
        $validated['category_id'] = null;
    }

    return $validated;
}

    public function destroy(HomeSection $homeSection): RedirectResponse
    {
        $homeSection->delete();

        return redirect()
            ->route('admin.home-sections.index')
            ->with('success', 'تم حذف القسم.');
    }

    /**
     * AJAX reorder — called when admin drags sections up/down.
     * Expects: { "order": [3, 1, 2] } — array of IDs in new order.
     */
    public function reorder(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['order' => 'required|array', 'order.*' => 'integer']);

        foreach ($request->order as $position => $id) {
            HomeSection::where('id', $id)->update(['sort_order' => $position + 1]);
        }

        return response()->json(['success' => true]);
    }

    // ── Private ────────────────────────────────────────────────────────────────

}