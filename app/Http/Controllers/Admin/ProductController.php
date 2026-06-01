<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Product::with([
            'categories',
            'variants'                     => fn($q) => $q->orderBy('is_active', 'desc'),
            'variants.attributeValues.attribute',
            'media',
        ])->withCount('variants');

        if ($request->filled('search')) {
            $query->where(fn($q) => $q
                ->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('sku', 'like', '%' . $request->search . '%'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', fn($q) => $q->where('categories.id', $request->category));
        }

        match ($request->get('sort', 'newest')) {
            'name_asc'   => $query->orderBy('name'),
            'name_desc'  => $query->orderByDesc('name'),
            'price_asc'  => $query->orderBy('base_price'),
            'price_desc' => $query->orderByDesc('base_price'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(20)->withQueryString();
        $categories = Category::active()->orderBy('name')->get();

        $stats = [
            'total'  => Product::count(),
            'active' => Product::where('status', 'active')->count(),
        ];

        return view('admin.products.index', compact('products', 'categories', 'stats'));
    }

    // ─── Create ───────────────────────────────────────────────────────────────

    public function create()
    {
        $categories = Category::active()->roots()
            ->with('allActiveChildren')->orderBy('sort_order')->get();

        return view('admin.products.create', compact('categories'));
    }

    // ─── Store ────────────────────────────────────────────────────────────────
    // Variants are NOT submitted from the create form.
    // A single default variant (no attributes) is created automatically.

    public function store(Request $request)
    {
        $request->validate([
            'name.ar'              => 'required|string|max:255',
            'description.ar'       => 'nullable|string',
            'short_description.ar' => 'nullable|string|max:500',
            'base_price'           => 'required|numeric|min:0',
            'discount_price'       => 'nullable|numeric|min:0|lt:base_price',
            'sku'                  => 'nullable|string|max:100|unique:products,sku',
            'category_ids'         => 'required|array|min:1',
            'category_ids.*'       => 'exists:categories,id',
            'primary_category_id'  => 'required|exists:categories,id',
            'main_image'           => 'required|image|mimes:jpeg,png,jpg,webp,avif|max:5120',
            'product_images'       => 'nullable|array|max:20',
            'product_images.*'     => 'image|mimes:jpeg,png,jpg,webp,avif|max:5120',
        ], [
            'name.ar.required'             => 'اسم المنتج مطلوب.',
            'name.ar.max'                  => 'اسم المنتج يجب ألا يتجاوز 255 حرفاً.',
            'short_description.ar.max'     => 'الوصف المختصر يجب ألا يتجاوز 500 حرف.',
            'base_price.required'          => 'السعر الأساسي مطلوب.',
            'base_price.numeric'           => 'السعر الأساسي يجب أن يكون رقماً.',
            'base_price.min'               => 'السعر الأساسي يجب أن يكون صفراً أو أكثر.',
            'discount_price.numeric'       => 'سعر الخصم يجب أن يكون رقماً.',
            'discount_price.min'           => 'سعر الخصم يجب أن يكون صفراً أو أكثر.',
            'discount_price.lt'            => 'سعر الخصم يجب أن يكون أقل من السعر الأساسي.',
            'sku.unique'                   => 'رمز SKU مستخدم بالفعل، يرجى اختيار رمز آخر.',
            'category_ids.required'        => 'يجب اختيار تصنيف واحد على الأقل.',
            'category_ids.min'             => 'يجب اختيار تصنيف واحد على الأقل.',
            'primary_category_id.required' => 'يجب تحديد التصنيف الأساسي.',
            'main_image.required'          => 'صورة الغلاف مطلوبة.',
            'main_image.image'             => 'الملف المرفوع يجب أن يكون صورة.',
            'main_image.mimes'             => 'صورة الغلاف يجب أن تكون بصيغة: JPEG أو PNG أو WebP أو AVIF.',
            'main_image.max'               => 'حجم الصورة يجب ألا يتجاوز 5 ميغابايت.',
            'product_images.max'           => 'لا يمكن رفع أكثر من 20 صورة.',
            'product_images.*.image'       => 'كل ملف يجب أن يكون صورة.',
            'product_images.*.mimes'       => 'صور المعرض يجب أن تكون بصيغة: JPEG أو PNG أو WebP أو AVIF.',
            'product_images.*.max'         => 'حجم كل صورة يجب ألا يتجاوز 5 ميغابايت.',
        ]);

        DB::transaction(function () use ($request) {
            $product = Product::create([
                'name'              => ['ar' => $request->input('name.ar'), 'en' => ''],
                'description'       => ['ar' => $request->input('description.ar', ''), 'en' => ''],
                'short_description' => ['ar' => $request->input('short_description.ar', ''), 'en' => ''],
                'slug'              => $this->uniqueSlug($request->input('name.ar')),
                'base_price'        => $request->base_price,
                'discount_price'    => $request->discount_price,
                'sku'               => $request->sku,
                'status'            => $request->boolean('is_active', true) ? 'active' : 'draft',
                'is_featured'       => $request->boolean('is_featured'),
            ]);

            // Categories
            $pivot = [];
            foreach ($request->category_ids as $catId) {
                $pivot[(int) $catId] = [
                    'is_primary' => (int) $catId === (int) $request->primary_category_id,
                ];
            }
            $product->categories()->attach($pivot);

            // Main image
            if ($request->hasFile('main_image')) {
                $product->addMedia($request->file('main_image'))
                        ->toMediaCollection('main');
            }

            // Gallery images
            if ($request->hasFile('product_images')) {
                foreach ($request->file('product_images') as $index => $image) {
                    $product->addMedia($image)
                            ->withCustomProperties(['order' => $index])
                            ->toMediaCollection('products');
                }
            }

            // Auto-create one default variant (no attributes, no stock tracking)
            $product->variants()->create([
                'sku'            => $request->sku ?: strtoupper(Str::random(8)),
                'price_override' => null,
                'stock_quantity' => 0,
                'is_active'      => true,
            ]);
        });

        return redirect()->route('admin.products.index')
                         ->with('success', 'تم إضافة المنتج بنجاح ✓');
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function show(Product $product)
    {
        $product->load(['categories', 'variants.attributeValues.attribute', 'media']);

        return view('admin.products.show', compact('product'));
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────

    public function edit(Product $product)
    {
        $product->load(['categories', 'media']);

        $categories = Category::active()->roots()
            ->with('allActiveChildren')->orderBy('sort_order')->get();

        $selectedCatIds = $product->categories->pluck('id')->toArray();
        $primaryCatId   = $product->categories
            ->first(fn($c) => $c->pivot->is_primary)?->id
            ?? $product->categories->first()?->id;

        return view('admin.products.edit', compact(
            'product', 'categories', 'selectedCatIds', 'primaryCatId'
        ));
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name.ar'              => 'required|string|max:255',
            'description.ar'       => 'nullable|string',
            'short_description.ar' => 'nullable|string|max:500',
            'base_price'           => 'required|numeric|min:0',
            'discount_price'       => 'nullable|numeric|min:0|lt:base_price',
            'sku'                  => [
                'nullable', 'string', 'max:100',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],
            'category_ids'         => 'required|array|min:1',
            'category_ids.*'       => 'exists:categories,id',
            'primary_category_id'  => 'required|exists:categories,id',
            'product_images'       => 'nullable|array|max:20',
            'product_images.*'     => 'image|mimes:jpeg,png,jpg,webp,avif|max:5120',
            'delete_media_ids'     => 'nullable|array',
            'delete_media_ids.*'   => 'integer',
        ], [
            'name.ar.required'             => 'اسم المنتج مطلوب.',
            'name.ar.max'                  => 'اسم المنتج يجب ألا يتجاوز 255 حرفاً.',
            'short_description.ar.max'     => 'الوصف المختصر يجب ألا يتجاوز 500 حرف.',
            'base_price.required'          => 'السعر الأساسي مطلوب.',
            'base_price.numeric'           => 'السعر الأساسي يجب أن يكون رقماً.',
            'base_price.min'               => 'السعر الأساسي يجب أن يكون صفراً أو أكثر.',
            'discount_price.numeric'       => 'سعر الخصم يجب أن يكون رقماً.',
            'discount_price.min'           => 'سعر الخصم يجب أن يكون صفراً أو أكثر.',
            'discount_price.lt'            => 'سعر الخصم يجب أن يكون أقل من السعر الأساسي.',
            'sku.unique'                   => 'رمز SKU مستخدم لمنتج آخر.',
            'category_ids.required'        => 'يجب اختيار تصنيف واحد على الأقل.',
            'category_ids.min'             => 'يجب اختيار تصنيف واحد على الأقل.',
            'primary_category_id.required' => 'يجب تحديد التصنيف الأساسي.',
            'product_images.max'           => 'لا يمكن رفع أكثر من 20 صورة.',
            'product_images.*.image'       => 'كل ملف يجب أن يكون صورة.',
            'product_images.*.mimes'       => 'صور المعرض يجب أن تكون بصيغة: JPEG أو PNG أو WebP أو AVIF.',
            'product_images.*.max'         => 'حجم كل صورة يجب ألا يتجاوز 5 ميغابايت.',
        ]);

        DB::transaction(function () use ($request, $product) {
            $newArName = $request->input('name.ar');

            if ($newArName !== $product->getTranslation('name', 'ar')) {
                $product->slug = $this->uniqueSlug($newArName, $product->id);
            }

            $product->update([
                'name'              => ['ar' => $newArName, 'en' => $product->getTranslation('name', 'en')],
                'description'       => ['ar' => $request->input('description.ar', ''), 'en' => $product->getTranslation('description', 'en')],
                'short_description' => ['ar' => $request->input('short_description.ar', ''), 'en' => $product->getTranslation('short_description', 'en')],
                'base_price'        => $request->base_price,
                'discount_price'    => $request->discount_price,
                'sku'               => $request->sku,
                'status'            => $request->boolean('is_active', true) ? 'active' : 'draft',
                'is_featured'       => $request->boolean('is_featured'),
            ]);

            // Categories
            $pivot = [];
            foreach ($request->category_ids as $catId) {
                $pivot[(int) $catId] = [
                    'is_primary' => (int) $catId === (int) $request->primary_category_id,
                ];
            }
            $product->categories()->sync($pivot);

            // Delete flagged gallery images
            $deleteIds = $request->input('delete_media_ids', []);
            if (!empty($deleteIds)) {
                $product->media()->whereIn('id', $deleteIds)->get()->each(fn($m) => $m->delete());
            }

            // Replace main image if a new one was uploaded
            if ($request->hasFile('main_image')) {
                $product->clearMediaCollection('main');
                $product->addMedia($request->file('main_image'))
                        ->toMediaCollection('main');
            }

            // Add new gallery images
            if ($request->hasFile('product_images')) {
                $existingCount = $product->getMedia('products')->count();
                foreach ($request->file('product_images') as $index => $image) {
                    $product->addMedia($image)
                            ->withCustomProperties(['order' => $existingCount + $index])
                            ->toMediaCollection('products');
                }
            }
        });

        return redirect()->route('admin.products.show', $product)
                         ->with('success', 'تم تحديث المنتج بنجاح ✓');
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
                         ->with('success', 'تم نقل المنتج إلى سلة المحذوفات');
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function uniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug  = Str::slug($name);
        $query = Product::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        if ($query->exists()) {
            $slug .= '-' . Str::lower(Str::random(4));
        }

        return $slug;
    }

}