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
    public function index()
    {
        $products = Product::with(['categories', 'variants'])
            ->latest()
            ->paginate(10);

        return view('admin.products.index', compact('products'));
    }

   public function create()
{
    $categories = Category::whereNull('parent_id')->with('allActiveChildren')->get();
    $attributes = Attribute::with('values')->get(); // تأكد من جلب السمات وقيمها
    
    return view('admin.products.create', compact('categories', 'attributes'));
}

    public function store(Request $request)
    {
        $request->validate([
            'name'                          => 'required|max:255',
            'base_price'                    => 'required|numeric|min:0',
            'discount_price'                => 'nullable|numeric|min:0|lt:base_price',
            'description'                   => 'nullable|string',
            'short_description'             => 'nullable|string|max:500',
            'sku'                           => 'nullable|unique:products,sku',
            'category_ids'                  => 'required|array|min:1',
            'category_ids.*'                => 'exists:categories,id',
            'primary_category_id'           => 'required|exists:categories,id',
            'main_image'                    => 'required|image|max:4096',
            'is_featured'                   => 'nullable|boolean',
            // Variants
            'variants'                      => 'nullable|array',
            'variants.*.sku'                => 'nullable|string|max:100',
            'variants.*.price_override'     => 'nullable|numeric|min:0',
            'variants.*.stock_quantity'     => 'required_with:variants|integer|min:0',
            'variants.*.attribute_values'   => 'nullable|array',
            'variants.*.attribute_values.*' => 'exists:attribute_values,id',
        ]);

        DB::transaction(function () use ($request) {
            // ── 1. Create product ──────────────────────────────────────────
            $slug = Str::slug($request->name);
            if (Product::where('slug', $slug)->exists()) {
                $slug .= '-' . Str::lower(Str::random(4));
            }

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

            // ── 2. Attach categories ───────────────────────────────────────
            $pivot = [];
            foreach ($request->category_ids as $catId) {
                $pivot[(int) $catId] = ['is_primary' => (int) $catId === (int) $request->primary_category_id];
            }
            $product->categories()->attach($pivot);

            // ── 3. Image via Spatie ────────────────────────────────────────
            if ($request->hasFile('main_image')) {
                $product->addMediaFromRequest('main_image')->toMediaCollection('products');
            }

            // ── 4. Variants ────────────────────────────────────────────────
            if ($request->filled('variants')) {
                foreach ($request->variants as $i => $variantData) {
                    $sku = !empty($variantData['sku'])
                        ? $variantData['sku']
                        : strtoupper(Str::random(8));

                    $variant = $product->variants()->create([
                        'sku'            => $sku,
                        'price_override' => $variantData['price_override'] ?? null,
                        'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                        'is_active'      => true,
                    ]);

                    // Attach attribute values
                    $avIds = collect($variantData['attribute_values'] ?? [])
                        ->filter(fn($v) => is_numeric($v))
                        ->toArray();

                    if (!empty($avIds)) {
                        $variant->attributeValues()->attach($avIds);
                    }

                    // Variant image
                    if (!empty($variantData['variant_image']) && $request->hasFile("variants.{$i}.variant_image")) {
                        $variant->addMediaFromRequest("variants.{$i}.variant_image")
                                ->toMediaCollection('variant_images');
                    }
                }
            } else {
                // Default single variant (no attributes selected)
                $product->variants()->create([
                    'sku'            => strtoupper(Str::random(8)),
                    'price_override' => $request->base_price,
                    'stock_quantity' => $request->input('stock_quantity', 0),
                    'is_active'      => true,
                ]);
            }
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'تم إضافة المنتج والمتغيرات بنجاح');
    }

    public function edit(Product $product)
    {
        $product->load(['categories', 'variants.attributeValues.attribute']);

        $categories = Category::active()
            ->roots()
            ->with('allActiveChildren')
            ->orderBy('sort_order')
            ->get();

        $attributes = Attribute::with('values')->orderBy('sort_order')->get();

        return view('admin.products.edit', compact('product', 'categories', 'attributes'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'             => 'required|max:255',
            'base_price'       => 'required|numeric|min:0',
            'discount_price'   => 'nullable|numeric|min:0|lt:base_price',
            'category_ids'     => 'required|array|min:1',
            'category_ids.*'   => 'exists:categories,id',
            'primary_category_id' => 'required|exists:categories,id',
            'main_image'       => 'nullable|image|max:4096',
            // Variants
            'variants'                      => 'nullable|array',
            'variants.*.sku'                => 'nullable|string',
            'variants.*.price_override'     => 'nullable|numeric|min:0',
            'variants.*.stock_quantity'     => 'required_with:variants|integer|min:0',
            'variants.*.attribute_values'   => 'nullable|array',
            'variants.*.attribute_values.*' => 'exists:attribute_values,id',
        ]);

        DB::transaction(function () use ($request, $product) {
            // ── Update product fields ──────────────────────────────────────
            if ($request->name !== $product->name) {
                $slug = Str::slug($request->name);
                if (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                    $slug .= '-' . Str::lower(Str::random(4));
                }
                $product->slug = $slug;
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

            // ── Re-sync categories ─────────────────────────────────────────
            $pivot = [];
            foreach ($request->category_ids as $catId) {
                $pivot[(int) $catId] = ['is_primary' => (int) $catId === (int) $request->primary_category_id];
            }
            $product->categories()->sync($pivot);

            // ── Image ──────────────────────────────────────────────────────
            if ($request->hasFile('main_image')) {
                $product->clearMediaCollection('products');
                $product->addMediaFromRequest('main_image')->toMediaCollection('products');
            }

            // ── Variants: delete all & recreate (simplest admin flow) ──────
            if ($request->filled('variants')) {
                $product->variants()->delete();

                foreach ($request->variants as $i => $variantData) {
                    $sku = !empty($variantData['sku'])
                        ? $variantData['sku']
                        : strtoupper(Str::random(8));

                    $variant = $product->variants()->create([
                        'sku'            => $sku,
                        'price_override' => $variantData['price_override'] ?? null,
                        'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                        'is_active'      => true,
                    ]);

                    $avIds = collect($variantData['attribute_values'] ?? [])
                        ->filter(fn($v) => is_numeric($v))
                        ->toArray();

                    if (!empty($avIds)) {
                        $variant->attributeValues()->attach($avIds);
                    }
                }
            }
        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'تم تحديث المنتج بنجاح');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()
            ->route('admin.products.index')
            ->with('success', 'تم نقل المنتج إلى سلة المهملات');
    }
}