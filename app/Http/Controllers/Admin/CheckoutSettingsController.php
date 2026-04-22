<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OtpSetting;
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
    public function show(): View
    {
        $guestEnabled = get_otp_setting('guest_checkout_enabled', '0') === '1';

        return view('admin.settings.checkout', compact('guestEnabled'));
    }

    public function update(Request $request): RedirectResponse
    {
        // Checkbox: present = '1', absent = '0'
        $value = $request->boolean('guest_checkout_enabled') ? '1' : '0';

        set_otp_setting('guest_checkout_enabled', $value);

        $label = $value === '1' ? 'تم تفعيل الشراء كزائر ✓' : 'تم تعطيل الشراء كزائر';

        return redirect()
            ->route('admin.settings.checkout')
            ->with('success', $label);
    }
}