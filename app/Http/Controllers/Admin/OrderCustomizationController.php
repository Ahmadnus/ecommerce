<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderCustomization;
use App\Models\ProductCustomization;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderCustomizationController extends Controller
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
    ];

    // ── Garment type label map ─────────────────────────────────────────────────
    private const GARMENT_LABELS = [
        'varsity_jacket'  => 'جاكيت رياضي',
        'hoodie'          => 'هودي',
        'graduation_robe' => 'ثوب تخرج',
    ];

    // ── index() ────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $query = OrderCustomization::with(['product', 'uploads'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('notes', 'like', "%{$search}%")
                  ->orWhereHas('product', fn ($p) => $p->where('name', 'like', "%{$search}%"))
                  ->orWhere('id', is_numeric($search) ? $search : -1)
                  ->orWhere('order_id', is_numeric($search) ? $search : -1);
            });
        }

        $customizations = $query->paginate(12)->withQueryString();

        return view('admin.order-customizations.index', compact('customizations'));
    }

    // ── show() ─────────────────────────────────────────────────────────────────

    public function show(OrderCustomization $customization): View
    {
        $customization->load(['product', 'uploads']);

        $config = $this->resolveConfig($customization);

        return view('admin.order-customizations.show', compact('customization', 'config'));
    }

    // ── embedded() — inline panel inside order detail view ────────────────────

    public function embedded(int $orderId): View
    {
        $customization = OrderCustomization::with(['product', 'uploads'])
            ->where('order_id', $orderId)
            ->latest()
            ->first();

        abort_if(! $customization, 404, 'لا توجد بيانات تخصيص لهذا الطلب.');

        $config = $this->resolveConfig($customization);

        return view('admin.orders.partials.customization', compact(
            'customization',
            'config',
            'orderId'
        ));
    }

    // ── resolveConfig() ───────────────────────────────────────────────────────

    private function resolveConfig(OrderCustomization $customization): ProductCustomization
    {
        // 1. Real product in DB
        if ($customization->product && method_exists($customization->product, 'customizationConfig')) {
            return $customization->product->customizationConfig();
        }

        // 2. Demo order — infer garment type from selected zones
        $garmentType = null;
        $zones = $customization->selected_zones ?? [];

        if (! empty(array_intersect($zones, ['1', '2', '4', '5', '6']))) {
            $garmentType = 'graduation_robe';
        } elseif (! empty(array_intersect($zones, ['D1', 'D2', 'D3', 'F']))) {
            $garmentType = 'hoodie';
        } elseif (! empty(array_intersect($zones, ['A', 'B', 'C', 'D', 'E1', 'E2', 'E3', 'F1', 'F2', 'F3', 'G', 'H']))) {
            $garmentType = 'varsity_jacket';
        } else {
            $garmentType = 'varsity_jacket';
        }

        if (isset(self::GARMENT_CONFIGS[$garmentType])) {
            return new ProductCustomization(self::GARMENT_CONFIGS[$garmentType]);
        }

        return new ProductCustomization([]);
    }

    // ── garmentLabel() helper for views ──────────────────────────────────────

    public static function garmentLabel(string $type): string
    {
        return self::GARMENT_LABELS[$type] ?? $type;
    }
}