<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderCustomization;
use App\Services\OrderCustomizationAdminService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderCustomizationController extends Controller
{
    public function __construct(
        private readonly OrderCustomizationAdminService $customizations,
    ) {}

    // ── index() ────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $customizations = $this->customizations->getFilteredCustomizations(
            $request->only(['status', 'search'])
        );

        return view('admin.order-customizations.index', compact('customizations'));
    }

    // ── show() ─────────────────────────────────────────────────────────────────

    public function show(OrderCustomization $customization): View
    {
        $customization->load(['product', 'uploads']);

        $config = $this->customizations->resolveConfig($customization);

        return view('admin.order-customizations.show', compact('customization', 'config'));
    }

    // ── embedded() — inline panel inside order detail view ────────────────────

    public function embedded(int $orderId): View
    {
        $customization = $this->customizations->findForOrder($orderId);

        abort_if(! $customization, 404, 'لا توجد بيانات تخصيص لهذا الطلب.');

        $config = $this->customizations->resolveConfig($customization);

        return view('admin.orders.partials.customization', compact(
            'customization',
            'config',
            'orderId'
        ));
    }

    // ── garmentLabel() helper for views ──────────────────────────────────────

    public static function garmentLabel(string $type): string
    {
        return OrderCustomizationAdminService::garmentLabel($type);
    }
}
