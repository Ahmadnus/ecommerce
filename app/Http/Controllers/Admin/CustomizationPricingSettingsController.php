<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CustomizationPricingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Admin controller for the customization pricing settings page.
 *
 * Manages 3 rows in the EXISTING `settings` table (App\Models\Setting):
 *   - customization_tshirt_base_price
 *   - customization_price_per_image
 *   - customization_price_per_text
 *
 * All storage logic lives in CustomizationPricingService (the same single
 * source of truth used for cart/checkout pricing). All values are stored
 * and treated as JOD (the base currency); conversion for display happens
 * elsewhere (CurrencyHelper).
 */
class CustomizationPricingSettingsController extends Controller
{
    public function __construct(
        private readonly CustomizationPricingService $pricing,
    ) {}

    public function edit(): View
    {
        return view('admin.settings.customization-pricing', [
            'settings' => CustomizationPricingService::SETTINGS,
            'values'   => $this->pricing->getSettingsValues(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            CustomizationPricingService::KEY_TSHIRT_BASE_PRICE => ['required', 'numeric', 'min:0', 'max:9999.99'],
            CustomizationPricingService::KEY_PRICE_PER_IMAGE   => ['required', 'numeric', 'min:0', 'max:999.99'],
            CustomizationPricingService::KEY_PRICE_PER_TEXT    => ['required', 'numeric', 'min:0', 'max:999.99'],
        ], [
            'required' => 'هذا الحقل مطلوب.',
            'numeric'  => 'يجب أن تكون القيمة رقماً.',
            'min'      => 'القيمة لا يمكن أن تكون سالبة.',
        ]);

        $this->pricing->saveSettings($validated);

        return redirect()
            ->route('admin.settings.customization-pricing.edit')
            ->with('success', 'تم حفظ أسعار التخصيص بنجاح.');
    }
}
