<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\AttributeValue;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // استدعاء ملف المشرفين فقط وتجاهل باقي الداتا
        $this->call([
            AdminSeeder::class,
        ]);

        // ─────────────────────────────────────────────
        // Categories
        // ─────────────────────────────────────────────
        $categoriesData = [
            ['name' => 'الكترونيات'],
            ['name' => 'ملابس'],
            ['name' => 'أحذية'],
            ['name' => 'ساعات'],
            ['name' => 'اكسسوارات'],
        ];

        $categories = collect();

        foreach ($categoriesData as $index => $data) {
            $categories->push(
                Category::firstOrCreate(
                    ['name' => $data['name']],
                    [
                        'is_active' => true,
                        'sort_order' => $index,
                    ]
                )
            );
        }

        // ─────────────────────────────────────────────
        // Attributes + Values
        // ─────────────────────────────────────────────

        $attributesData = [
            [
                'name' => 'اللون',
                'type' => 'color',
                'values' => [
                    ['value' => 'red',   'label' => 'أحمر', 'color_hex' => '#ef4444'],
                    ['value' => 'blue',  'label' => 'أزرق', 'color_hex' => '#3b82f6'],
                    ['value' => 'black', 'label' => 'أسود', 'color_hex' => '#111827'],
                    ['value' => 'white', 'label' => 'أبيض', 'color_hex' => '#ffffff'],
                ],
            ],
            [
                'name' => 'الحجم',
                'type' => 'select',
                'values' => [
                    ['value' => 's', 'label' => 'Small'],
                    ['value' => 'm', 'label' => 'Medium'],
                    ['value' => 'l', 'label' => 'Large'],
                    ['value' => 'xl', 'label' => 'XL'],
                ],
            ],
            [
                'name' => 'التخزين',
                'type' => 'select',
                'values' => [
                    ['value' => '64',  'label' => '64GB'],
                    ['value' => '128', 'label' => '128GB'],
                    ['value' => '256', 'label' => '256GB'],
                ],
            ],
        ];

        foreach ($attributesData as $aIndex => $attrData) {

            $attribute = Attribute::firstOrCreate(
                ['name' => $attrData['name']],
                [
                    'slug' => \Str::slug($attrData['name']),
                    'type' => $attrData['type'],
                    'sort_order' => $aIndex,
                ]
            );

            foreach ($attrData['values'] as $vIndex => $value) {
                AttributeValue::firstOrCreate(
                    [
                        'attribute_id' => $attribute->id,
                        'value' => $value['value'],
                    ],
                    [
                        'label' => $value['label'],
                        'color_hex' => $value['color_hex'] ?? null,
                        'sort_order' => $vIndex,
                    ]
                );
            }
        }

        // ─────────────────────────────────────────────
        // Products
        // ─────────────────────────────────────────────
        $products = collect();

        for ($i = 1; $i <= 20; $i++) {
            $basePrice = rand(50, 500);
            $hasDiscount = rand(0, 1) === 1;
            $discountPrice = $hasDiscount ? rand(30, max(31, $basePrice - 10)) : null;

            $products->push(
                Product::firstOrCreate(
                    ['name' => 'منتج ' . $i],
                    [
                        'slug' => 'product-' . $i,
                        'description' => 'وصف تجريبي للمنتج ' . $i,
                        'short_description' => 'وصف مختصر للمنتج ' . $i,
                        'base_price' => $basePrice,
                        'discount_price' => $discountPrice,
                        'sku' => 'SKU-' . str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                        'status' => 'active',
                        'is_featured' => $i <= 5,
                        'sort_order' => $i,
                        'meta' => [
                            'title' => 'منتج ' . $i,
                            'description' => 'منتج تجريبي',
                        ],
                    ]
                )
            );
        }

        // ─────────────────────────────────────────────
        // Attach products to random categories
        // ─────────────────────────────────────────────
        foreach ($products as $product) {
            $randomCategories = $categories->random(rand(1, min(3, $categories->count())));

            foreach ($randomCategories as $index => $category) {
                $product->categories()->syncWithoutDetaching([
                    $category->id => [
                        'is_primary' => $index === 0,
                    ],
                ]);
            }
        }
    }
}