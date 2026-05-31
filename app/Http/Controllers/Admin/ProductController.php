<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    private const LOW_STOCK_THRESHOLD = 5;

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
        if ($request->filled('stock')) {
            match ($request->stock) {
                'out'   => $query->whereHas('variants', fn($q) => $q->where('stock_quantity', 0)),
                'low'   => $query->whereHas('variants', fn($q) => $q
                               ->where('stock_quantity', '>', 0)
                               ->where('stock_quantity', '<=', self::LOW_STOCK_THRESHOLD)),
                default => null,
            };
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
            'out'    => ProductVariant::where('stock_quantity', 0)->count(),
            'low'    => ProductVariant::where('stock_quantity', '>', 0)
                                      ->where('stock_quantity', '<=', self::LOW_STOCK_THRESHOLD)->count(),
        ];

        return view('admin.products.index', compact('products', 'categories', 'stats'));
    }

    // ─── Create ───────────────────────────────────────────────────────────────

    public function create()
    {
        $categories = Category::active()->roots()
            ->with('allActiveChildren')->orderBy('sort_order')->get();
        $attributes = Attribute::with('values')->orderBy('sort_order')->get();

        return view('admin.products.create', compact('categories', 'attributes'));
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $messages = [
            'name.ar.required'                  => 'اسم المنتج بالعربية مطلوب.',
            'name.ar.max'                       => 'اسم المنتج بالعربية يجب ألا يتجاوز 255 حرفاً.',
            'name.en.max'                       => 'اسم المنتج بالإنجليزية يجب ألا يتجاوز 255 حرفاً.',
            'short_description.ar.max'          => 'الوصف المختصر بالعربية يجب ألا يتجاوز 500 حرف.',
            'short_description.en.max'          => 'الوصف المختصر بالإنجليزية يجب ألا يتجاوز 500 حرف.',
            'base_price.required'               => 'السعر الأساسي مطلوب.',
            'base_price.numeric'                => 'السعر الأساسي يجب أن يكون رقماً.',
            'base_price.min'                    => 'السعر الأساسي يجب أن يكون صفراً أو أكثر.',
            'discount_price.numeric'            => 'سعر الخصم يجب أن يكون رقماً.',
            'discount_price.min'                => 'سعر الخصم يجب أن يكون صفراً أو أكثر.',
            'discount_price.lt'                 => 'سعر الخصم يجب أن يكون أقل من السعر الأساسي.',
            'sku.unique'                        => 'رمز SKU مستخدم بالفعل، يرجى اختيار رمز آخر.',
            'category_ids.required'             => 'يجب اختيار تصنيف واحد على الأقل.',
            'category_ids.min'                  => 'يجب اختيار تصنيف واحد على الأقل.',
            'category_ids.*.exists'             => 'أحد التصنيفات المختارة غير موجود.',
            'primary_category_id.required'      => 'يجب تحديد التصنيف الأساسي للمنتج.',
            'primary_category_id.exists'        => 'التصنيف الأساسي المحدد غير موجود.',
            'main_image.required'               => 'صورة الغلاف مطلوبة.',
            'main_image.image'                  => 'الملف المرفوع يجب أن يكون صورة.',
            'main_image.mimes'                  => 'صورة الغلاف يجب أن تكون بصيغة: JPEG، PNG، WebP، أو AVIF.',
            'main_image.max'                    => 'حجم صورة الغلاف يجب ألا يتجاوز 5 ميغابايت.',
            'product_images.max'                => 'لا يمكن رفع أكثر من 20 صورة للمعرض.',
            'product_images.*.image'            => 'كل ملف في المعرض يجب أن يكون صورة.',
            'product_images.*.mimes'            => 'صور المعرض يجب أن تكون بصيغة: JPEG، PNG، WebP، أو AVIF.',
            'product_images.*.max'              => 'حجم كل صورة في المعرض يجب ألا يتجاوز 5 ميغابايت.',
            'variants.required'                 => 'يجب إضافة متغير واحد على الأقل.',
            'variants.min'                      => 'يجب إضافة متغير واحد على الأقل.',
            'variants.*.stock_quantity.integer' => 'كمية المخزون يجب أن تكون عدداً صحيحاً.',
            'variants.*.stock_quantity.min'     => 'كمية المخزون يجب أن تكون صفراً أو أكثر.',
            'variants.*.price_override.numeric' => 'سعر المتغير يجب أن يكون رقماً.',
            'variants.*.price_override.min'     => 'سعر المتغير يجب أن يكون صفراً أو أكثر.',
            'variants.*.sku.max'                => 'رمز SKU للمتغير يجب ألا يتجاوز 100 حرف.',
            'variants.*.attribute_values.*.exists' => 'قيمة الخاصية المختارة غير موجودة.',
        ];

        $request->validate([
            'name.ar'              => 'required|string|max:255',
            'name.en'              => 'nullable|string|max:255',
            'description.ar'       => 'nullable|string',
            'description.en'       => 'nullable|string',
            'short_description.ar' => 'nullable|string|max:500',
            'short_description.en' => 'nullable|string|max:500',
            'base_price'           => 'required|numeric|min:0',
            'discount_price'       => 'nullable|numeric|min:0|lt:base_price',
            'sku'                  => 'nullable|unique:products,sku',
            'category_ids'         => 'required|array|min:1',
            'category_ids.*'       => 'exists:categories,id',
            'primary_category_id'  => 'required|exists:categories,id',
            'main_image'           => 'required|image|mimes:jpeg,png,jpg,webp,avif|max:5120',
            'product_images'       => 'nullable|array|max:20',
            'product_images.*'     => 'image|mimes:jpeg,png,jpg,webp,avif|max:5120',
            'variants'                      => 'required|array|min:1',
            'variants.*.stock_quantity'     => 'required|integer|min:0',
            'variants.*.price_override'     => 'nullable|numeric|min:0',
            'variants.*.sku'                => 'nullable|string|max:100',
            'variants.*.attribute_values'   => 'nullable|array',
            'variants.*.attribute_values.*' => 'exists:attribute_values,id',
        ], $messages);

        DB::transaction(function () use ($request) {
            $product = Product::create([
                'name'              => $request->input('name'),
                'description'       => $request->input('description'),
                'short_description' => $request->input('short_description'),
                'slug'              => $this->uniqueSlug(
                                           $request->input('name.ar') ?: $request->input('name.en')
                                       ),
                'base_price'        => $request->base_price,
                'discount_price'    => $request->discount_price,
                'sku'               => $request->sku,
                'status'            => $request->boolean('is_active', true) ? 'active' : 'draft',
                'is_featured'       => $request->boolean('is_featured'),
            ]);

            $pivot = [];
            foreach ($request->category_ids as $catId) {
                $pivot[(int) $catId] = [
                    'is_primary' => (int) $catId === (int) $request->primary_category_id,
                ];
            }
            $product->categories()->attach($pivot);

            if ($request->hasFile('main_image')) {
                $product->addMedia($request->file('main_image'))
                        ->toMediaCollection('main');
            }

            if ($request->hasFile('product_images')) {
                foreach ($request->file('product_images') as $index => $image) {
                    $product->addMedia($image)
                            ->withCustomProperties(['order' => $index])
                            ->toMediaCollection('products');
                }
            }

            foreach ($request->variants as $variantData) {
                $this->createVariant($product, $variantData);
            }
        });

        return redirect()->route('admin.products.index')
                         ->with('success', 'تم إضافة المنتج بنجاح');
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function show(Product $product)
    {
        $product->load(['categories', 'variants.attributeValues.attribute', 'media']);
        $lowThreshold = self::LOW_STOCK_THRESHOLD;

        return view('admin.products.show', compact('product', 'lowThreshold'));
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────

    public function edit(Product $product)
    {
        $product->load(['categories', 'variants.attributeValues.attribute']);

        $categories = Category::active()->roots()
            ->with('allActiveChildren')->orderBy('sort_order')->get();
        $attributes = Attribute::with('values')->orderBy('sort_order')->get();

        $selectedCatIds = $product->categories->pluck('id')->toArray();
        $primaryCatId   = $product->categories
            ->first(fn($c) => $c->pivot->is_primary)?->id
            ?? $product->categories->first()?->id;

        $existingVariants = $product->variants->map(fn($v) => [
            'id'               => $v->id,
            'sku'              => $v->sku,
            'stock_quantity'   => $v->stock_quantity,
            'price_override'   => $v->price_override,
            'is_active'        => $v->is_active,
            'attribute_values' => $v->attributeValues->pluck('id')->toArray(),
        ]);

        $existingImages = $product->getMedia('products')->map(fn($m) => [
            'id'  => $m->id,
            'url' => $m->getUrl(),
        ]);

        return view('admin.products.edit', compact(
            'product', 'categories', 'attributes',
            'selectedCatIds', 'primaryCatId', 'existingVariants', 'existingImages'
        ));
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(Request $request, Product $product)
    {
        $messages = [
            'name.ar.required'                  => 'اسم المنتج بالعربية مطلوب.',
            'name.ar.max'                       => 'اسم المنتج بالعربية يجب ألا يتجاوز 255 حرفاً.',
            'name.en.max'                       => 'اسم المنتج بالإنجليزية يجب ألا يتجاوز 255 حرفاً.',
            'short_description.ar.max'          => 'الوصف المختصر بالعربية يجب ألا يتجاوز 500 حرف.',
            'short_description.en.max'          => 'الوصف المختصر بالإنجليزية يجب ألا يتجاوز 500 حرف.',
            'base_price.required'               => 'السعر الأساسي مطلوب.',
            'base_price.numeric'                => 'السعر الأساسي يجب أن يكون رقماً.',
            'base_price.min'                    => 'السعر الأساسي يجب أن يكون صفراً أو أكثر.',
            'discount_price.numeric'            => 'سعر الخصم يجب أن يكون رقماً.',
            'discount_price.min'                => 'سعر الخصم يجب أن يكون صفراً أو أكثر.',
            'discount_price.lt'                 => 'سعر الخصم يجب أن يكون أقل من السعر الأساسي.',
            'sku.unique'                        => 'رمز SKU مستخدم بالفعل لمنتج آخر، يرجى اختيار رمز مختلف.',
            'category_ids.required'             => 'يجب اختيار تصنيف واحد على الأقل.',
            'category_ids.min'                  => 'يجب اختيار تصنيف واحد على الأقل.',
            'category_ids.*.exists'             => 'أحد التصنيفات المختارة غير موجود.',
            'primary_category_id.required'      => 'يجب تحديد التصنيف الأساسي للمنتج.',
            'primary_category_id.exists'        => 'التصنيف الأساسي المحدد غير موجود.',
            'product_images.max'                => 'لا يمكن رفع أكثر من 20 صورة للمعرض.',
            'product_images.*.image'            => 'كل ملف في المعرض يجب أن يكون صورة.',
            'product_images.*.mimes'            => 'صور المعرض يجب أن تكون بصيغة: JPEG، PNG، WebP، أو AVIF.',
            'product_images.*.max'              => 'حجم كل صورة في المعرض يجب ألا يتجاوز 5 ميغابايت.',
            'delete_media_ids.*.integer'        => 'معرّف الصورة المحذوفة يجب أن يكون رقماً صحيحاً.',
            'variants.required'                 => 'يجب إضافة متغير واحد على الأقل.',
            'variants.min'                      => 'يجب إضافة متغير واحد على الأقل.',
            'variants.*.stock_quantity.integer' => 'كمية المخزون يجب أن تكون عدداً صحيحاً.',
            'variants.*.stock_quantity.min'     => 'كمية المخزون يجب أن تكون صفراً أو أكثر.',
            'variants.*.price_override.numeric' => 'سعر المتغير يجب أن يكون رقماً.',
            'variants.*.price_override.min'     => 'سعر المتغير يجب أن يكون صفراً أو أكثر.',
            'variants.*.sku.max'                => 'رمز SKU للمتغير يجب ألا يتجاوز 100 حرف.',
            'variants.*.attribute_values.*.exists' => 'قيمة الخاصية المختارة غير موجودة.',
        ];

        $request->validate([
            'name.ar'              => 'required|string|max:255',
            'name.en'              => 'nullable|string|max:255',
            'description.ar'       => 'nullable|string',
            'description.en'       => 'nullable|string',
            'short_description.ar' => 'nullable|string|max:500',
            'short_description.en' => 'nullable|string|max:500',
            'base_price'           => 'required|numeric|min:0',
            'discount_price'       => 'nullable|numeric|min:0|lt:base_price',

            // FIX: ignore this product's own SKU when checking for uniqueness
            // so the user can save the form without touching the SKU field.
            // Without this, update() would reject the existing SKU as a duplicate.
            'sku'                  => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('products', 'sku')->ignore($product->id),
            ],

            'category_ids'         => 'required|array|min:1',
            'category_ids.*'       => 'exists:categories,id',
            'primary_category_id'  => 'required|exists:categories,id',
            'product_images'       => 'nullable|array|max:20',
            'product_images.*'     => 'image|mimes:jpeg,png,jpg,webp,avif|max:5120',
            'delete_media_ids'     => 'nullable|array',
            'delete_media_ids.*'   => 'integer',
            'variants'                      => 'required|array|min:1',
            'variants.*.stock_quantity'     => 'required|integer|min:0',
            'variants.*.price_override'     => 'nullable|numeric|min:0',
            'variants.*.sku'                => 'nullable|string|max:100',
            'variants.*.attribute_values'   => 'nullable|array',
            'variants.*.attribute_values.*' => 'exists:attribute_values,id',
        ], $messages);

        DB::transaction(function () use ($request, $product) {
            $newArName = $request->input('name.ar');
            $newEnName = $request->input('name.en');

            if ($newArName !== $product->getTranslation('name', 'ar')) {
                $product->slug = $this->uniqueSlug($newArName ?: $newEnName, $product->id);
            }

            $product->update([
                'name'              => $request->input('name'),
                'description'       => $request->input('description'),
                'short_description' => $request->input('short_description'),
                'base_price'        => $request->base_price,
                'discount_price'    => $request->discount_price,
                'sku'               => $request->sku,
                'status'            => $request->boolean('is_active', true) ? 'active' : 'draft',
                'is_featured'       => $request->boolean('is_featured'),
            ]);

            $pivot = [];
            foreach ($request->category_ids as $catId) {
                $pivot[(int) $catId] = [
                    'is_primary' => (int) $catId === (int) $request->primary_category_id,
                ];
            }
            $product->categories()->sync($pivot);

            $deleteIds = $request->input('delete_media_ids', []);
            if (!empty($deleteIds)) {
                $product->media()->whereIn('id', $deleteIds)->get()->each(fn($m) => $m->delete());
            }

            if ($request->hasFile('main_image')) {
                $product->clearMediaCollection('main');
                $product->addMedia($request->file('main_image'))
                        ->toMediaCollection('main');
            }

            if ($request->hasFile('product_images')) {
                $existingCount = $product->getMedia('products')->count();
                foreach ($request->file('product_images') as $index => $image) {
                    $product->addMedia($image)
                            ->withCustomProperties(['order' => $existingCount + $index])
                            ->toMediaCollection('products');
                }
            }

            $product->variants()->forceDelete();
            foreach ($request->variants as $variantData) {
                $this->createVariant($product, $variantData);
            }
        });

        return redirect()->route('admin.products.show', $product)
                         ->with('success', 'تم تحديث المنتج بنجاح');
    }

    // ─── Stock update ─────────────────────────────────────────────────────────

    public function updateStock(Request $request, Product $product)
    {
        $messages = [
            'variants.required'                 => 'بيانات المتغيرات مطلوبة.',
            'variants.*.id.required'            => 'معرّف المتغير مطلوب.',
            'variants.*.id.exists'              => 'المتغير المحدد غير موجود.',
            'variants.*.stock_quantity.required'=> 'كمية المخزون مطلوبة.',
            'variants.*.stock_quantity.integer' => 'كمية المخزون يجب أن تكون عدداً صحيحاً.',
            'variants.*.stock_quantity.min'     => 'كمية المخزون يجب أن تكون صفراً أو أكثر.',
            'variants.*.price_override.numeric' => 'سعر المتغير يجب أن يكون رقماً.',
            'variants.*.price_override.min'     => 'سعر المتغير يجب أن يكون صفراً أو أكثر.',
        ];

        $request->validate([
            'variants'                  => 'required|array',
            'variants.*.id'             => 'required|exists:product_variants,id',
            'variants.*.stock_quantity' => 'required|integer|min:0',
            'variants.*.price_override' => 'nullable|numeric|min:0',
            'variants.*.is_active'      => 'nullable|boolean',
        ], $messages);

        foreach ($request->variants as $data) {
            ProductVariant::where('id', $data['id'])
                ->where('product_id', $product->id)
                ->update([
                    'stock_quantity' => $data['stock_quantity'],
                    'price_override' => $data['price_override'] ?: null,
                    'is_active'      => (bool) ($data['is_active'] ?? true),
                ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'تم تحديث المخزون']);
        }

        return back()->with('success', 'تم تحديث المخزون بنجاح');
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
                         ->with('success', 'تم نقل المنتج إلى المحذوفات');
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

    private function createVariant(Product $product, array $data): ProductVariant
    {
        $variant = $product->variants()->create([
            'sku'            => $data['sku'] ?: strtoupper(Str::random(8)),
            'price_override' => $data['price_override'] ?: null,
            'stock_quantity' => (int) ($data['stock_quantity'] ?? 0),
            'is_active'      => true,
        ]);

        $avIds = collect($data['attribute_values'] ?? [])
            ->filter(fn($v) => is_numeric($v))
            ->map(fn($v) => (int) $v)
            ->unique()
            ->toArray();

        if (!empty($avIds)) {
            $variant->attributeValues()->attach($avIds);
        }

        return $variant;
    }
}