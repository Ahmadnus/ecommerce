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
        $parentOptions       = Category::active()
            ->with('media')                             // thumbnails in the parent selector
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
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|unique:categories,slug',
            'parent_id'   => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
            'image'       => 'nullable|image|max:4096|mimes:jpeg,png,jpg,webp,gif',
        ], [
            'name.required' => 'اسم التصنيف مطلوب',
            'image.max'     => 'حجم الصورة يجب أن لا يتجاوز 4 ميجابايت',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['is_active']  = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $category = Category::create($data);

        // ── Spatie: upload to 'category_images' collection ───────────────────
        if ($request->hasFile('image')) {
            $category
                ->addMediaFromRequest('image')
                ->usingName($category->name)
                ->usingFileName(Str::slug($category->name) . '.' . $request->file('image')->extension())
                ->toMediaCollection('category_images');
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
            'name'         => 'required|max:255',
            'slug'         => 'nullable|unique:categories,slug,' . $category->id,
            'description'  => 'nullable|string',
            'parent_id'    => 'nullable|exists:categories,id',
            'sort_order'   => 'nullable|integer|min:0',
            'is_active'    => 'nullable|boolean',
            'image'        => 'nullable|image|max:4096|mimes:jpeg,png,jpg,webp,gif',
            'remove_image' => 'nullable|boolean',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $data['is_active']  = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

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
            $category->clearMediaCollection('categories');   // legacy
        }

        // ── Replace/add image ────────────────────────────────────────────────
        if ($request->hasFile('image')) {
            // clearMediaCollection ensures singleFile — only one image per category
            $category->clearMediaCollection('category_images');
            $category
                ->addMediaFromRequest('image')
                ->usingName($category->name)
                ->usingFileName(Str::slug($category->name) . '.' . $request->file('image')->extension())
                ->toMediaCollection('category_images');
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