<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\HomeSection;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * DemoStorefrontSeeder — bilingual demo catalogue for the storefront.
 *
 * Fills the shop with enough realistic data to exercise every branch of the
 * new storefront UI:
 *
 *   • products on sale (discount badge + struck-through old price)
 *   • single-variant products (quick "add to cart" straight from the card)
 *   • multi-variant products (card links to the detail page to pick options)
 *   • a sold-out product (disabled action + "out of stock" badge)
 *   • featured products, so the homepage product rails have something to show
 *   • size / colour attributes, so the detail page variant selector renders
 *
 * Safe to re-run: it clears only the rows it owns, matched by SKU prefix.
 *
 *     php artisan db:seed --class=DemoStorefrontSeeder
 */
class DemoStorefrontSeeder extends Seeder
{
    /** Marks every row this seeder creates so re-runs stay idempotent. */
    private const SKU_PREFIX = 'DEMO-';

    public function run(): void
    {
        $this->reset();

        $categories = $this->categories();
        $attributes = $this->attributes();

        $this->products($categories, $attributes);
        $this->homeSections($categories);

        $this->command?->info('Demo storefront seeded: '
            . Product::active()->count() . ' active products, '
            . ProductVariant::count() . ' variants, '
            . Category::count() . ' categories.');
    }

    // ── Reset ──────────────────────────────────────────────────────────────

    private function reset(): void
    {
        $productIds = Product::withTrashed()
            ->where('sku', 'like', self::SKU_PREFIX . '%')
            ->pluck('id');

        if ($productIds->isNotEmpty()) {
            ProductVariant::whereIn('product_id', $productIds)->delete();
            Product::withTrashed()->whereIn('id', $productIds)->forceDelete();
        }

        HomeSection::where('sort_order', '>=', 900)->delete();
    }

    // ── Categories ─────────────────────────────────────────────────────────

    /** @return array<string, Category> keyed by slug */
    private function categories(): array
    {
        $definitions = [
            'electronics' => ['en' => 'Electronics',  'ar' => 'إلكترونيات'],
            'home-living' => ['en' => 'Home & Living','ar' => 'المنزل والمعيشة'],
            'fashion'     => ['en' => 'Fashion',      'ar' => 'أزياء'],
            'beauty'      => ['en' => 'Beauty',       'ar' => 'العناية والجمال'],
            'sports'      => ['en' => 'Sports',       'ar' => 'رياضة'],
            'toys'        => ['en' => 'Toys & Kids',  'ar' => 'ألعاب وأطفال'],
        ];

        $result = [];
        $order  = 0;

        foreach ($definitions as $slug => $name) {
            $result[$slug] = Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'       => $name,
                    'is_active'  => true,
                    'sort_order' => $order++,
                ],
            );
        }

        return $result;
    }

    // ── Attributes ─────────────────────────────────────────────────────────

    /** @return array{size: AttributeValue[], color: AttributeValue[]} */
    private function attributes(): array
    {
        $size = Attribute::firstOrCreate(
            ['slug' => 'size'],
            ['name' => ['en' => 'Size', 'ar' => 'المقاس'], 'type' => 'select', 'sort_order' => 1],
        );

        $color = Attribute::firstOrCreate(
            ['slug' => 'color'],
            ['name' => ['en' => 'Colour', 'ar' => 'اللون'], 'type' => 'color', 'sort_order' => 2],
        );

        $sizes = [];
        foreach (['S', 'M', 'L', 'XL'] as $i => $value) {
            $sizes[] = AttributeValue::firstOrCreate(
                ['attribute_id' => $size->id, 'value' => $value],
                ['label' => ['en' => $value, 'ar' => $value], 'sort_order' => $i],
            );
        }

        $colors = [];
        $palette = [
            'black' => ['#111827', 'Black', 'أسود'],
            'white' => ['#f9fafb', 'White', 'أبيض'],
            'blue'  => ['#2563eb', 'Blue',  'أزرق'],
        ];

        $i = 0;
        foreach ($palette as $value => [$hex, $en, $ar]) {
            $colors[] = AttributeValue::firstOrCreate(
                ['attribute_id' => $color->id, 'value' => $value],
                [
                    'label'      => ['en' => $en, 'ar' => $ar],
                    'color_hex'  => $hex,
                    'sort_order' => $i++,
                ],
            );
        }

        return ['size' => $sizes, 'color' => $colors];
    }

    // ── Products ───────────────────────────────────────────────────────────

    /**
     * @param array<string, Category> $categories
     * @param array{size: AttributeValue[], color: AttributeValue[]} $attributes
     */
    private function products(array $categories, array $attributes): void
    {
        // name (en/ar), category, base, discount, stock, variant mode, featured
        //   variant mode: 'single'  → one plain variant, quick-add from the card
        //                 'size'    → one variant per size, card links to detail
        //                 'color'   → one variant per colour
        $rows = [
            ['Wireless Headphones',  'سماعات لاسلكية',        'electronics', 89.00,  64.00, 24, 'color',  true],
            ['Smart Watch Series 6', 'ساعة ذكية الإصدار 6',   'electronics', 149.00, null,  12, 'color',  true],
            ['Bluetooth Speaker',    'مكبر صوت بلوتوث',       'electronics', 45.50,  32.00, 30, 'single', false],
            ['USB-C Fast Charger',   'شاحن سريع USB-C',       'electronics', 15.00,  null,  80, 'single', false],

            ['Ceramic Coffee Mug',   'كوب قهوة سيراميك',      'home-living', 12.00,  9.00,  50, 'color',  false],
            ['Scented Candle Set',   'طقم شموع معطرة',        'home-living', 28.00,  null,  18, 'single', true],
            ['Cotton Throw Blanket', 'بطانية قطنية',          'home-living', 55.00,  39.00,  0, 'single', false],

            ['Classic Cotton Tee',   'تيشيرت قطني كلاسيكي',   'fashion',     22.00,  null,  60, 'size',   false],
            ['Denim Jacket',         'جاكيت جينز',            'fashion',     95.00,  72.00, 14, 'size',   true],
            ['Leather Belt',         'حزام جلد',              'fashion',     34.00,  null,  25, 'single', false],

            ['Vitamin C Serum',      'سيروم فيتامين سي',      'beauty',      38.00,  27.50, 40, 'single', false],
            ['Argan Hair Oil',       'زيت الأرغان للشعر',     'beauty',      24.00,  null,  35, 'single', false],

            ['Yoga Mat Pro',         'سجادة يوغا برو',        'sports',      42.00,  33.00, 22, 'color',  false],
            ['Adjustable Dumbbell',  'دمبل قابل للتعديل',     'sports',      120.00, null,   6, 'single', false],

            ['Wooden Puzzle Set',    'طقم أحجية خشبية',       'toys',        19.50,  14.00, 45, 'single', false],
            ['Plush Teddy Bear',     'دبدوب قطيفة',           'toys',        26.00,  null,   0, 'single', false],
        ];

        foreach ($rows as $i => [$en, $ar, $categorySlug, $base, $discount, $stock, $mode, $featured]) {
            $product = Product::create([
                'name'              => ['en' => $en, 'ar' => $ar],
                'slug'              => Str::slug($en),
                'short_description' => [
                    'en' => "Demo listing for {$en}.",
                    'ar' => "منتج تجريبي: {$ar}.",
                ],
                'description' => [
                    'en' => "This is demo catalogue data for {$en}, created by DemoStorefrontSeeder "
                          . 'so the storefront can be reviewed with realistic content.',
                    'ar' => "بيانات تجريبية للمنتج {$ar}، تم إنشاؤها بواسطة DemoStorefrontSeeder "
                          . 'لعرض واجهة المتجر بمحتوى واقعي.',
                ],
                'base_price'     => $base,
                'discount_price' => $discount,
                'sku'            => self::SKU_PREFIX . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                'status'         => 'active',
                'is_featured'    => $featured,
                'sort_order'     => $i,
            ]);

            if (isset($categories[$categorySlug])) {
                $product->categories()->sync([$categories[$categorySlug]->id]);
            }

            $this->variants($product, $mode, $stock, $attributes);
        }
    }

    /**
     * Stock lives entirely on variants in this application — Product::$total_stock
     * sums them and CartService refuses anything at zero — so every product needs
     * at least one variant to be purchasable.
     *
     * @param array{size: AttributeValue[], color: AttributeValue[]} $attributes
     */
    private function variants(Product $product, string $mode, int $stock, array $attributes): void
    {
        $values = match ($mode) {
            'size'  => $attributes['size'],
            'color' => $attributes['color'],
            default => [null],
        };

        foreach ($values as $index => $value) {
            $variant = ProductVariant::create([
                'product_id'     => $product->id,
                'sku'            => $product->sku . '-V' . ($index + 1),
                'price_override' => null,
                // Spread the declared stock across the variants so a
                // "sold out" product stays sold out on every option.
                'stock_quantity' => $stock === 0
                    ? 0
                    : max(1, (int) floor($stock / max(1, count($values)))),
                'is_active'      => true,
            ]);

            if ($value instanceof AttributeValue) {
                $variant->attributeValues()->sync([$value->id]);
            }
        }
    }

    // ── Homepage sections ──────────────────────────────────────────────────

    /** @param array<string, Category> $categories */
    private function homeSections(array $categories): void
    {
        HomeSection::create([
            'title'      => ['en' => 'Featured products', 'ar' => 'منتجات مميزة'],
            'type'       => HomeSection::TYPE_FEATURED,
            'limit'      => 8,
            'sort_order' => 900,
            'is_active'  => true,
        ]);

        HomeSection::create([
            'title'      => ['en' => 'New arrivals', 'ar' => 'وصل حديثاً'],
            'type'       => HomeSection::TYPE_LATEST,
            'limit'      => 8,
            'sort_order' => 901,
            'is_active'  => true,
        ]);

        if (isset($categories['electronics'])) {
            HomeSection::create([
                'title'       => ['en' => 'Electronics', 'ar' => 'إلكترونيات'],
                'type'        => HomeSection::TYPE_CATEGORY,
                'category_id' => $categories['electronics']->id,
                'limit'       => 8,
                'sort_order'  => 902,
                'is_active'   => true,
            ]);
        }
    }
}
