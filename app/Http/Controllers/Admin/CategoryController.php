<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Show the nested category tree.
     * Loads only ROOT categories; allChildren is eager-loaded recursively.
     */
    public function index()
    {
        $categories = Category::withCount('products')
            ->with('allChildren.products')   // recursive + product counts for children
            ->roots()                        // whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(Request $request)
    {
        $parentOptions = Category::active()
            ->orderBy('depth')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Pre-select parent if coming from "add sub-category" button
        $preselectedParentId = $request->query('parent_id');

        return view('admin.categories.create', compact('parentOptions', 'preselectedParentId'));
    }

  public function store(Request $request)
{
    // 1. التحقق من البيانات الأساسية للمنتج + مصفوفة المتغيرات
    $validated = $request->validate([
        'name'          => 'required|string|max:255',
        'base_price'    => 'required|numeric|min:0',
        'category_id'   => 'required|exists:categories,id',
        'description'   => 'nullable|string',
        // التحقق من مصفوفة المتغيرات القادمة من الداشبورد
        'variants'      => 'required|array|min:1',
        'variants.*.sku'   => 'required|string|unique:product_variants,sku',
        'variants.*.stock' => 'required|integer|min:0',
        'variants.*.price' => 'nullable|numeric|min:0',
        'variants.*.attribute_values' => 'required|array', // مصفوفة الـ IDs للسمات (لون، قياس..)
    ]);

    // 2. إنشاء المنتج الأساسي
    $product = Product::create([
        'name'        => $validated['name'],
        'slug'        => Str::slug($validated['name']),
        'base_price'  => $validated['base_price'],
        'description' => $validated['description'],
        'status'      => 'active',
    ]);

    // 3. ربط القسم (Category)
    $product->categories()->attach($validated['category_id'], ['is_primary' => true]);

    // 4. إنشاء المتغيرات (Variants) وربطها بالسمات
    foreach ($request->variants as $vData) {
        // إنشاء الخيار (النسخة)
        $variant = $product->variants()->create([
            'sku'            => $vData['sku'],
            'stock_quantity' => $vData['stock'],
            'price_override' => $vData['price'] ?? null, // إذا كان سعر الخيار يختلف عن السعر الأساسي
            'is_active'      => true,
        ]);

        // ربط "السمات" (مثل ID اللون الأخضر و ID قياس XXL) بهذا الـ Variant
        // هذا هو الجزء الذي يجعلها تظهر في صفحة العرض
        $variant->attributeValues()->attach($vData['attribute_values']);
    }

    return redirect()->route('admin.products.index')->with('success', 'تم إضافة المنتج والخيارات بنجاح');
}

    public function edit(Category $category)
    {
        // Exclude self + descendants to prevent circular references
        $descendantIds = $category->getAllDescendants()->pluck('id')->push($category->id);

        $parentOptions = Category::active()
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
            'name'        => 'required|max:255',
            'slug'        => 'nullable|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:categories,id',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
            'image'       => 'nullable|image|max:2048',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $data['is_active'] = $request->boolean('is_active', true);

        // Guard: prevent assigning own descendant as parent
        if (!empty($data['parent_id'])) {
            $descendantIds = $category->getAllDescendants()->pluck('id');
            if ($descendantIds->contains($data['parent_id']) || $data['parent_id'] == $category->id) {
                return back()->withErrors(['parent_id' => 'لا يمكن تعيين تصنيف فرعي كأب.']);
            }
        }

        $category->update($data);

        if ($request->hasFile('image')) {
            $category->clearMediaCollection('categories');
            $category->addMediaFromRequest('image')->toMediaCollection('categories');
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'تم تحديث التصنيف "' . $category->name . '" بنجاح');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'تم حذف التصنيف');
    }
}