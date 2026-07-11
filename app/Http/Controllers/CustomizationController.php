<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomizationRequest;
use App\Services\CustomizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

/**
 * Handles the customer-facing customization UI.
 *
 * Thin HTTP layer: validation (via StoreCustomizationRequest), calling
 * CustomizationService, and returning views/redirects/JSON. All garment
 * resolution, persistence, pricing, and cart logic lives in the service.
 */
class CustomizationController extends Controller
{
    public function __construct(
        private readonly CustomizationService $customization,
    ) {}

    // ─────────────────────────────────────────────────────────────────────────
    // show()
    // ─────────────────────────────────────────────────────────────────────────

    public function show(string $garment)
    {
        [$product, $config, $isDemo] = $this->customization->resolveGarment($garment);

        return view('customize.show', [
            'product'  => $product,
            'config'   => $config,
            'zones'    => $config->zones(),
            'colors'   => $config->availableColors(),
            'defaults' => $config->defaultColors(),
            'isDemo'   => $isDemo,
            'garmentSlug' => $this->customization->toSlug($garment),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // store()
    // ─────────────────────────────────────────────────────────────────────────

    public function store(StoreCustomizationRequest $request, string $garment): JsonResponse|RedirectResponse
    {
        [$product, $config, $isDemo] = $this->customization->resolveGarment($garment);

        try {
            [$customization, $priceBreakdown] = $this->customization->store(
                input: $request->only(['colors', 'texts', 'text_styles', 'size', 'selected_zones', 'design_snapshot', 'notes']),
                images: $request->hasFile('images') ? $request->file('images') : [],
                product: $product,
                config: $config,
                isDemo: $isDemo,
            );
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء حفظ التخصيص. يرجى المحاولة مرة أخرى.',
            ], 500);
        }

        session(['pending_customization_id' => $customization->id]);

        // ── AJAX callers (e.g. if the designer still uses fetch()) get JSON
        //    with a redirect URL; normal form submits get a real redirect. ──────
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'          => true,
                'message'          => 'تم حفظ التصميم وإضافته إلى السلة بنجاح!',
                'customization_id' => $customization->id,
                'price_breakdown'  => $priceBreakdown,
                'redirect'         => route('cart.index'),
            ]);
        }

        return redirect()
            ->route('cart.index')
            ->with('success', 'تم حفظ تصميمك وإضافته إلى السلة بنجاح!');
    }

    // ── Attach pending customization to a real order (call from OrderController) ─
    public static function attachPendingCustomization(int $orderId): void
    {
        app(CustomizationService::class)->attachPendingCustomization($orderId);
    }
}
