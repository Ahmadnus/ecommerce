<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CustomizableProductsController extends Controller
{
    public function index(): View
    {
        // ── Hardcoded card definitions (always present) ───────────────────────
        // These power the 3 SVG preview cards shown in Mode A (no DB products yet)
        // and supply labels/descriptions in Mode B (real DB products).
        $garmentCards = [
            [
                'key'     => 'varsity_jacket',
                'type'    => 'جاكيت رياضي',
                'name'    => 'Varsity Jacket',
                'desc'    => 'جاكيت كلاسيكي بأكمام جلدية وأشرطة قابلة للتخصيص — 8 مناطق تصميم.',
                'zones'   => 8,
                'price'   => '249',
                'popular' => true,
                'colors'  => ['#141414', '#f3f3f1', '#c8102e', '#c9a227'],
                'svg_key' => 'jacket',
            ],
            [
                'key'     => 'hoodie',
                'type'    => 'هودي',
                'name'    => 'Studio Hoodie',
                'desc'    => 'هودي فليس بجيب كنغر وغطاء رأس — 7 مناطق تصميم بالأمام والخلف.',
                'zones'   => 7,
                'price'   => '179',
                'popular' => false,
                'colors'  => ['#2b3a4a', '#6b705c', '#7a0c1f', '#efe9d6'],
                'svg_key' => 'hoodie',
            ],
            [
                'key'     => 'graduation_robe',
                'type'    => 'ثوب التخرج',
                'name'    => 'Graduation Robe',
                'desc'    => 'ثوب تخرج أنيق بأشرطة ياقة ثلاثية الطبقات — 5 مناطق تصميم.',
                'zones'   => 5,
                'price'   => '199',
                'popular' => false,
                'colors'  => ['#ffffff', '#1d2b53', '#7a0c1f', '#0f3d2e'],
                'svg_key' => 'robe',
            ],
            [
                'key'     => 'tshirt',
                'type'    => 'تيشيرت',
                'name'    => 'Studio T-Shirt',
                'desc'    => 'تيشيرت قطني بقصة مريحة — 6 مناطق تصميم بالأمام والخلف والأكمام.',
                'zones'   => 6,
                'price'   => '99',
                'popular' => false,
                'colors'  => ['#f3f4f6', '#141414', '#1d2b53', '#7a0c1f'],
                'svg_key' => 'tshirt',
            ],
            [
                'key'     => 'stole',
                'type'    => 'وشاح التخرج',
                'name'    => 'Graduation Stole',
                'desc'    => 'وشاح تخرج بحدود ذهبية — 4 مناطق تصميم.',
                'zones'   => 4,
                'price'   => '79',
                'popular' => false,
                'colors'  => ['#111111', '#d4a017'],
                'svg_key' => 'stole',
            ],
        ];

        // ── Try to load real DB products ──────────────────────────────────────
        $products     = collect(); // default: empty — triggers Mode A in the view
        $useHardcoded = true;

        try {
            if (Schema::hasColumn('products', 'is_customizable')) {
                $query = Product::where('is_customizable', true);

                if (Schema::hasColumn('products', 'is_active')) {
                    $query->where('is_active', true);
                }

                $products     = $query->latest()->paginate(12);
                $useHardcoded = $products->isEmpty();
            }
        } catch (\Throwable $e) {
            // DB not ready / migration pending — stay in Mode A
            $products     = collect();
            $useHardcoded = true;
        }

        return view('customize.index', compact('products', 'garmentCards', 'useHardcoded'));
    }
}