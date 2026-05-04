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
            ->with(['allChildren.products', 'media'])
            ->roots()
            ->orderBy('sort_order')
            ->orderBy('name')   // Spatie sorts by current locale automatically
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(Request $request)
    {
        $parentOptions = Category::active()
            ->with('media')
            ->orderBy('depth')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $preselectedParentId = $request->query('parent_id');

        return view('admin.categories.create', compact('parentOptions', 'preselectedParentId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            // ── Translatable ────────────────────────────────────────────────
            'name.ar'         => 'required|string|max:255',
            'name.en'         => 'nullable|string|max:255',
            'description.ar'  => 'nullable|string',
            'description.en'  => 'nullable|string',
            // ────────────────────────────────────────────────────────────────
            'slug'            => 'nullable|string|unique:categories,slug',
            'parent_id'       => 'nullable|exists:categories,id',
            'sort_order'      => 'nullable|integer|min:0',
            'is_active'       => 'nullable|boolean',
            'image'           => 'nullable|image|max:4096|mimes:jpeg,png,jpg,webp,gif',
            'banner_image'    => 'nullable|image|max:4096|mimes:jpeg,png,jpg,webp,gif',
            'banner_is_active'=> 'nullable|boolean',
        ], [
            'name.ar.required' => 'اسم التصنيف بالعربية مطلوب',
            'image.max'        => 'حجم الصورة يجب أن لا يتجاوز 4 ميجابايت',
        ]);

        $arName = $request->input('name.ar');
        $enName = $request->input('name.en');

        $category = Category::create([
            // Pass the full array — Spatie stores it as {"ar": "...", "en": "..."}
            'name'             => $request->input('name'),
            'description'      => $request->input('description'),
            'slug'             => $request->filled('slug')
                                    ? $request->slug
                                    : Str::slug($arName ?: $enName),
            'parent_id'        => $request->parent_id,
            'sort_order'       => $request->input('sort_order', 0),
            'is_active'        => $request->boolean('is_active', true),
            'banner_is_active' => $request->boolean('banner_is_active', false),
        ]);

        if ($request->hasFile('image')) {
            $category
                ->addMediaFromRequest('image')
                ->usingName($arName ?: $enName)
                ->usingFileName(Str::slug($arName ?: $enName) . '.' . $request->file('image')->extension())
                ->toMediaCollection('category_images');
        }

        if ($request->hasFile('banner_image')) {
            $category
                ->addMediaFromRequest('banner_image')
                ->usingName(($arName ?: $enName) . ' Banner')
                ->usingFileName(Str::slug($arName ?: $enName) . '-banner.' . $request->file('banner_image')->extension())
                ->toMediaCollection('category_banner');
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'تم إضافة التصنيف "' . $arName . '" بنجاح');
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
        $request->validate([
            // ── Translatable ────────────────────────────────────────────────
            'name.ar'         => 'required|string|max:255',
            'name.en'         => 'nullable|string|max:255',
            'description.ar'  => 'nullable|string',
            'description.en'  => 'nullable|string',
            // ────────────────────────────────────────────────────────────────
            'slug'            => 'nullable|string|unique:categories,slug,' . $category->id,
        
            'parent_id'       => 'nullable|exists:categories,id',
            'sort_order'      => 'nullable|integer|min:0',
            'is_active'       => 'nullable|boolean',
            'image'           => 'nullable|image|max:4096|mimes:jpeg,png,jpg,webp,gif',
            'remove_image'    => 'nullable|boolean',
            'banner_image'    => 'nullable|image|max:4096|mimes:jpeg,png,jpg,webp,gif',
            'remove_banner_image' => 'nullable|boolean',
            'banner_is_active'=> 'nullable|boolean',
        ]);

        // Prevent circular parent
        if ($request->filled('parent_id')) {
            $descendants = $category->getAllDescendants()->pluck('id');
            if ($descendants->contains($request->parent_id) || $request->parent_id == $category->id) {
                return back()->withErrors(['parent_id' => 'لا يمكن تعيين تصنيف فرعي كأب.']);
            }
        }

        $arName = $request->input('name.ar');
        $enName = $request->input('name.en');

        // Regenerate slug only if Arabic name changed
        $slug = $category->slug;
        if ($arName !== $category->getTranslation('name', 'ar', false)) {
            $slug = $request->filled('slug')
                ? $request->slug
                : Str::slug($arName ?: $enName);
        }

        $category->update([
            'name'             => $request->input('name'),
            'description'      => $request->input('description'),
            'slug'             => $slug,
            'parent_id'        => $request->parent_id,
            'sort_order'       => $request->input('sort_order', 0),
            'is_active'        => $request->boolean('is_active', true),
            'banner_is_active' => $request->boolean('banner_is_active', false),
        ]);

        // ── Image handling ─────────────────────────────────────────────────
        if ($request->boolean('remove_image')) {
            $category->clearMediaCollection('category_images');
            $category->clearMediaCollection('categories'); // legacy
        }

        if ($request->hasFile('image')) {
            $category->clearMediaCollection('category_images');
            $category
                ->addMediaFromRequest('image')
                ->usingName($arName ?: $enName)
                ->usingFileName(Str::slug($arName ?: $enName) . '.' . $request->file('image')->extension())
                ->toMediaCollection('category_images');
        }

        if ($request->boolean('remove_banner_image')) {
            $category->clearMediaCollection('category_banner');
        }

        if ($request->hasFile('banner_image')) {
            $category->clearMediaCollection('category_banner');
            $category
                ->addMediaFromRequest('banner_image')
                ->usingName(($arName ?: $enName) . ' Banner')
                ->usingFileName(Str::slug($arName ?: $enName) . '-banner.' . $request->file('banner_image')->extension())
                ->toMediaCollection('category_banner');
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'تم تحديث التصنيف "' . $arName . '" بنجاح');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'تم حذف التصنيف');
    }
}