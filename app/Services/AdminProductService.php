<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * AdminProductService
 *
 * All business logic for the admin product CRUD (listing/filtering, create,
 * update, stock updates, delete). The distinct storefront ProductService
 * (repository-based) is unrelated — this one backs Admin\ProductController.
 *
 * Never returns views/redirects; returns data/models and throws on failure.
 */
class AdminProductService
{
    public const LOW_STOCK_THRESHOLD = 1;

    /**
     * Filtered, sorted, paginated product list + categories + stats
     * for the admin index page.
     *
     * $filters keys: search, status, stock, category, sort.
     */
    public function getIndexData(array $filters): array
    {
        $query = Product::with([
            'categories',
            'variants' => fn($q) => $q->orderBy('is_active', 'desc'),
            'variants.attributeValues.attribute',
            'media',
        ])->withCount('variants');

        if (!empty($filters['search'])) {
            $query->where(fn($q) => $q
                ->where('name', 'like', '%' . $filters['search'] . '%')
                ->orWhere('sku', 'like', '%' . $filters['search'] . '%'));
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['stock'])) {
            match ($filters['stock']) {
                'out' => $query->whereHas('variants', fn($q) => $q->where('stock_quantity', 0)),
                'low' => $query->whereHas('variants', fn($q) => $q
                    ->where('stock_quantity', '>', 0)
                    ->where('stock_quantity', '<=', self::LOW_STOCK_THRESHOLD)),
                default => null,
            };
        }

        if (!empty($filters['category'])) {
            $query->whereHas('categories', fn($q) => $q->where('categories.id', $filters['category']));
        }

        match ($filters['sort'] ?? 'newest') {
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
                ->where('stock_quantity', '<=', self::LOW_STOCK_THRESHOLD)
                ->count(),
        ];

        return compact('products', 'categories', 'stats');
    }

    /**
     * Categories + attributes needed by the create/edit forms.
     */
    public function getFormData(): array
    {
        $categories = Category::active()->roots()
            ->with('allActiveChildren')
            ->orderBy('sort_order')
            ->get();

        $attributes = Attribute::with('values')
            ->orderBy('sort_order')
            ->get();

        return compact('categories', 'attributes');
    }

    /**
     * Create a product with categories, media and variants.
     *
     * $data keys: name, description, short_description, base_price,
     *   discount_price, sku, is_active (bool), is_featured (bool),
     *   category_ids, primary_category_id, variants.
     * $mainImage: UploadedFile|null; $productImages: UploadedFile[].
     *
     * @throws \Throwable on failure (transaction rolled back).
     */
    public function create(array $data, $mainImage, array $productImages): Product
    {
        try {
            return DB::transaction(function () use ($data, $mainImage, $productImages) {
                $product = Product::create([
                    'name'              => $data['name'],
                    'description'       => $data['description'] ?? null,
                    'short_description' => $data['short_description'] ?? null,
                    'slug'              => $this->uniqueSlug(
                        ($data['name']['ar'] ?? null) ?: ($data['name']['en'] ?? '')
                    ),
                    'base_price'        => $data['base_price'],
                    'discount_price'    => $data['discount_price'] ?? null,
                    'sku'               => $data['sku'] ?? null,
                    'status'            => $data['is_active'] ? 'active' : 'draft',
                    'is_featured'       => $data['is_featured'],
                ]);

                $pivot = [];
                foreach ($data['category_ids'] as $catId) {
                    $pivot[(int) $catId] = [
                        'is_primary' => (int) $catId === (int) $data['primary_category_id'],
                    ];
                }
                $product->categories()->attach($pivot);

                if ($mainImage) {
                    $product->addCompressedMedia($mainImage, 'main');
                }

                foreach ($productImages as $index => $image) {
                    $product->addCompressedMedia($image, 'products');
                }

                foreach ($data['variants'] as $variantData) {
                    $this->createVariant($product, $variantData);
                }

                return $product;
            });
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }

    /**
     * Data the edit form needs beyond the form data itself.
     */
    public function getEditData(Product $product): array
    {
        $product->load(['categories', 'variants.attributeValues.attribute']);

        $selectedCatIds = $product->categories->pluck('id')->toArray();
        $primaryCatId   = $product->categories
            ->first(fn($c) => $c->pivot->is_primary)?->id
            ?? $product->categories->first()?->id;

        $existingVariants = $product->variants->map(fn($v) => [
            'id'              => $v->id,
            'sku'             => $v->sku,
            'stock_quantity'  => $v->stock_quantity,
            'price_override'  => $v->price_override,
            'is_active'       => $v->is_active,
            'attribute_values' => $v->attributeValues->pluck('id')->toArray(),
        ]);

        $existingImages = $product->getMedia('products')->map(fn($m) => [
            'id'  => $m->id,
            'url' => $m->getUrl(),
        ]);

        return compact('selectedCatIds', 'primaryCatId', 'existingVariants', 'existingImages');
    }

    /**
     * Update a product: fields, slug (if Arabic name changed), categories,
     * media deletions/additions, and full variant rebuild.
     *
     * $data keys as in create() plus delete_media_ids.
     *
     * @throws \Throwable on failure (transaction rolled back).
     */
    public function update(Product $product, array $data, array $productImages): Product
    {
        try {
            return DB::transaction(function () use ($product, $data, $productImages) {
                $newArName = $data['name']['ar'] ?? null;
                $newEnName = $data['name']['en'] ?? null;

                if ($newArName !== $product->getTranslation('name', 'ar')) {
                    $product->slug = $this->uniqueSlug($newArName ?: $newEnName, $product->id);
                }

                $product->update([
                    'name'              => $data['name'],
                    'description'       => $data['description'] ?? null,
                    'short_description' => $data['short_description'] ?? null,
                    'base_price'        => $data['base_price'],
                    'discount_price'    => $data['discount_price'] ?? null,
                    'status'            => $data['is_active'] ? 'active' : 'draft',
                    'is_featured'       => $data['is_featured'],
                ]);

                $pivot = [];
                foreach ($data['category_ids'] as $catId) {
                    $pivot[(int) $catId] = [
                        'is_primary' => (int) $catId === (int) $data['primary_category_id'],
                    ];
                }
                $product->categories()->sync($pivot);

                $deleteIds = $data['delete_media_ids'] ?? [];
                if (!empty($deleteIds)) {
                    $product->media()->whereIn('id', $deleteIds)->get()->each(fn($m) => $m->delete());
                }

                foreach ($productImages as $index => $image) {
                    $product->addCompressedMedia($image, 'products');
                }

                $product->variants()->forceDelete();

                foreach ($data['variants'] as $variantData) {
                    $this->createVariant($product, $variantData);
                }

                return $product;
            });
        } catch (\Throwable $e) {
            report($e);
            throw $e;
        }
    }

    /**
     * Bulk stock/price/active update for a product's variants.
     */
    public function updateStock(Product $product, array $variants): void
    {
        foreach ($variants as $data) {
            ProductVariant::where('id', $data['id'])
                ->where('product_id', $product->id)
                ->update([
                    'stock_quantity' => $data['stock_quantity'],
                    'price_override'  => $data['price_override'] ?: null,
                    'is_active'      => (bool) ($data['is_active'] ?? true),
                ]);
        }
    }

    /**
     * Soft-delete a product.
     */
    public function delete(Product $product): void
    {
        $product->delete();
    }

    private function uniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $base = $slug;
        $i = 1;

        while (
            Product::withTrashed()
                ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
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
