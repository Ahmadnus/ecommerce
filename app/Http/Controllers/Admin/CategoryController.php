<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
            ->with(['allChildren.products', 'media'])   // eager-load media — no N+1
            ->roots()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(Request $request)
    {
        $parentOptions = Category::active()
            ->with('media') // thumbnails in the parent selector
            ->orderBy('depth')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $preselectedParentId = $request->query('parent_id');

        return view('admin.categories.create', compact('parentOptions', 'preselectedParentId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                    => 'required|string|max:255',
            'slug'                    => 'nullable|string|unique:categories,slug',
            'parent_id'               => 'nullable|exists:categories,id',
            'description'             => 'nullable|string',
            'sort_order'              => 'nullable|integer|min:0',
            'is_active'               => 'nullable|boolean',
            'image'                   => 'nullable|image|max:4096|mimes:jpeg,png,jpg,webp,gif',

            'banner_title'            => 'nullable|string|max:255',
            'banner_subtitle'         => 'nullable|string|max:255',
            'banner_button_text'      => 'nullable|string|max:100',
            'banner_button_url'       => 'nullable|string|max:500',
            'banner_background_color'  => 'nullable|string|max:20',
            'banner_text_color'        => 'nullable|string|max:20',
            'banner_is_active'         => 'nullable|boolean',
            'banner_image'             => 'nullable|image|max:4096|mimes:jpeg,png,jpg,webp,gif',
        ], [
            'name.required' => 'اسم التصنيف مطلوب',
            'image.max'     => 'حجم الصورة يجب أن لا يتجاوز 4 ميجابايت',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['is_active']        = $request->boolean('is_active', true);
        $data['banner_is_active'] = $request->boolean('banner_is_active', false);
        $data['sort_order']       = $data['sort_order'] ?? 0;

        $category = Category::create($data);

        // ── Spatie: upload category image ───────────────────────────────────
        if ($request->hasFile('image')) {
            $category
                ->addMediaFromRequest('image')
                ->usingName($category->name)
                ->usingFileName(Str::slug($category->name) . '.' . $request->file('image')->extension())
                ->toMediaCollection('category_images');
        }

        // ── Spatie: upload category banner image ───────────────────────────
        if ($request->hasFile('banner_image')) {
            $category
                ->addMediaFromRequest('banner_image')
                ->usingName($category->name . ' Banner')
                ->usingFileName(Str::slug($category->name) . '-banner.' . $request->file('banner_image')->extension())
                ->toMediaCollection('category_banner');
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'تم إضافة التصنيف "' . $category->name . '" بنجاح');
    }

    public function edit(Category $category)
    {
        $descendantIds = $category->getAllDescendants()->pluck('id')->push($category->id);

        $parentOptions = Category::active()
            ->with('media')
            ->whereNotIn('id', $descendantIds)
            ->orderBy('depth')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentOptions'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'                    => 'required|string|max:255',
            'slug'                    => 'nullable|string|unique:categories,slug,' . $category->id,
            'description'             => 'nullable|string',
            'parent_id'               => 'nullable|exists:categories,id',
            'sort_order'              => 'nullable|integer|min:0',
            'is_active'               => 'nullable|boolean',
            'image'                   => 'nullable|image|max:4096|mimes:jpeg,png,jpg,webp,gif',
            'remove_image'            => 'nullable|boolean',

            'banner_title'            => 'nullable|string|max:255',
            'banner_subtitle'         => 'nullable|string|max:255',
            'banner_button_text'      => 'nullable|string|max:100',
            'banner_button_url'       => 'nullable|string|max:500',
            'banner_background_color'  => 'nullable|string|max:20',
            'banner_text_color'        => 'nullable|string|max:20',
            'banner_is_active'         => 'nullable|boolean',
            'banner_image'             => 'nullable|image|max:4096|mimes:jpeg,png,jpg,webp,gif',
            'remove_banner_image'      => 'nullable|boolean',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['is_active']        = $request->boolean('is_active', true);
        $data['sort_order']       = $data['sort_order'] ?? 0;
        $data['banner_is_active'] = $request->boolean('banner_is_active', false);

        // Prevent circular parent assignment
        if (!empty($data['parent_id'])) {
            $descendants = $category->getAllDescendants()->pluck('id');

            if ($descendants->contains($data['parent_id']) || $data['parent_id'] == $category->id) {
                return back()->withErrors(['parent_id' => 'لا يمكن تعيين تصنيف فرعي كأب.']);
            }
        }

        $category->update($data);

        // ── Handle image removal ─────────────────────────────────────────────
        if ($request->boolean('remove_image')) {
            $category->clearMediaCollection('category_images');
            $category->clearMediaCollection('categories'); // legacy
        }

        // ── Replace/add category image ──────────────────────────────────────
        if ($request->hasFile('image')) {
            $category->clearMediaCollection('category_images');
            $category
                ->addMediaFromRequest('image')
                ->usingName($category->name)
                ->usingFileName(Str::slug($category->name) . '.' . $request->file('image')->extension())
                ->toMediaCollection('category_images');
        }

        // ── Handle banner removal ────────────────────────────────────────────
        if ($request->boolean('remove_banner_image')) {
            $category->clearMediaCollection('category_banner');
        }

        // ── Replace/add banner image ─────────────────────────────────────────
        if ($request->hasFile('banner_image')) {
            $category->clearMediaCollection('category_banner');
            $category
                ->addMediaFromRequest('banner_image')
                ->usingName($category->name . ' Banner')
                ->usingFileName(Str::slug($category->name) . '-banner.' . $request->file('banner_image')->extension())
                ->toMediaCollection('category_banner');
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'تم تحديث التصنيف "' . $category->name . '" بنجاح');
    }

    public function destroy(Category $category)
    {
        // Spatie automatically deletes media files when the model is deleted
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'تم حذف التصنيف');
    }
}