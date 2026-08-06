<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * DemoStoreSeeder
 *
 * Demo catalogue for the LEGACY e-commerce dashboard on the presentation
 * build. It exists so /admin, /admin/products, /admin/categories and
 * /admin/orders show realistic numbers instead of empty tables during a demo.
 *
 * Deliberately self-contained: no remote image downloads (unlike TestSeeder),
 * so it runs offline and never fails a demo on a flaky connection. Products
 * carry no media — the admin list falls back to its own placeholder.
 *
 * Idempotent: every row is matched on a natural key (slug / sku / order
 * number), so re-running tops the data up instead of duplicating it.
 */
class DemoStoreSeeder extends Seeder
{
    public function run(): void
    {
        $categories = $this->seedCategories();
        $sizes      = $this->seedAttributes();

        $products = $this->seedProducts($categories, $sizes);

        $this->seedOrders($products);
    }

    /**
     * @return array<string, Category>
     */
    private function seedCategories(): array
    {
        $rows = [
            'womens-ready-to-wear' => ['en' => "Women's Ready to Wear", 'ar' => 'ملابس نسائية جاهزة'],
            'womens-shoes'         => ['en' => "Women's Shoes",         'ar' => 'أحذية نسائية'],
            'hats'                 => ['en' => 'Hats',                  'ar' => 'قبعات'],
            'beach'                => ['en' => 'Beach',                 'ar' => 'شاطئ'],
            'travel'               => ['en' => 'Travel',                'ar' => 'سفر'],
            'accessories'          => ['en' => 'Accessories',           'ar' => 'اكسسوارات'],
        ];

        $categories = [];
        $order      = 0;

        foreach ($rows as $slug => $name) {
            $categories[$slug] = Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'       => $name,
                    'is_active'  => true,
                    'sort_order' => $order++,
                ]
            );
        }

        return $categories;
    }

    /**
     * Seeds a Size and a Colour attribute. Only the size values are returned —
     * they are what the product variants below are built from.
     *
     * @return array<int, AttributeValue>
     */
    private function seedAttributes(): array
    {
        $size = Attribute::updateOrCreate(
            ['slug' => 'size'],
            ['name' => ['en' => 'Size', 'ar' => 'المقاس'], 'type' => 'select', 'sort_order' => 0]
        );

        $colour = Attribute::updateOrCreate(
            ['slug' => 'colour'],
            ['name' => ['en' => 'Colour', 'ar' => 'اللون'], 'type' => 'color', 'sort_order' => 1]
        );

        // Matched on (attribute_id, sort_order) rather than the translatable
        // `value` column — that one is JSON, so comparing it in a WHERE clause
        // is brittle across database drivers.
        $sizeValues = [];

        $sizes = [['S', 'صغير'], ['M', 'وسط'], ['L', 'كبير']];
        foreach ($sizes as $i => [$en, $ar]) {
            $sizeValues[] = AttributeValue::updateOrCreate(
                ['attribute_id' => $size->id, 'sort_order' => $i],
                ['value' => ['en' => $en, 'ar' => $en], 'label' => ['en' => $en, 'ar' => $ar]]
            );
        }

        $colours = [['Black', 'أسود', '#111111'], ['Beige', 'بيج', '#d8c3a5'], ['Olive', 'زيتي', '#5a6650']];
        foreach ($colours as $i => [$en, $ar, $hex]) {
            AttributeValue::updateOrCreate(
                ['attribute_id' => $colour->id, 'sort_order' => $i],
                ['value' => ['en' => $en, 'ar' => $en], 'label' => ['en' => $en, 'ar' => $ar], 'color_hex' => $hex]
            );
        }

        return $sizeValues;
    }

    /**
     * @param  array<string, Category>  $categories
     * @param  array<int, AttributeValue>  $sizes
     * @return array<int, Product>
     */
    private function seedProducts(array $categories, array $sizes): array
    {
        // [slug, en, ar, category, base, discount|null, featured, status, stock per size]
        $rows = [
            ['linen-midi-dress',   'Linen Midi Dress',    'فستان كتان ميدي',    'womens-ready-to-wear', 249.00, 199.00, true,  'active', [6, 9, 4]],
            ['wrap-blouse',        'Wrap Blouse',         'بلوزة ملفوفة',        'womens-ready-to-wear', 139.00, null,   false, 'active', [12, 8, 5]],
            ['pleated-maxi-skirt', 'Pleated Maxi Skirt',  'تنورة ماكسي بليسيه',  'womens-ready-to-wear', 179.00, 149.00, true,  'active', [3, 7, 2]],
            ['tailored-blazer',    'Tailored Blazer',     'بليزر مفصل',          'womens-ready-to-wear', 329.00, null,   false, 'draft',  [4, 4, 4]],

            ['leather-slides',     'Leather Slides',      'شبشب جلد',            'womens-shoes',        189.00, 159.00, false, 'active', [5, 6, 3]],
            ['suede-ankle-boots',  'Suede Ankle Boots',   'بوت كاحل شامواه',     'womens-shoes',        399.00, null,   true,  'active', [2, 3, 2]],

            ['wide-brim-sun-hat',  'Wide Brim Sun Hat',   'قبعة شمس عريضة',      'hats',                 99.00, null,   false, 'active', [10, 0, 0]],
            ['knit-beanie',        'Knit Beanie',         'قبعة صوف',            'hats',                 59.00, 45.00,  false, 'active', [0, 1, 0]],

            ['striped-cover-up',   'Striped Cover Up',    'روب شاطئ مخطط',       'beach',               129.00, null,   false, 'active', [7, 7, 7]],
            ['woven-beach-bag',    'Woven Beach Bag',     'حقيبة شاطئ منسوجة',   'beach',               149.00, 119.00, true,  'active', [4, 0, 0]],

            ['cabin-trolley-case', 'Cabin Trolley Case',  'حقيبة سفر صغيرة',     'travel',              549.00, 479.00, true,  'active', [3, 0, 0]],
            ['passport-holder',    'Passport Holder',     'حافظة جواز سفر',      'travel',               79.00, null,   false, 'active', [15, 0, 0]],

            ['silk-scarf',         'Silk Scarf',          'وشاح حرير',           'accessories',         119.00, null,   false, 'active', [8, 0, 0]],
            ['gold-hoop-earrings', 'Gold Hoop Earrings',  'أقراط دائرية ذهبية',  'accessories',         159.00, 129.00, false, 'active', [0, 0, 1]],
        ];

        $products = [];

        foreach ($rows as $i => [$slug, $en, $ar, $catSlug, $base, $discount, $featured, $status, $stock]) {
            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'              => ['en' => $en, 'ar' => $ar],
                    'short_description' => [
                        'en' => "Demo catalogue item — {$en}.",
                        'ar' => "منتج تجريبي — {$ar}.",
                    ],
                    'description' => [
                        'en' => "Seeded demo product used to populate the store dashboard. {$en}.",
                        'ar' => "منتج تجريبي لعرض لوحة تحكم المتجر. {$ar}.",
                    ],
                    'base_price'     => $base,
                    'discount_price' => $discount,
                    'sku'            => 'DEMO-' . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                    'status'         => $status,
                    'is_featured'    => $featured,
                    'sort_order'     => $i,
                ]
            );

            $product->categories()->syncWithoutDetaching([
                $categories[$catSlug]->id => ['is_primary' => true],
            ]);

            // One variant per size, so the admin list's stock column and its
            // low-stock banner have something real to report.
            foreach ($sizes as $s => $sizeValue) {
                if ($stock[$s] === 0 && $s > 0) {
                    continue; // single-size product
                }

                $variant = ProductVariant::updateOrCreate(
                    ['sku' => strtoupper($slug) . '-' . ($s + 1)],
                    [
                        'product_id'     => $product->id,
                        'stock_quantity' => $stock[$s],
                        'is_active'      => true,
                    ]
                );

                $variant->attributeValues()->syncWithoutDetaching([$sizeValue->id]);
            }

            $products[] = $product;
        }

        return $products;
    }

    /**
     * Orders across every status so the dashboard's status tiles are all
     * non-zero, each with real line items drawn from the seeded catalogue.
     *
     * @param  array<int, Product>  $products
     */
    private function seedOrders(array $products): void
    {
        $customers = [];
        foreach ([
            ['Sara Al-Otaibi', 'sara.demo@example.com',  '0551000101', 'الرياض'],
            ['Noura Hassan',   'noura.demo@example.com', '0551000102', 'جدة'],
            ['Lina Kamal',     'lina.demo@example.com',  '0551000103', 'الدمام'],
        ] as [$name, $email, $phone, $city]) {
            $customers[] = [
                'user' => User::firstOrCreate(
                    ['email' => $email],
                    ['name' => $name, 'phone' => $phone, 'password' => bcrypt('password')]
                ),
                'phone' => $phone,
                'city'  => $city,
            ];
        }

        $plan = [
            ['DEMO-1001', Order::STATUS_PENDING,    Order::PAYMENT_PENDING, 0, [0, 4]],
            ['DEMO-1002', Order::STATUS_PROCESSING, Order::PAYMENT_PAID,    1, [2, 6]],
            ['DEMO-1003', Order::STATUS_PROCESSING, Order::PAYMENT_PAID,    2, [10]],
            ['DEMO-1004', Order::STATUS_SHIPPED,    Order::PAYMENT_PAID,    0, [5, 12]],
            ['DEMO-1005', Order::STATUS_SHIPPED,    Order::PAYMENT_PAID,    1, [8]],
            ['DEMO-1006', Order::STATUS_DELIVERED,  Order::PAYMENT_PAID,    2, [1, 9, 11]],
            ['DEMO-1007', Order::STATUS_DELIVERED,  Order::PAYMENT_PAID,    0, [13]],
            ['DEMO-1008', Order::STATUS_CANCELLED,  Order::PAYMENT_PENDING, 1, [3]],
        ];

        foreach ($plan as $offset => [$number, $status, $paymentStatus, $customerIndex, $productIndexes]) {
            $customer = $customers[$customerIndex];

            DB::transaction(function () use (
                $number, $status, $paymentStatus, $customer, $productIndexes, $products, $offset
            ) {
                $order = Order::updateOrCreate(
                    ['order_number' => $number],
                    [
                        'user_id'          => $customer['user']->id,
                        'status'           => $status,
                        'payment_method'   => Order::PAYMENT_COD,
                        'payment_status'   => $paymentStatus,
                        'shipping_name'    => $customer['user']->name,
                        'shipping_email'   => $customer['user']->email,
                        'shipping_phone'   => $customer['phone'],
                        'shipping_address' => 'شارع الملك عبدالعزيز، مبنى ' . (10 + $offset),
                        'shipping_city'    => $customer['city'],
                        'shipping_country' => 'SA',
                        'delivery_fee'     => 25.00,
                        'notes'            => 'طلب تجريبي للعرض التقديمي.',
                        'created_at'       => now()->subDays(14 - $offset),
                    ]
                );

                // Rebuild the line items so a re-run can't double them up.
                $order->items()->delete();

                $subtotal = 0.0;

                foreach ($productIndexes as $pi) {
                    $product   = $products[$pi];
                    $unitPrice = (float) ($product->discount_price ?? $product->base_price);
                    $quantity  = 1 + ($pi % 2);
                    $lineTotal = $unitPrice * $quantity;
                    $subtotal += $lineTotal;

                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $product->id,
                        'product_name' => $product->getTranslation('name', 'ar'),
                        'product_sku'  => $product->sku,
                        'quantity'     => $quantity,
                        'unit_price'   => $unitPrice,
                        'total_price'  => $lineTotal,
                    ]);
                }

                $order->update([
                    'subtotal'     => $subtotal,
                    'total_amount' => $subtotal + (float) $order->delivery_fee,
                    'paid_at'      => $paymentStatus === Order::PAYMENT_PAID ? now()->subDays(13 - $offset) : null,
                ]);
            });
        }
    }
}
