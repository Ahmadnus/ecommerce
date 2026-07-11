<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\PageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function __construct(
        private readonly PageService $pages,
    ) {}

    public function index(): View
    {
        $pages = $this->pages->getOrderedPages();
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
            'slug'            => 'nullable|string|max:255|unique:pages,slug',
            'sort_order'      => 'nullable|integer|min:0',
            'is_active'       => 'nullable|boolean',
            'featured_image'  => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:5120',
            'image_alt'       => 'nullable|string|max:255',
        ], [
            'name.ar.required'    => 'اسم الصفحة بالعربية مطلوب',
            'content.ar.required' => 'محتوى الصفحة بالعربية مطلوب',
        ]);

        try {
            $this->pages->create(
                data: [
                    'name'       => $request->input('name'),
                    'content'    => $request->input('content'),
                    'slug'       => $request->filled('slug') ? $request->slug : null,
                    'sort_order' => $request->input('sort_order', 0),
                    'is_active'  => $request->boolean('is_active', true),
                    'image_alt'  => $request->input('image_alt', ''),
                ],
                featuredImage: $request->hasFile('featured_image') ? $request->file('featured_image') : null,
            );
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الصفحة. يرجى المحاولة مرة أخرى.');
        }

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'تم إنشاء الصفحة "' . $request->input('name.ar') . '" بنجاح.');
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
            'slug'            => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'sort_order'      => 'nullable|integer|min:0',
            'is_active'       => 'nullable|boolean',
            'featured_image'  => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:5120',
            'image_alt'       => 'nullable|string|max:255',
        ], [
            'name.ar.required'    => 'اسم الصفحة بالعربية مطلوب',
            'content.ar.required' => 'محتوى الصفحة بالعربية مطلوب',
        ]);

        try {
            $this->pages->update(
                page: $page,
                data: [
                    'name'         => $request->input('name'),
                    'content'      => $request->input('content'),
                    'slug'         => $request->filled('slug') ? $request->slug : null,
                    'sort_order'   => $request->input('sort_order', 0),
                    'is_active'    => $request->boolean('is_active', true),
                    'image_alt'    => $request->input('image_alt', ''),
                    'remove_image' => $request->boolean('remove_image'),
                ],
                featuredImage: $request->hasFile('featured_image') ? $request->file('featured_image') : null,
            );
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الصفحة. يرجى المحاولة مرة أخرى.');
        }

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'تم تحديث الصفحة بنجاح.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $this->pages->delete($page);

        return redirect()
            ->route('admin.pages.index')
            ->with('success', 'تم حذف الصفحة.');
    }
}
