<?php

namespace App\Services;

use App\Models\CustomizationUpload;
use App\Models\OrderCustomization;
use App\Models\Product;
use App\Models\ProductCustomization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Business logic for the customer-facing customization flow.
 *
 * The {garment} route parameter accepts:
 *   A) A garment slug  — "varsity_jacket" | "hoodie" | "graduation_robe"
 *      Works with zero DB rows. Builds a virtual product from hardcoded config.
 *   B) A numeric ID    — "1" | "2" | "3"
 *      Maps to a real Product row. Falls back to slug lookup if not found.
 *
 * This means /customize/varsity_jacket, /customize/hoodie, /customize/1
 * all work correctly whether or not products are seeded.
 *
 * This service NEVER returns views or redirects — it returns data/models
 * and throws exceptions; HTTP concerns stay in CustomizationController.
 */
class CustomizationService
{
    public function __construct(
        private readonly CustomizationPricingService $pricing,
        private readonly CartService $cart,
    ) {}

    // ── Hardcoded garment definitions (demo mode) ─────────────────────────────
    // These are used when no real DB product is found.
    // They mirror the cards shown on the index page.

    private const GARMENT_CONFIGS = [
        'varsity_jacket' => [
            'id'          => 0,
            'name'        => 'Varsity Jacket',
            'price'       => 249,
            'description' => 'جاكيت كلاسيكي قابل للتخصيص بأكمام جلدية',
            'config'      => [
                'garment_type' => 'varsity_jacket',
                'zones'        => [
                    ['key' => 'A',  'label' => 'الصدر الأيسر',     'type' => 'both'],
                    ['key' => 'B',  'label' => 'الصدر الأيمن',     'type' => 'both'],
                    ['key' => 'C',  'label' => 'الجيب الأيسر',     'type' => 'image'],
                    ['key' => 'D',  'label' => 'الجيب الأيمن',     'type' => 'image'],
                    ['key' => 'E1', 'label' => 'الكم الأيسر أعلى', 'type' => 'text'],
                    ['key' => 'E2', 'label' => 'الكم الأيسر وسط',  'type' => 'text'],
                    ['key' => 'E3', 'label' => 'الكم الأيسر أسفل', 'type' => 'text'],
                    ['key' => 'F1', 'label' => 'الكم الأيمن أعلى', 'type' => 'text'],
                    ['key' => 'F2', 'label' => 'الكم الأيمن وسط',  'type' => 'text'],
                    ['key' => 'F3', 'label' => 'الكم الأيمن أسفل', 'type' => 'text'],
                    ['key' => 'G',  'label' => 'الظهر',            'type' => 'both'],
                    ['key' => 'H',  'label' => 'الياقة الخلفية',   'type' => 'text'],
                ],
                'available_colors' => [
                    'body'   => ['#141414', '#1d2b53', '#7a0c1f', '#0f3d2e', '#4a1942'],
                    'sleeve' => ['#f3f3f1', '#e9e2cf', '#1a1a1a', '#d4b8e0'],
                    'rib'    => ['#141414', '#c8102e', '#c9a227', '#e8e8e8'],
                    'stripe' => ['#ffffff', '#c9a227', '#e94560'],
                ],
            ],
        ],
        'hoodie' => [
            'id'          => 0,
            'name'        => 'Studio Hoodie',
            'price'       => 179,
            'description' => 'هودي فليس بجيب كنغر وغطاء رأس',
            'config'      => [
                'garment_type' => 'hoodie',
                'zones'        => [
                    ['key' => 'A',  'label' => 'الصدر الأيسر',   'type' => 'both'],
                    ['key' => 'B',  'label' => 'الصدر الأيمن',   'type' => 'both'],
                    ['key' => 'C',  'label' => 'الجيب الكنغر',   'type' => 'image'],
                    ['key' => 'D1', 'label' => 'الكم الأيسر أعلى','type' => 'text'],
                    ['key' => 'E1', 'label' => 'الكم الأيمن أعلى','type' => 'text'],
                    ['key' => 'F',  'label' => 'أسفل الغطاء خلف','type' => 'text'],
                    ['key' => 'G',  'label' => 'الظهر الكبير',   'type' => 'both'],
                ],
                'available_colors' => [
                    'body'   => ['#2b3a4a', '#141414', '#6b705c', '#7a0c1f', '#efe9d6'],
                    'sleeve' => ['#2b3a4a', '#141414', '#6b705c', '#7a0c1f'],
                    'rib'    => ['#23303e', '#0d0d0d', '#555f4e', '#5e0a17'],
                    'stripe' => ['#ffffff', '#a8e6cf', '#f4c2e8'],
                ],
            ],
        ],
        'graduation_robe' => [
            'id'          => 0,
            'name'        => 'Graduation Robe',
            'price'       => 199,
            'description' => 'ثوب تخرج أنيق بأشرطة ياقة ثلاثية الطبقات',
            'config'      => [
                'garment_type' => 'graduation_robe',
                'zones'        => [
                    ['key' => '1', 'label' => 'الصدر الأيمن',  'type' => 'both'],
                    ['key' => '2', 'label' => 'الصدر الأيسر',  'type' => 'both'],
                    ['key' => '4', 'label' => 'الظهر الكبير',  'type' => 'both'],
                    ['key' => '5', 'label' => 'الكم الأيسر',   'type' => 'text'],
                    ['key' => '6', 'label' => 'الكم الأيمن',   'type' => 'text'],
                ],
                'available_colors' => [
                    'main'  => ['#ffffff', '#1d2b53', '#7a0c1f', '#0f3d2e', '#141414'],
                    'yoke1' => ['#c8102e', '#c9a227', '#1d2b53', '#0f3d2e'],
                    'yoke2' => ['#c9a227', '#c8102e', '#ffffff', '#f3f3f1'],
                    'yoke3' => ['#ffffff', '#f3f3f1', '#c9a227', '#c8102e'],
                    'line'  => ['#111111', '#444444', '#c9a227'],
                ],
            ],
        ],
        'tshirt' => [
            'id'          => 0,
            'name'        => 'T-Shirt',
            'price'       => 99,
            'description' => 'تيشيرت قطني بقصة مريحة — 6 مناطق تصميم',
            'config'      => [
                'garment_type' => 'tshirt',
                'zones'        => [
                    ['key' => 'A',  'label' => 'الصدر الأيسر',   'type' => 'both'],
                    ['key' => 'B',  'label' => 'الصدر الأيمن',   'type' => 'both'],
                    ['key' => 'C',  'label' => 'الواجهة الكاملة','type' => 'both'],
                    ['key' => 'D1', 'label' => 'الكم الأيسر',    'type' => 'text'],
                    ['key' => 'E1', 'label' => 'الكم الأيمن',    'type' => 'text'],
                    ['key' => 'F',  'label' => 'الظهر الكبير',   'type' => 'both'],
                ],
                'available_colors' => [
                    'body'   => ['#f3f4f6', '#ffffff', '#141414', '#1d2b53', '#7a0c1f', '#0f3d2e', '#c9a227', '#4a1942'],
                    'sleeve' => ['#f3f4f6', '#ffffff', '#141414', '#1d2b53', '#7a0c1f', '#c8102e'],
                    'collar' => ['#e5e7eb', '#ffffff', '#141414', '#1d2b53', '#c8102e', '#c9a227'],
                    'stitch' => ['#9ca3af', '#ffffff', '#141414', '#c9a227', '#60a5fa'],
                ],
            ],
        ],
        'stole' => [
            'id'          => 0,
            'name'        => 'Graduation Stole',
            'price'       => 79,
            'description' => 'وشاح تخرج بحدود ذهبية — 4 مناطق تخصيص',
            'config'      => [
                'garment_type' => 'stole',
                'zones'        => [
                    ['key' => 'A', 'label' => 'اللوحة اليسرى العلوية',  'type' => 'both'],
                    ['key' => 'B', 'label' => 'اللوحة اليسرى السفلية',  'type' => 'both'],
                    ['key' => 'C', 'label' => 'اللوحة اليمنى العلوية',  'type' => 'both'],
                    ['key' => 'D', 'label' => 'اللوحة اليمنى السفلية',  'type' => 'both'],
                ],
                'available_colors' => [
                    'main'   => ['#111111', '#ffffff', '#1d2b53', '#7a0c1f', '#0f3d2e', '#4a1942'],
                    'border' => ['#d4a017', '#c8102e', '#1d2b53', '#ffffff', '#0f3d2e', '#111111'],
                ],
            ],
        ],
    ];

    // ── Slug ↔ numeric-ID map ─────────────────────────────────────────────────
    // Mirrors the $hardcodedIds on the index page.
    // When you seed real products, their IDs will match these values.
    private const ID_TO_SLUG = [
        1 => 'varsity_jacket',
        2 => 'hoodie',
        3 => 'graduation_robe',
        4 => 'tshirt',
        5 => 'stole',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // resolveGarment()
    //
    // Returns [$productObject, $configObject, $isDemo]
    //
    // Resolution order:
    //   1. Numeric ID → look up real Product in DB
    //   2. Slug       → look up real Product by garment_type in customization_config
    //   3. Fallback   → build a virtual stdClass from GARMENT_CONFIGS (demo mode)
    // ─────────────────────────────────────────────────────────────────────────

    public function resolveGarment(string $garment): array
    {
        $slug = $this->toSlug($garment);

        // ── Try real DB product ───────────────────────────────────────────────
        try {
            if (Schema::hasColumn('products', 'is_customizable')) {
                $product = null;

                if (is_numeric($garment)) {
                    $product = Product::where('is_customizable', true)->find((int) $garment);
                }

                if (! $product && $slug) {
                    // Find by garment_type stored in JSON config
                    $product = Product::where('is_customizable', true)
                        ->whereJsonContains('customization_config->garment_type', $slug)
                        ->first();
                }

                if ($product) {
                    return [$product, $product->customizationConfig(), false];
                }
            }
        } catch (\Throwable) {
            // DB not ready — fall through to demo mode
        }

        // ── Demo mode: build virtual product from hardcoded config ────────────
        abort_unless(isset(self::GARMENT_CONFIGS[$slug]), 404, "Garment type '{$slug}' not found.");

        $def     = self::GARMENT_CONFIGS[$slug];
        $config  = new ProductCustomization($def['config']);

        // Build a plain object that mimics the Product model interface
        // so the view can call $product->name, $product->price, etc.
        $virtual              = new \stdClass();
        $virtual->id          = $def['id'];
        $virtual->name        = $def['name'];
        $virtual->price       = $def['price'];
        $virtual->formatted_price = number_format($def['price'], 0) . ' ر.س';
        $virtual->short_description = $def['description'];

        return [$virtual, $config, true];
    }

    // ── Convert numeric ID or slug to a canonical slug ────────────────────────
    public function toSlug(string $garment): string
    {
        if (is_numeric($garment)) {
            return self::ID_TO_SLUG[(int) $garment] ?? '';
        }
        return $garment;
    }

    /**
     * Persist a customization (with its uploads), price it, and add it to
     * the cart.
     *
     * $input keys (already validated by StoreCustomizationRequest):
     *   colors, texts, text_styles, size, selected_zones, design_snapshot, notes
     * $images: array of UploadedFile keyed by zone key (or [] if none).
     *
     * Returns [OrderCustomization $customization, array $priceBreakdown].
     *
     * @throws \Throwable if the DB write fails (already rolled back and reported).
     */
    public function store(array $input, array $images, $product, ProductCustomization $config, bool $isDemo): array
    {
        // Cast to strings — PHP auto-converts numeric keys ('1','2'…) to int in $_FILES
        $validZoneKeys = array_map('strval', $config->zoneKeys());

        // ── Colors ────────────────────────────────────────────────────────────
        $rawColors = $input['colors'] ?? [];
        $colors    = array_intersect_key($rawColors, array_flip(array_keys($config->availableColors())));

        // ── Texts + styles ────────────────────────────────────────────────────
        $rawTexts  = $input['texts'] ?? [];
        $rawStyles = $input['text_styles'] ?? [];
        $rawTexts  = array_combine(array_map('strval', array_keys($rawTexts)), array_values($rawTexts));
        $texts     = array_intersect_key($rawTexts, array_flip($validZoneKeys));
        $texts     = array_filter($texts, fn($v) => $v !== '' && $v !== null);

        $richTexts = [];
        foreach ($texts as $key => $value) {
            $style            = $rawStyles[$key] ?? [];
            $richTexts[$key]  = [
                'value'     => $value,
                'color'     => $style['color']     ?? '#ffffff',
                'fontSize'  => (int) ($style['fontSize']  ?? 22),
                'fontStyle' => $style['fontStyle']  ?? 'normal',
            ];
        }

        // ── Size ──────────────────────────────────────────────────────────────
        $garmentType    = $config->garmentType();
        $validSizes     = array_keys(config("garment_sizes.charts.{$garmentType}", []));
        $requestedSize  = $input['size'] ?? '';
        $size           = in_array($requestedSize, $validSizes, true) ? $requestedSize : null;

        // ── Selected zones ────────────────────────────────────────────────────
        $rawZones      = array_map('strval', $input['selected_zones'] ?? []);
        $selectedZones = array_values(array_intersect($rawZones, $validZoneKeys));

        // ── Design snapshot ───────────────────────────────────────────────────
        $snapshot = null;
        if ($raw = ($input['design_snapshot'] ?? null)) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $snapshot = $decoded;
            }
        }

        DB::beginTransaction();
        try {
            $customization = OrderCustomization::create([
                'order_id'       => 0,
                'product_id'     => $isDemo ? 0 : $product->id,
                'garment_type'   => $garmentType,   // ← always save the type
                'colors'         => $colors    ?: null,
                'texts'          => $richTexts ?: null,
                'selected_zones' => $selectedZones ?: null,
                'notes'          => $input['notes'] ?? null,
                'size'           => $size,
                'status'         => 'pending',
            ]);

            // ── Images ────────────────────────────────────────────────────────
            foreach ($images as $zoneKey => $uploadedFile) {
                if (! in_array((string) $zoneKey, $validZoneKeys, true)) continue;
                if (! $uploadedFile->isValid())                 continue;

                $path = $uploadedFile->store(
                    "customizations/{$customization->id}",
                    'public'
                );

                CustomizationUpload::create([
                    'order_customization_id' => $customization->id,
                    'zone_key'               => (string) $zoneKey,  // ensure string, not int
                    'path'                   => $path,
                    'original_filename'      => $uploadedFile->getClientOriginalName(),
                    'mime_type'              => $uploadedFile->getMimeType(),
                    'size_bytes'             => $uploadedFile->getSize(),
                ]);
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            throw $e;
        }

        // ── Refresh with relations so the pricing service can count
        //    images/texts without extra queries (uploads were just created above) ──
        $customization->load('uploads');

        // ── Compute the customization price breakdown (JOD, base currency) ────
        //    base price + (images × price-per-image) + (texts × price-per-text)
        //    See CustomizationPricingService for the single source of truth.
        $priceBreakdown = $this->pricing->breakdown($customization);

        // ── Add the customized garment to the cart ─────────────────────────────
        //    product_id = 0 in demo mode → CartService treats this as a
        //    "virtual" line item; it stores customization_id + the computed JOD
        //    price so the cart/checkout pages show the correct total without
        //    recalculating anything.
        $this->cart->add(
            productId: $isDemo ? 0 : $product->id,
            quantity: 1,
            variantId: null,
            customizationId: $customization->id,
            customPriceJod: $priceBreakdown['total'],
        );

        return [$customization, $priceBreakdown];
    }

    /**
     * Data for the customizable-products landing page (customize.index).
     *
     * Returns hardcoded garment cards (Mode A / labels for Mode B) plus real
     * DB products when the schema and rows exist.
     */
    public function getLandingData(): array
    {
        // ── Hardcoded card definitions (always present) ───────────────────────
        // These power the SVG preview cards shown in Mode A (no DB products yet)
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

        return compact('products', 'garmentCards', 'useHardcoded');
    }

    // ── Attach pending customization to a real order (call from OrderController) ─
    public function attachPendingCustomization(int $orderId): void
    {
        if ($id = session()->pull('pending_customization_id')) {
            OrderCustomization::where('id', $id)
                ->where('order_id', 0)
                ->update(['order_id' => $orderId]);
        }
    }
}
