<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            JordanCountrySeeder::class,
        ]);

        $now = now();

        $json = fn ($value) => json_encode($value, JSON_UNESCAPED_UNICODE);

        /**
         * Categories
         */
        $perfumesId = DB::table('categories')->insertGetId([
            'parent_id'   => null,
            'name'        => $json(['ar' => 'العطور', 'en' => 'Perfumes']),
            'description' => $json(['ar' => 'مجموعة العطور والمنتجات المرتبطة بها', 'en' => 'Perfume and related products']),
            'slug'        => Str::slug('العطور'),
            'image'       => null,
            'depth'       => 0,
            'path'        => 'perfumes',
            'sort_order'  => 1,
            'is_active'   => 1,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        $womenId = DB::table('categories')->insertGetId([
            'parent_id'   => $perfumesId,
            'name'        => $json(['ar' => 'عطور نسائية', 'en' => 'Women Perfumes']),
            'description' => $json(['ar' => 'عطور مخصصة للسيدات', 'en' => 'Perfumes for women']),
            'slug'        => Str::slug('عطور نسائية'),
            'image'       => null,
            'depth'       => 1,
            'path'        => 'perfumes/women-perfumes',
            'sort_order'  => 2,
            'is_active'   => 1,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        $menId = DB::table('categories')->insertGetId([
            'parent_id'   => $perfumesId,
            'name'        => $json(['ar' => 'عطور رجالية', 'en' => 'Men Perfumes']),
            'description' => $json(['ar' => 'عطور مخصصة للرجال', 'en' => 'Perfumes for men']),
            'slug'        => Str::slug('عطور رجالية'),
            'image'       => null,
            'depth'       => 1,
            'path'        => 'perfumes/men-perfumes',
            'sort_order'  => 3,
            'is_active'   => 1,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        $unisexId = DB::table('categories')->insertGetId([
            'parent_id'   => $perfumesId,
            'name'        => $json(['ar' => 'عطور للجنسين', 'en' => 'Unisex Perfumes']),
            'description' => $json(['ar' => 'عطور مناسبة للجميع', 'en' => 'Perfumes for everyone']),
            'slug'        => Str::slug('عطور للجنسين'),
            'image'       => null,
            'depth'       => 1,
            'path'        => 'perfumes/unisex-perfumes',
            'sort_order'  => 4,
            'is_active'   => 1,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        $giftsId = DB::table('categories')->insertGetId([
            'parent_id'   => null,
            'name'        => $json(['ar' => 'بوكسات وهدايا', 'en' => 'Gift Sets']),
            'description' => $json(['ar' => 'بوكسات وهدايا فاخرة', 'en' => 'Premium gift sets']),
            'slug'        => Str::slug('بوكسات وهدايا'),
            'image'       => null,
            'depth'       => 0,
            'path'        => 'gift-sets',
            'sort_order'  => 5,
            'is_active'   => 1,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        /**
         * Attributes
         */
        $sizeAttributeId = DB::table('attributes')->insertGetId([
            'name'       => $json(['ar' => 'الحجم', 'en' => 'Size']),
            'slug'       => Str::slug('الحجم'),
            'type'       => 'select',
            'sort_order' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $concentrationAttributeId = DB::table('attributes')->insertGetId([
            'name'       => $json(['ar' => 'التركيز', 'en' => 'Concentration']),
            'slug'       => Str::slug('التركيز'),
            'type'       => 'select',
            'sort_order' => 2,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        /**
         * Attribute Values
         */
        $size30Id = DB::table('attribute_values')->insertGetId([
            'attribute_id' => $sizeAttributeId,
            'value'        => $json(['ar' => '30 مل', 'en' => '30 ml']),
            'label'        => $json(['ar' => '30 مل', 'en' => '30 ml']),
            'color_hex'    => null,
            'sort_order'   => 1,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        $size50Id = DB::table('attribute_values')->insertGetId([
            'attribute_id' => $sizeAttributeId,
            'value'        => $json(['ar' => '50 مل', 'en' => '50 ml']),
            'label'        => $json(['ar' => '50 مل', 'en' => '50 ml']),
            'color_hex'    => null,
            'sort_order'   => 2,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        $size100Id = DB::table('attribute_values')->insertGetId([
            'attribute_id' => $sizeAttributeId,
            'value'        => $json(['ar' => '100 مل', 'en' => '100 ml']),
            'label'        => $json(['ar' => '100 مل', 'en' => '100 ml']),
            'color_hex'    => null,
            'sort_order'   => 3,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        $edpId = DB::table('attribute_values')->insertGetId([
            'attribute_id' => $concentrationAttributeId,
            'value'        => $json(['ar' => 'بارفيوم', 'en' => 'Parfum']),
            'label'        => $json(['ar' => 'بارفيوم', 'en' => 'Parfum']),
            'color_hex'    => null,
            'sort_order'   => 1,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        $edtId = DB::table('attribute_values')->insertGetId([
            'attribute_id' => $concentrationAttributeId,
            'value'        => $json(['ar' => 'أو دو برفيوم', 'en' => 'Eau de Parfum']),
            'label'        => $json(['ar' => 'أو دو برفيوم', 'en' => 'Eau de Parfum']),
            'color_hex'    => null,
            'sort_order'   => 2,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        /**
         * Products
         */
        $products = [
            [
                'name'             => ['ar' => 'عود رويال', 'en' => 'Oud Royal'],
                'description'      => ['ar' => 'عطر شرقي فاخر بنفحات العود والعنبر.', 'en' => 'A premium oriental fragrance with oud and amber notes.'],
                'short_description'=> ['ar' => 'عطر شرقي فاخر.', 'en' => 'Premium oriental fragrance.'],
                'slug'             => 'oud-royal',
                'base_price'       => 120.00,
                'discount_price'   => 99.00,
                'sku'              => 'ODR-001',
                'image'            => 'products/oud-royal.jpg',
                'images'           => ['products/oud-royal-1.jpg', 'products/oud-royal-2.jpg'],
                'status'           => 'active',
                'is_featured'      => 1,
                'sort_order'       => 1,
                'meta'             => ['ar' => ['title' => 'عود رويال'], 'en' => ['title' => 'Oud Royal']],
                'categories'       => [$menId, $unisexId],
                'variants'         => [
                    ['sku' => 'ODR-30-EDP',  'size_id' => $size30Id,  'concentration_id' => $edpId, 'price_override' => 89.00,  'stock_quantity' => 40, 'variant_image' => 'products/oud-royal-30.jpg'],
                    ['sku' => 'ODR-50-EDP',  'size_id' => $size50Id,  'concentration_id' => $edpId, 'price_override' => 109.00, 'stock_quantity' => 30, 'variant_image' => 'products/oud-royal-50.jpg'],
                    ['sku' => 'ODR-100-EDP', 'size_id' => $size100Id, 'concentration_id' => $edpId, 'price_override' => 149.00, 'stock_quantity' => 20, 'variant_image' => 'products/oud-royal-100.jpg'],
                ],
            ],
            [
                'name'             => ['ar' => 'روز نوار', 'en' => 'Rose Noir'],
                'description'      => ['ar' => 'عطر زهري أنيق بلمسة مسك ناعم.', 'en' => 'Elegant floral fragrance with soft musk.'],
                'short_description'=> ['ar' => 'عطر زهري أنيق.', 'en' => 'Elegant floral fragrance.'],
                'slug'             => 'rose-noir',
                'base_price'       => 95.00,
                'discount_price'   => 79.00,
                'sku'              => 'RSN-001',
                'image'            => 'products/rose-noir.jpg',
                'images'           => ['products/rose-noir-1.jpg', 'products/rose-noir-2.jpg'],
                'status'           => 'active',
                'is_featured'      => 1,
                'sort_order'       => 2,
                'meta'             => ['ar' => ['title' => 'روز نوار'], 'en' => ['title' => 'Rose Noir']],
                'categories'       => [$womenId, $unisexId],
                'variants'         => [
                    ['sku' => 'RSN-30-EDT',  'size_id' => $size30Id,  'concentration_id' => $edtId, 'price_override' => 69.00,  'stock_quantity' => 50, 'variant_image' => 'products/rose-noir-30.jpg'],
                    ['sku' => 'RSN-50-EDT',  'size_id' => $size50Id,  'concentration_id' => $edtId, 'price_override' => 89.00,  'stock_quantity' => 35, 'variant_image' => 'products/rose-noir-50.jpg'],
                    ['sku' => 'RSN-100-EDT', 'size_id' => $size100Id, 'concentration_id' => $edtId, 'price_override' => 119.00, 'stock_quantity' => 25, 'variant_image' => 'products/rose-noir-100.jpg'],
                ],
            ],
            [
                'name'             => ['ar' => 'صحراء الليل', 'en' => 'Desert Night'],
                'description'      => ['ar' => 'عطر دافئ بلمسات عنبر وفانيلا.', 'en' => 'Warm fragrance with amber and vanilla notes.'],
                'short_description'=> ['ar' => 'عطر دافئ جذاب.', 'en' => 'Warm and attractive fragrance.'],
                'slug'             => 'desert-night',
                'base_price'       => 110.00,
                'discount_price'   => null,
                'sku'              => 'DSN-001',
                'image'            => 'products/desert-night.jpg',
                'images'           => ['products/desert-night-1.jpg'],
                'status'           => 'active',
                'is_featured'      => 0,
                'sort_order'       => 3,
                'meta'             => ['ar' => ['title' => 'صحراء الليل'], 'en' => ['title' => 'Desert Night']],
                'categories'       => [$menId, $giftsId],
                'variants'         => [
                    ['sku' => 'DSN-30-EDP',  'size_id' => $size30Id,  'concentration_id' => $edpId, 'price_override' => 85.00,  'stock_quantity' => 45, 'variant_image' => 'products/desert-night-30.jpg'],
                    ['sku' => 'DSN-50-EDP',  'size_id' => $size50Id,  'concentration_id' => $edpId, 'price_override' => 105.00, 'stock_quantity' => 28, 'variant_image' => 'products/desert-night-50.jpg'],
                    ['sku' => 'DSN-100-EDP', 'size_id' => $size100Id, 'concentration_id' => $edpId, 'price_override' => 139.00, 'stock_quantity' => 18, 'variant_image' => 'products/desert-night-100.jpg'],
                ],
            ],
        ];

        foreach ($products as $productData) {
            $productId = DB::table('products')->insertGetId([
                'name'              => $json($productData['name']),
                'description'       => $json($productData['description']),
                'short_description' => $json($productData['short_description']),
                'slug'              => $productData['slug'],
                'base_price'        => $productData['base_price'],
                'discount_price'    => $productData['discount_price'],
                'sku'               => $productData['sku'],
                'image'             => $productData['image'],
                'images'            => $json($productData['images']),
                'status'            => $productData['status'],
                'is_featured'       => $productData['is_featured'],
                'sort_order'        => $productData['sort_order'],
                'meta'              => $json($productData['meta']),
                'created_at'        => $now,
                'updated_at'        => $now,
                'deleted_at'        => null,
            ]);

            foreach ($productData['categories'] as $index => $categoryId) {
                DB::table('category_product')->insert([
                    'category_id' => $categoryId,
                    'product_id'   => $productId,
                    'is_primary'   => $index === 0,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            }

            foreach ($productData['variants'] as $variant) {
                $variantId = DB::table('product_variants')->insertGetId([
                    'product_id'      => $productId,
                    'sku'             => $variant['sku'],
                    'price_override'  => $variant['price_override'],
                    'stock_quantity'  => $variant['stock_quantity'],
                    'variant_image'   => $variant['variant_image'],
                    'is_active'       => 1,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                    'deleted_at'      => null,
                ]);

                DB::table('product_variant_attribute_values')->insert([
                    ['product_variant_id' => $variantId, 'attribute_value_id' => $variant['size_id']],
                    ['product_variant_id' => $variantId, 'attribute_value_id' => $variant['concentration_id']],
                ]);
            }
        }
    }
}