<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(): View
    {
        $pages = Page::ordered()->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function create(): View
    {
        return view('admin.pages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // ── Translatable ─────────────────────────────────────────────
            'name.ar'    => 'required|string|max:255',
            'name.en'    => 'nullable|string|max:255',
            'content.ar' => 'required|string',
            'content.en' => 'nullable|string',
            // ─────────────────────────────────────────────────────────────
            'slug'       => 'nullable|string|max:255|unique:pages,slug',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ], [
            'name.ar.required'    => 'اسم الصفحة بالعربية مطلوب',
            'content.ar.required' => 'محتوى الصفحة بالعربية مطلوب',
        ]);

        $arName = $request->input('name.ar');
        $enName = $request->input('name.en');

        $slug = $request->filled('slug')
            ? $request->slug
            : Page::uniqueSlug($arName ?: $enName);

        Page::create([
            'name'       => $request->input('name'),       // ['ar' => ..., 'en' => ...]
            'content'    => $request->input('content'),    // ['ar' => ..., 'en' => ...]
            'slug'       => $slug,
            'sort_order' => $request->input('sort_order', 0),
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'تم إنشاء الصفحة "' . $arName . '" بنجاح.');
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $request->validate([
            'name.ar'    => 'required|string|max:255',
            'name.en'    => 'nullable|string|max:255',
            'content.ar' => 'required|string',
            'content.en' => 'nullable|string',
            'slug'       => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ], [
            'name.ar.required'    => 'اسم الصفحة بالعربية مطلوب',
            'content.ar.required' => 'محتوى الصفحة بالعربية مطلوب',
        ]);

        $arName = $request->input('name.ar');
        $enName = $request->input('name.en');

        // Regenerate slug only if Arabic name changed
        $slug = $page->slug;
        if ($arName !== $page->getTranslation('name', 'ar', false)) {
            $slug = $request->filled('slug')
                ? $request->slug
                : Page::uniqueSlug($arName ?: $enName, $page->id);
        }

        $page->update([
            'name'       => $request->input('name'),
            'content'    => $request->input('content'),
            'slug'       => $slug,
            'sort_order' => $request->input('sort_order', 0),
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'تم تحديث الصفحة بنجاح.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'تم حذف الصفحة.');
    }
}