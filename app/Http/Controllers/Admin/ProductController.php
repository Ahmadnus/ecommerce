<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\AdminProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private readonly AdminProductService $products,
    ) {}

    public function index(Request $request)
    {
        $data = $this->products->getIndexData(
            $request->only(['search', 'status', 'stock', 'category', 'sort'])
        );

        return view('admin.products.index', $data);
    }

    public function create()
    {
        return view('admin.products.create', $this->products->getFormData());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name.ar'               => 'required|string|max:255',
            'name.en'               => 'nullable|string|max:255',
            'description.ar'        => 'nullable|string',
            'description.en'        => 'nullable|string',
            'short_description.ar'  => 'nullable|string|max:500',
            'short_description.en'  => 'nullable|string|max:500',
            'base_price'            => 'required|numeric|min:0',
            'discount_price'        => 'nullable|numeric|min:0|lt:base_price',
            'sku'                   => 'nullable|unique:products,sku',
            'category_ids'          => 'required|array|min:1',
            'category_ids.*'        => 'exists:categories,id',
            'primary_category_id'   => 'required|exists:categories,id',
            'main_image'            => 'required|image|mimes:jpeg,png,jpg,webp,avif|max:5120',
            'product_images'        => 'nullable|array|max:10',
            'product_images.*'      => 'image|mimes:jpeg,png,jpg,webp,avif|max:5120',
            'variants'                      => 'required|array|min:1',
            'variants.*.stock_quantity'      => 'required|integer|min:0',
            'variants.*.price_override'      => 'nullable|numeric|min:0',
            'variants.*.sku'                 => 'nullable|string|max:100',
            'variants.*.attribute_values'    => 'nullable|array',
            'variants.*.attribute_values.*'  => 'exists:attribute_values,id',
        ]);

        try {
            $this->products->create(
                data: [
                    'name'                => $request->input('name'),
                    'description'         => $request->input('description'),
                    'short_description'   => $request->input('short_description'),
                    'base_price'          => $request->base_price,
                    'discount_price'      => $request->discount_price,
                    'sku'                 => $request->sku,
                    'is_active'           => $request->boolean('is_active', true),
                    'is_featured'         => $request->boolean('is_featured'),
                    'category_ids'        => $request->category_ids,
                    'primary_category_id' => $request->primary_category_id,
                    'variants'            => $request->variants,
                ],
                mainImage: $request->hasFile('main_image') ? $request->file('main_image') : null,
                productImages: $request->hasFile('product_images') ? $request->file('product_images') : [],
            );
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة المنتج. يرجى المحاولة مرة أخرى.');
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'تم إضافة المنتج بنجاح');
    }

    public function show(Product $product)
    {
        $product->load(['categories', 'variants.attributeValues.attribute', 'media']);
        $lowThreshold = AdminProductService::LOW_STOCK_THRESHOLD;

        return view('admin.products.show', compact('product', 'lowThreshold'));
    }

    public function edit(Product $product)
    {
        $data = array_merge(
            ['product' => $product],
            $this->products->getFormData(),
            $this->products->getEditData($product),
        );

        return view('admin.products.edit', $data);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name.ar'               => 'required|string|max:255',
            'name.en'               => 'nullable|string|max:255',
            'description.ar'        => 'nullable|string',
            'description.en'        => 'nullable|string',
            'short_description.ar'  => 'nullable|string|max:500',
            'short_description.en'  => 'nullable|string|max:500',
            'base_price'            => 'required|numeric|min:0',
            'discount_price'        => 'nullable|numeric|min:0|lt:base_price',
            'category_ids'          => 'required|array|min:1',
            'category_ids.*'        => 'exists:categories,id',
            'primary_category_id'   => 'required|exists:categories,id',
            'product_images'        => 'nullable|array|max:10',
            'product_images.*'      => 'image|mimes:jpeg,png,jpg,webp,avif|max:5120',
            'delete_media_ids'      => 'nullable|array',
            'delete_media_ids.*'    => 'integer',
            'variants'                      => 'required|array|min:1',
            'variants.*.stock_quantity'      => 'required|integer|min:0',
            'variants.*.price_override'      => 'nullable|numeric|min:0',
            'variants.*.sku'                 => 'nullable|string|max:100',
            'variants.*.attribute_values'    => 'nullable|array',
            'variants.*.attribute_values.*'  => 'exists:attribute_values,id',
        ]);

        try {
            $this->products->update(
                product: $product,
                data: [
                    'name'                => $request->input('name'),
                    'description'         => $request->input('description'),
                    'short_description'   => $request->input('short_description'),
                    'base_price'          => $request->base_price,
                    'discount_price'      => $request->discount_price,
                    'is_active'           => $request->boolean('is_active', true),
                    'is_featured'         => $request->boolean('is_featured'),
                    'category_ids'        => $request->category_ids,
                    'primary_category_id' => $request->primary_category_id,
                    'delete_media_ids'    => $request->input('delete_media_ids', []),
                    'variants'            => $request->variants,
                ],
                productImages: $request->hasFile('product_images') ? $request->file('product_images') : [],
            );
        } catch (\Throwable $e) {
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث المنتج. يرجى المحاولة مرة أخرى.');
        }

        return redirect()->route('admin.products.show', $product)
            ->with('success', 'تم تحديث المنتج بنجاح');
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'variants'                   => 'required|array',
            'variants.*.id'              => 'required|exists:product_variants,id',
            'variants.*.stock_quantity'   => 'required|integer|min:0',
            'variants.*.price_override'   => 'nullable|numeric|min:0',
            'variants.*.is_active'        => 'nullable|boolean',
        ]);

        $this->products->updateStock($product, $request->variants);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'تم تحديث المخزون']);
        }

        return back()->with('success', 'تم تحديث المخزون بنجاح');
    }

    public function destroy(Product $product)
    {
        $this->products->delete($product);

        return redirect()->route('admin.products.index')
            ->with('success', 'تم نقل المنتج إلى المحذوفات');
    }
}
