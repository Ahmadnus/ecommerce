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
    // ─── Constants ────────────────────────────────────────────────────────────

    /** Qty below which a variant is "low stock" */
    private const LOW_STOCK_THRESHOLD = 5;

    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Product::with([
            'categories',
            'variants' => fn($q) => $q->orderBy('is_active', 'desc'),
            'variants.attributeValues.attribute',
            'media',
        ])
        ->withCount('variants');

        // ── Search ──────────────────────────────────────────────────────────
        if ($request->filled('search')) {
            $query->where(fn($q) => $q
                ->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('sku', 'like', '%' . $request->search . '%')
            );
        }

        // ── Status filter ────────────────────────────────────────────────────
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // ── Stock filter ─────────────────────────────────────────────────────
        if ($request->filled('stock')) {
            match ($request->stock) {
                'out'  => $query->whereHas('variants', fn($q) => $q->where('stock_quantity', 0)),
                'low'  => $query->whereHas('variants', fn($q) => $q->where('stock_quantity', '>', 0)
                                  ->where('stock_quantity', '<=', self::LOW_STOCK_THRESHOLD)),
                default => null,
            };
        }

        // ── Category filter ──────────────────────────────────────────────────
        if ($request->filled('category')) {
            $query->whereHas('categories', fn($q) => $q->where('categories.id', $request->category));
        }

        // ── Sort ─────────────────────────────────────────────────────────────
        match ($request->get('sort', 'newest')) {
            'name_asc'    => $query->orderBy('name'),
            'name_desc'   => $query->orderByDesc('name'),
            'price_asc'   => $query->orderBy('base_price'),
            'price_desc'  => $query->orderByDesc('base_price'),
            'stock_asc'   => $query->withSum('variants', 'stock_quantity')
                                   ->orderBy('variants_sum_stock_quantity'),
            default       => $query->latest(),
        };

        $products   = $query->paginate(20)->withQueryString();
        $categories = Category::active()->orderBy('name')->get();

        // ── Dashboard stats ──────────────────────────────────────────────────
        $stats = [
            'total'   => Product::count(),
            'active'  => Product::where('status', 'active')->count(),
            'out'     => ProductVariant::where('stock_quantity', 0)->count(),
            'low'     => ProductVariant::where('stock_quantity', '>', 0)
                                       ->where('stock_quantity', '<=', self::LOW_STOCK_THRESHOLD)
                                       ->count(),
        ];

        return view('admin.products.index', compact('products', 'categories', 'stats'));
    }

    // ─── Create ───────────────────────────────────────────────────────────────

    public function create()
    {
        $categories = Category::active()
            ->roots()
            ->with('allActiveChildren')
            ->orderBy('sort_order')
            ->get();

        $attributes = Attribute::with('values')
            ->orderBy('sort_order')
            ->get();

        return view('admin.products.create', compact('categories', 'attributes'));
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $request->validate([
            'name'                          => 'required|string|max:255',
            'base_price'                    => 'required|numeric|min:0',
            'discount_price'                => 'nullable|numeric|min:0|lt:base_price',
            'description'                   => 'nullable|string',
            'short_description'             => 'nullable|string|max:500',
            'sku'                           => 'nullable|unique:products,sku',
            'category_ids'                  => 'required|array|min:1',
            'category_ids.*'                => 'exists:categories,id',
            'primary_category_id'           => 'required|exists:categories,id',
            'main_image'                    => 'nullable|image|max:4096',
            'variants'                      => 'required|array|min:1',
            'variants.*.stock_quantity'     => 'required|integer|min:0',
            'variants.*.price_override'     => 'nullable|numeric|min:0',
            'variants.*.sku'                => 'nullable|string|max:100',
            'variants.*.attribute_values'   => 'nullable|array',
            'variants.*.attribute_values.*' => 'exists:attribute_values,id',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Product
            $slug = $this->uniqueSlug($request->name);

            $product = Product::create([
                'name'              => $request->name,
                'slug'              => $slug,
                'description'       => $request->description,
                'short_description' => $request->short_description,
                'base_price'        => $request->base_price,
                'discount_price'    => $request->discount_price,
                'sku'               => $request->sku,
                'status'            => $request->boolean('is_active', true) ? 'active' : 'draft',
                'is_featured'       => $request->boolean('is_featured'),
            ]);

            // 2. Categories (pivot: is_primary)
            $pivot = [];
            foreach ($request->category_ids as $catId) {
                $pivot[(int) $catId] = [
                    'is_primary' => (int) $catId === (int) $request->primary_category_id,
                ];
            }
            $product->categories()->attach($pivot);

            // 3. Main image via Spatie
            if ($request->hasFile('main_image')) {
                $product->addMediaFromRequest('main_image')
                        ->toMediaCollection('products');
            }

            // 4. Variants + attribute_values pivot
            foreach ($request->variants as $variantData) {
                $this->createVariant($product, $variantData);
            }
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'تم إضافة المنتج بنجاح');
    }

    // ─── Show (product detail with inventory breakdown) ───────────────────────

    public function show(Product $product)
    {
        $product->load([
            'categories',
            'variants.attributeValues.attribute',
            'media',
        ]);

        $lowThreshold = self::LOW_STOCK_THRESHOLD;

        return view('admin.products.show', compact('product', 'lowThreshold'));
    }

    // ─── Edit ─────────────────────────────────────────────────────────────────

    public function edit(Product $product)
    {
        $product->load([
            'categories',
            'variants.attributeValues.attribute',
        ]);

        $categories = Category::active()
            ->roots()
            ->with('allActiveChildren')
            ->orderBy('sort_order')
            ->get();

        $attributes = Attribute::with('values')
            ->orderBy('sort_order')
            ->get();

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

        return view('admin.products.edit', compact(
            'product', 'categories', 'attributes',
            'selectedCatIds', 'primaryCatId', 'existingVariants'
        ));
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'                          => 'required|string|max:255',
            'base_price'                    => 'required|numeric|min:0',
            'discount_price'                => 'nullable|numeric|min:0|lt:base_price',
            'category_ids'                  => 'required|array|min:1',
            'category_ids.*'                => 'exists:categories,id',
            'primary_category_id'           => 'required|exists:categories,id',
            'main_image'                    => 'nullable|image|max:4096',
            'variants'                      => 'required|array|min:1',
            'variants.*.stock_quantity'     => 'required|integer|min:0',
            'variants.*.price_override'     => 'nullable|numeric|min:0',
            'variants.*.sku'                => 'nullable|string|max:100',
            'variants.*.attribute_values'   => 'nullable|array',
            'variants.*.attribute_values.*' => 'exists:attribute_values,id',
        ]);

        DB::transaction(function () use ($request, $product) {
            // 1. Base product
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

            // 2. Re-sync categories
            $pivot = [];
            foreach ($request->category_ids as $catId) {
                $pivot[(int) $catId] = [
                    'is_primary' => (int) $catId === (int) $request->primary_category_id,
                ];
            }
            $product->categories()->sync($pivot);

            // 3. Image
            if ($request->hasFile('main_image')) {
                $product->clearMediaCollection('products');
                $product->addMediaFromRequest('main_image')
                        ->toMediaCollection('products');
            }

            // 4. Variants: delete all + recreate
            //    (simple and safe — avoids stale pivot rows)
            $product->variants()->delete();

            foreach ($request->variants as $variantData) {
                $this->createVariant($product, $variantData);
            }
        });

        return redirect()
            ->route('admin.products.show', $product)
            ->with('success', 'تم تحديث المنتج بنجاح');
    }

    // ─── Bulk stock update (AJAX / form from show page) ──────────────────────

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
                ->where('product_id', $product->id)   // ownership check
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

    // ─── Destroy (soft delete) ────────────────────────────────────────────────

    public function destroy(Product $product)
    {
        // Soft-delete cascades because variants check the product's deleted_at
        // via the SoftDeletes trait; add a global scope if needed.
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'تم نقل المنتج إلى المحذوفات');
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    /**
     * Generate a guaranteed-unique slug for the products table.
     */
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

    /**
     * Create a single variant and attach its attribute values.
     */
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
            // Uses the product_variant_attribute_values pivot table
            $variant->attributeValues()->attach($avIds);
        }

        return $variant;
    }
}