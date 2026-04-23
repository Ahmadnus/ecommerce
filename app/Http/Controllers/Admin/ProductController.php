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

class ProductController extends Controller
{
    private const LOW_STOCK_THRESHOLD = 5;

    // ─── Index (unchanged) ────────────────────────────────────────────────────

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
    // CHANGED: accepts `product_images[]` (multiple) instead of `main_image` (single)

  public function store(Request $request)
{
    $request->validate([
        'name'                => 'required|string|max:255',
        'base_price'          => 'required|numeric|min:0',
        'discount_price'      => 'nullable|numeric|min:0|lt:base_price',
        'description'         => 'nullable|string',
        'short_description'   => 'nullable|string|max:500',
        'sku'                 => 'nullable|unique:products,sku',
        'category_ids'        => 'required|array|min:1',
        'category_ids.*'      => 'exists:categories,id',
        'primary_category_id' => 'required|exists:categories,id',

        // ── الصورة الأساسية (Collection: main) ──
        'main_image'          => 'required|image|mimes:jpeg,png,jpg,webp,avif|max:5120',

        // ── صور المعرض (Collection: product) ──
        'product_images'      => 'nullable|array|max:10',
        'product_images.*'    => 'image|mimes:jpeg,png,jpg,webp,avif|max:5120',

        'variants'                     => 'required|array|min:1',
        'variants.*.stock_quantity'    => 'required|integer|min:0',
        'variants.*.price_override'    => 'nullable|numeric|min:0',
        'variants.*.sku'               => 'nullable|string|max:100',
        'variants.*.attribute_values'  => 'nullable|array',
        'variants.*.attribute_values.*'=> 'exists:attribute_values,id',
    ]);

    DB::transaction(function () use ($request) {
        $product = Product::create([
            'name'              => $request->name,
            'slug'              => $this->uniqueSlug($request->name),
            'description'       => $request->description,
            'short_description' => $request->short_description,
            'base_price'        => $request->base_price,
            'discount_price'    => $request->discount_price,
            'sku'               => $request->sku,
            'status'            => $request->boolean('is_active', true) ? 'active' : 'draft',
            'is_featured'       => $request->boolean('is_featured'),
        ]);

        // ربط الأقسام
        $pivot = [];
        foreach ($request->category_ids as $catId) {
            $pivot[(int) $catId] = [
                'is_primary' => (int) $catId === (int) $request->primary_category_id,
            ];
        }
        $product->categories()->attach($pivot);

        // ── 1. رفع الصورة الأساسية (Main Collection) ──
        if ($request->hasFile('main_image')) {
            $product->addMedia($request->file('main_image'))
                    ->toMediaCollection('main');
        }

        // ── 2. رفع صور المنتج الإضافية (Product Collection) ──
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $index => $image) {
                $product->addMedia($image)
                        ->withCustomProperties(['order' => $index])
                        ->toMediaCollection('products'); 
            }
        }

        // إضافة المتغيرات (Variants)
        foreach ($request->variants as $variantData) {
            $this->createVariant($product, $variantData);
        }
    });

    return redirect()->route('admin.products.index')
                     ->with('success', 'تم إضافة المنتج بنجاح مع الصور');
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

        // Pass existing media for preview in the edit form
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
    // CHANGED: handles new uploads + deletion of individual existing images

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'                          => 'required|string|max:255',
            'base_price'                    => 'required|numeric|min:0',
            'discount_price'                => 'nullable|numeric|min:0|lt:base_price',
            'category_ids'                  => 'required|array|min:1',
            'category_ids.*'                => 'exists:categories,id',
            'primary_category_id'           => 'required|exists:categories,id',

            // ── MULTI-IMAGE UPLOAD ─────────────────────────────────────────
            'product_images'                => 'nullable|array|max:10',
            'product_images.*'              => 'image|mimes:jpeg,png,jpg,webp,avif|max:5120',
            // IDs of existing media that should be DELETED
            'delete_media_ids'              => 'nullable|array',
            'delete_media_ids.*'            => 'integer',
            // ──────────────────────────────────────────────────────────────

            'variants'                      => 'required|array|min:1',
            'variants.*.stock_quantity'     => 'required|integer|min:0',
            'variants.*.price_override'     => 'nullable|numeric|min:0',
            'variants.*.sku'                => 'nullable|string|max:100',
            'variants.*.attribute_values'   => 'nullable|array',
            'variants.*.attribute_values.*' => 'exists:attribute_values,id',
        ]);

        DB::transaction(function () use ($request, $product) {
            if ($request->name !== $product->name) {
                $product->slug = $this->uniqueSlug($request->name, $product->id);
            }

            $product->update([
                'name'              => $request->name,
                'description'       => $request->description,
                'short_description' => $request->short_description,
                'base_price'        => $request->base_price,
                'discount_price'    => $request->discount_price,
                'status'            => $request->boolean('is_active', true) ? 'active' : 'draft',
                'is_featured'       => $request->boolean('is_featured'),
            ]);

            // Re-sync categories
            $pivot = [];
            foreach ($request->category_ids as $catId) {
                $pivot[(int) $catId] = [
                    'is_primary' => (int) $catId === (int) $request->primary_category_id,
                ];
            }
            $product->categories()->sync($pivot);

            // ── Delete individually removed images ─────────────────────────
            $deleteIds = $request->input('delete_media_ids', []);
            if (!empty($deleteIds)) {
                $product->media()
                        ->whereIn('id', $deleteIds)
                        ->get()
                        ->each(fn($m) => $m->delete());
            }

            // ── Add newly uploaded images ──────────────────────────────────
            if ($request->hasFile('product_images')) {
                $existingCount = $product->getMedia('products')->count();
                foreach ($request->file('product_images') as $index => $image) {
                    $product->addMedia($image)
                            ->withCustomProperties(['order' => $existingCount + $index])
                            ->toMediaCollection('products');
                }
            }
            // ──────────────────────────────────────────────────────────────

            // Variants: delete all + recreate
        $product->variants()->forceDelete();
            foreach ($request->variants as $variantData) {
                $this->createVariant($product, $variantData);
            }
        });

        return redirect()->route('admin.products.show', $product)
                         ->with('success', 'تم تحديث المنتج بنجاح');
    }

    // ─── Stock update (unchanged) ─────────────────────────────────────────────

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'variants'                   => 'required|array',
            'variants.*.id'              => 'required|exists:product_variants,id',
            'variants.*.stock_quantity'  => 'required|integer|min:0',
            'variants.*.price_override'  => 'nullable|numeric|min:0',
            'variants.*.is_active'       => 'nullable|boolean',
        ]);

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