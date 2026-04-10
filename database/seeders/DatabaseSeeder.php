<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $attr = $this->seedAllAttributes();
            $cats = $this->seedFullCategoryTree();

            // 1. الإلكترونيات (4 منتجات)
            $this->seedElectronics($cats['phones'], $attr);
            
            // 2. الملابس (4 منتجات)
            $this->seedFashion($cats['pants'], $cats['shirts'], $attr);
            
            // 3. العطور والساعات (4 منتجات)
            $this->seedLifestyle($cats['perfumes'], $cats['watches'], $attr);
        });
    }

    private function seedAllAttributes(): array
    {
        $color = Attribute::updateOrCreate(['slug' => 'color'], ['name' => 'اللون', 'sort_order' => 1]);
        $size = Attribute::updateOrCreate(['slug' => 'size'], ['name' => 'المقاس', 'sort_order' => 2]);
        $storage = Attribute::updateOrCreate(['slug' => 'storage'], ['name' => 'سعة التخزين', 'sort_order' => 3]);
        $volume = Attribute::updateOrCreate(['slug' => 'volume'], ['name' => 'الحجم', 'sort_order' => 4]);

        // القيم
        $colorValues = ['أزرق', 'أسود', 'أبيض', 'تيتانيوم', 'بني'];
        foreach ($colorValues as $v) { $color->values()->updateOrCreate(['value' => $v]); }

        $storageValues = ['128GB', '256GB', '512GB', '1TB'];
        foreach ($storageValues as $v) { $storage->values()->updateOrCreate(['value' => $v]); }

        $sizeValues = ['S', 'M', 'L', 'XL', '40', '42', '44'];
        foreach ($sizeValues as $v) { $size->values()->updateOrCreate(['value' => $v]); }

        $volumeValues = ['50ml', '100ml', '200ml'];
        foreach ($volumeValues as $v) { $volume->values()->updateOrCreate(['value' => $v]); }

        return compact('color', 'size', 'storage', 'volume');
    }

    private function seedFullCategoryTree(): array
    {
        $clothing = Category::updateOrCreate(['slug' => 'clothing'], ['name' => 'الملابس', 'is_active' => true]);
        $men = Category::updateOrCreate(['slug' => 'men'], ['parent_id' => $clothing->id, 'name' => 'رجالي']);
        $pants = Category::updateOrCreate(['slug' => 'men-pants'], ['parent_id' => $men->id, 'name' => 'بناطيل']);
        $shirts = Category::updateOrCreate(['slug' => 'men-shirts'], ['parent_id' => $men->id, 'name' => 'قمصان']);

        $electronics = Category::updateOrCreate(['slug' => 'electronics'], ['name' => 'الإلكترونيات', 'is_active' => true]);
        $mobiles = Category::updateOrCreate(['slug' => 'mobiles'], ['parent_id' => $electronics->id, 'name' => 'هواتف']);
        $phones = Category::updateOrCreate(['slug' => 'iphone'], ['parent_id' => $mobiles->id, 'name' => 'آيفون']);

        $lifestyle = Category::updateOrCreate(['slug' => 'lifestyle'], ['name' => 'لايف ستايل', 'is_active' => true]);
        $perfumes = Category::updateOrCreate(['slug' => 'perfumes'], ['parent_id' => $lifestyle->id, 'name' => 'عطور']);
        $watches = Category::updateOrCreate(['slug' => 'watches'], ['parent_id' => $lifestyle->id, 'name' => 'ساعات']);

        return compact('pants', 'shirts', 'phones', 'perfumes', 'watches');
    }

    private function seedElectronics($cat, $attr): void
    {
        $products = [
            ['iPhone 15 Pro', 'iphone-15-pro', 999],
            ['iPhone 14', 'iphone-14', 799],
            ['Samsung S24 Ultra', 's24-ultra', 1199],
            ['Google Pixel 8', 'pixel-8', 699],
        ];

        foreach ($products as $p) {
            $product = Product::create(['name' => $p[0], 'slug' => $p[1], 'base_price' => $p[2], 'sku' => strtoupper($p[1]), 'status' => 'active']);
            $product->categories()->attach($cat->id, ['is_primary' => true]);
            
            $val1 = $attr['storage']->values->first();
            $val2 = $attr['color']->values->first();
            $this->makeVariant($product, $product->sku . '-V1', [$val1, $val2], 10);
        }
    }

    private function seedFashion($pantsCat, $shirtsCat, $attr): void
    {
        $items = [
            [$pantsCat, 'جينز سليم فيت', 'slim-jeans', 45],
            [$pantsCat, 'بنطلون قماش رسمي', 'formal-pants', 55],
            [$shirtsCat, 'قميص قطن أبيض', 'white-shirt', 35],
            [$shirtsCat, 'تيشيرت رياضي', 'sport-tshirt', 25],
        ];

        foreach ($items as $i) {
            $product = Product::create(['name' => $i[1], 'slug' => $i[2], 'base_price' => $i[3], 'sku' => strtoupper($i[2]), 'status' => 'active']);
            $product->categories()->attach($i[0]->id, ['is_primary' => true]);
            
            $sizeVal = $attr['size']->values->where('value', 'L')->first();
            $this->makeVariant($product, $product->sku . '-V1', [$sizeVal], 20);
        }
    }

    private function seedLifestyle($perfumes, $watches, $attr): void
    {
        $items = [
            [$perfumes, 'Dior Sauvage', 'sauvage', 120],
            [$perfumes, 'Blue de Chanel', 'chanel-blue', 135],
            [$watches, 'Rolex Submariner', 'rolex-sub', 8500],
            [$watches, 'Casio G-Shock', 'gshock', 150],
        ];

        foreach ($items as $i) {
            $product = Product::create(['name' => $i[1], 'slug' => $i[2], 'base_price' => $i[3], 'sku' => strtoupper($i[2]), 'status' => 'active']);
            $product->categories()->attach($i[0]->id, ['is_primary' => true]);
            
            $v = $i[0]->slug == 'perfumes' ? $attr['volume']->values->first() : $attr['color']->values->last();
            $this->makeVariant($product, $product->sku . '-V1', [$v], 5);
        }
    }

    private function makeVariant($product, $sku, $values, $stock): void
    {
        $variant = $product->variants()->create(['sku' => $sku, 'stock_quantity' => $stock, 'is_active' => true]);
        $variant->attributeValues()->attach(collect($values)->pluck('id'));
    }
}