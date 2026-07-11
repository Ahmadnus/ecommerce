<?php

namespace App\Services;

use App\Models\OrderCustomization;
use App\Models\ProductCustomization;

/**
 * OrderCustomizationAdminService
 *
 * Business logic for the admin order-customizations screens: listing,
 * resolving the garment config for a stored customization (including the
 * legacy zone-key inference for pre-garment_type records), and garment
 * labels. Never returns views/redirects.
 */
class OrderCustomizationAdminService
{
    // ── Garment configs fallback (for demo orders with product_id = 0) ────────
    private const GARMENT_CONFIGS = [
        'varsity_jacket' => [
            'garment_type' => 'varsity_jacket',
            'zones' => [
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
        'hoodie' => [
            'garment_type' => 'hoodie',
            'zones' => [
                ['key' => 'A',  'label' => 'الصدر الأيسر',    'type' => 'both'],
                ['key' => 'B',  'label' => 'الصدر الأيمن',    'type' => 'both'],
                ['key' => 'C',  'label' => 'الجيب الكنغر',    'type' => 'image'],
                ['key' => 'D1', 'label' => 'الكم الأيسر أعلى','type' => 'text'],
                ['key' => 'D2', 'label' => 'الكم الأيسر وسط', 'type' => 'text'],
                ['key' => 'D3', 'label' => 'الكم الأيسر أسفل','type' => 'text'],
                ['key' => 'E1', 'label' => 'الكم الأيمن أعلى','type' => 'text'],
                ['key' => 'E2', 'label' => 'الكم الأيمن وسط', 'type' => 'text'],
                ['key' => 'E3', 'label' => 'الكم الأيمن أسفل','type' => 'text'],
                ['key' => 'F',  'label' => 'أسفل الغطاء خلف', 'type' => 'text'],
                ['key' => 'G',  'label' => 'الظهر الكبير',    'type' => 'both'],
            ],
            'available_colors' => [
                'body'   => ['#2b3a4a', '#141414', '#6b705c', '#7a0c1f', '#efe9d6'],
                'sleeve' => ['#2b3a4a', '#141414', '#6b705c', '#7a0c1f'],
                'rib'    => ['#23303e', '#0d0d0d', '#555f4e', '#5e0a17'],
                'stripe' => ['#ffffff', '#a8e6cf', '#f4c2e8'],
            ],
        ],
        'graduation_robe' => [
            'garment_type' => 'graduation_robe',
            'zones' => [
                ['key' => '1', 'label' => 'الصدر الأيمن', 'type' => 'both'],
                ['key' => '2', 'label' => 'الصدر الأيسر', 'type' => 'both'],
                ['key' => '4', 'label' => 'الظهر الكبير', 'type' => 'both'],
                ['key' => '5', 'label' => 'الكم الأيسر',  'type' => 'both'],
                ['key' => '6', 'label' => 'الكم الأيمن',  'type' => 'both'],
            ],
            'available_colors' => [
                'main'  => ['#ffffff', '#1d2b53', '#7a0c1f', '#0f3d2e', '#141414'],
                'yoke1' => ['#c8102e', '#c9a227', '#1d2b53', '#0f3d2e', '#141414'],
                'yoke2' => ['#c9a227', '#c8102e', '#ffffff', '#f3f3f1', '#1d2b53'],
                'yoke3' => ['#ffffff', '#f3f3f1', '#c9a227', '#c8102e', '#000000'],
                'line'  => ['#111111', '#444444', '#c9a227', '#c8102e'],
            ],
        ],
        'tshirt' => [
            'garment_type' => 'tshirt',
            'zones' => [
                ['key' => 'A',  'label' => 'الصدر الأيسر',   'type' => 'both'],
                ['key' => 'B',  'label' => 'الصدر الأيمن',   'type' => 'both'],
                ['key' => 'C',  'label' => 'الواجهة الكاملة','type' => 'both'],
                ['key' => 'D1', 'label' => 'الكم الأيسر',    'type' => 'text'],
                ['key' => 'E1', 'label' => 'الكم الأيمن',    'type' => 'text'],
                ['key' => 'F',  'label' => 'الظهر الكبير',   'type' => 'both'],
            ],
            'available_colors' => [
                'body'   => ['#f3f4f6', '#ffffff', '#141414', '#1d2b53', '#7a0c1f', '#0f3d2e'],
                'sleeve' => ['#f3f4f6', '#ffffff', '#141414', '#1d2b53', '#7a0c1f', '#c8102e'],
                'collar' => ['#e5e7eb', '#ffffff', '#141414', '#1d2b53', '#c8102e', '#c9a227'],
                'stitch' => ['#9ca3af', '#ffffff', '#141414', '#c9a227', '#60a5fa'],
            ],
        ],
        'stole' => [
            'garment_type' => 'stole',
            'zones' => [
                ['key' => 'A', 'label' => 'اللوحة اليسرى العلوية', 'type' => 'both'],
                ['key' => 'B', 'label' => 'اللوحة اليسرى السفلية', 'type' => 'both'],
                ['key' => 'C', 'label' => 'اللوحة اليمنى العلوية', 'type' => 'both'],
                ['key' => 'D', 'label' => 'اللوحة اليمنى السفلية', 'type' => 'both'],
            ],
            'available_colors' => [
                'main'   => ['#111111', '#ffffff', '#1d2b53', '#7a0c1f', '#0f3d2e', '#4a1942'],
                'border' => ['#d4a017', '#c8102e', '#1d2b53', '#ffffff', '#0f3d2e', '#111111'],
            ],
        ],
    ];

    // ── Garment type label map ─────────────────────────────────────────────────
    private const GARMENT_LABELS = [
        'varsity_jacket'  => 'جاكيت رياضي',
        'hoodie'          => 'هودي',
        'graduation_robe' => 'ثوب تخرج',
        'tshirt'          => 'تيشيرت',
        'stole'           => 'وشاح التخرج',
    ];

    /**
     * Filtered, paginated customization list for the admin index.
     * $filters keys: status, search.
     */
    public function getFilteredCustomizations(array $filters)
    {
        $query = OrderCustomization::with(['product', 'uploads'])
            ->latest();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$search}%"))
                  ->orWhere('id', is_numeric($search) ? $search : -1)
                  ->orWhere('order_id', is_numeric($search) ? $search : -1);
            });
        }

        return $query->paginate(12)->withQueryString();
    }

    /**
     * Latest customization for a given order, or null.
     */
    public function findForOrder(int $orderId): ?OrderCustomization
    {
        return OrderCustomization::with(['product', 'uploads'])
            ->where('order_id', $orderId)
            ->latest()
            ->first();
    }

    // ── resolveConfig() ───────────────────────────────────────────────────────

    public function resolveConfig(OrderCustomization $customization): ProductCustomization
    {
        // ── Priority 1: Real product in DB with customization config ──────────
        if ($customization->product && method_exists($customization->product, 'customizationConfig')) {
            return $customization->product->customizationConfig();
        }

        // ── Priority 2: garment_type column (THE FIX — stored since the fix) ─
        // This is the correct, unambiguous source. Used for all new orders.
        $garmentType = $customization->garment_type ?? null;

        // ── Priority 3: Legacy fallback — zone-key inference ──────────────────
        // Only used for old records created before the garment_type column existed.
        // Zone-key inference is inherently ambiguous (A/B/G exist in both jacket
        // and hoodie) which is exactly why Priority 2 was added.
        if (! $garmentType) {
            $zones = $customization->selected_zones ?? [];

            if (! empty(array_intersect($zones, ['1', '2', '4', '5', '6']))) {
                // Robe: numeric zone keys — unambiguous
                $garmentType = 'graduation_robe';
            } elseif (! empty(array_intersect($zones, ['A','B','C','D'])) && empty(array_intersect($zones, ['D1','E1','F','G','H','1','2','4','5','6']))) {
                // Stole: has only A/B/C/D zones (no tshirt D1/E1/F, no robe numerics, no jacket zones)
                $garmentType = 'stole';
            } elseif (in_array('C', $zones) && (in_array('D1', $zones) || in_array('E1', $zones) || in_array('F', $zones))) {
                // T-shirt: has zone C (large front panel) + sleeve D1/E1 or back F
                $garmentType = 'tshirt';
            } elseif (! empty(array_intersect($zones, ['D1', 'D2', 'D3'])) && ! in_array('C', $zones)) {
                // Hoodie: D1-D3 sleeve zones, no C
                $garmentType = 'hoodie';
            } elseif (! empty(array_intersect($zones, ['F1', 'F2', 'F3', 'H']))) {
                // Jacket: F1/F2/F3 right-sleeve or H back yoke are jacket-only
                $garmentType = 'varsity_jacket';
            } else {
                // Cannot determine — log it and use jacket as last resort
                \Illuminate\Support\Facades\Log::warning(
                    "OrderCustomization #{$customization->id} has no garment_type and zone inference failed. " .
                    "Zones: " . implode(',', $zones) . ". Defaulting to varsity_jacket."
                );
                $garmentType = 'varsity_jacket';
            }
        }

        if (isset(self::GARMENT_CONFIGS[$garmentType])) {
            return new ProductCustomization(self::GARMENT_CONFIGS[$garmentType]);
        }

        return new ProductCustomization([]);
    }

    // ── garmentLabel() helper ─────────────────────────────────────────────────

    public static function garmentLabel(string $type): string
    {
        return self::GARMENT_LABELS[$type] ?? $type;
    }
}
