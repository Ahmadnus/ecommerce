<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CheckoutSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CheckoutSettingsController
 * ─────────────────────────────────────────────────────────────────────────────
 * Admin page for checkout behaviour settings.
 * Currently manages: guest_checkout_enabled
 *
 * Routes (add inside your admin group):
 *   GET  /admin/settings/checkout        → show
 *   POST /admin/settings/checkout        → update
 */
class CheckoutSettingsController extends Controller
{
    public function __construct(
        private readonly CheckoutSettingsService $settings,
    ) {}

    public function show(): View
    {
        $guestEnabled = $this->settings->isGuestCheckoutEnabled();

        return view('admin.settings.checkout', compact('guestEnabled'));
    }

    public function update(Request $request): RedirectResponse
    {
        $value = $this->settings->setGuestCheckoutEnabled(
            $request->input('guest_checkout_enabled')
        );

        $label = $value === '1' ? 'تم تفعيل الشراء كزائر ✓' : 'تم تعطيل الشراء كزائر';

        return redirect()
            ->route('admin.settings.checkout')
            ->with('success', $label);
    }
}
