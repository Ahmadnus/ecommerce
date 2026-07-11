<?php

namespace App\Services;

use App\Models\OrderCustomization;
use App\Models\Setting;

/**
 * ╔══════════════════════════════════════════════════════════════════════════╗
 * ║  CustomizationPricingService                                           ║
 * ║                                                                        ║
 * ║  Single source of truth for computing the JOD price of a garment       ║
 * ║  customization (base garment price + per-image fee + per-text fee).   ║
 * ║                                                                        ║
 * ║  ── Settings storage ─────────────────────────────────────────────────║
 * ║  Uses your EXISTING App\Models\Setting (settings table, key/value)    ║
 * ║  via Setting::get($key, $default) and Setting::set($key, $value).     ║
 * ║  This is the SAME model/table your TypographySettingsHelper uses for  ║
 * ║  fonts and colors — pricing settings live alongside them as plain     ║
 * ║  rows, no new table, no schema change.                                ║
 * ║                                                                        ║
 * ║  NOTE: get_otp_setting()/set_otp_setting() are NOT used here — those  ║
 * ║  point at a different model (OtpSetting / otpsettings table) used     ║
 * ║  only for OTP/SMS config. Mixing the two would silently write pricing ║
 * ║  values into the wrong table.                                         ║
 * ║                                                                        ║
 * ║  ── Why this exists ──────────────────────────────────────────────────║
 * ║  The price must be calculated IDENTICALLY in three places:            ║
 * ║    1. When added to the cart (CustomizationController::store())       ║
 * ║    2. When the cart is displayed (cart/index.blade.php)                ║
 * ║    3. When the order is finally placed (CheckoutController)            ║
 * ║  Centralizing the math here means all three always agree — no drift.  ║
 * ║                                                                        ║
 * ║  ── Currency handling ────────────────────────────────────────────────║
 * ║  Every method on this service returns a value in JOD (the base        ║
 * ║  currency). Currency conversion happens ONLY at the display layer     ║
 * ║  via CurrencyHelper::convert() / ::format() — exactly like every      ║
 * ║  other price in the app. This service NEVER touches the active       ║
 * ║  currency. Keep it pure JOD in, JOD out.                              ║
 * ╚══════════════════════════════════════════════════════════════════════════╝
 */
class CustomizationPricingService
{
    // ── Setting keys (stored in the `settings` table via App\Models\Setting) ──
    public const KEY_TSHIRT_BASE_PRICE = 'customization_tshirt_base_price';
    public const KEY_PRICE_PER_IMAGE   = 'customization_price_per_image';
    public const KEY_PRICE_PER_TEXT    = 'customization_price_per_text';

    /**
     * Get the base price (JOD) for a garment type.
     * "tshirt" is admin-editable via Setting; other garment types fall back
     * to config('customization_pricing.base_prices.*') for now — extend
     * with their own Setting keys later the same way tshirt is done here.
     */
    public function basePrice(string $garmentType): float
    {
        if ($garmentType === 'tshirt') {
            return (float) Setting::get(
                self::KEY_TSHIRT_BASE_PRICE,
                config('customization_pricing.base_prices.tshirt', 10.00)
            );
        }

        return (float) config(
            "customization_pricing.base_prices.{$garmentType}",
            10.00
        );
    }

    /**
     * Price charged per uploaded image/logo (JOD). Admin-editable.
     */
    public function pricePerImage(): float
    {
        return (float) Setting::get(
            self::KEY_PRICE_PER_IMAGE,
            config('customization_pricing.fees.per_image', 2.00)
        );
    }

    /**
     * Price charged per filled text zone (JOD). Admin-editable.
     */
    public function pricePerText(): float
    {
        return (float) Setting::get(
            self::KEY_PRICE_PER_TEXT,
            config('customization_pricing.fees.per_text', 1.00)
        );
    }

    /**
     * Count how many zones have an uploaded image.
     */
    public function imageCount(OrderCustomization $customization): int
    {
        // Use already-loaded relation if present, to avoid an extra query
        // when called from a page that preloaded uploads.
        if ($customization->relationLoaded('uploads')) {
            return $customization->uploads->count();
        }

        return $customization->uploads()->count();
    }

    /**
     * Count how many zones have non-empty text.
     * texts is stored as ['zoneKey' => ['value' => string, 'color'=>..., ...]]
     */
    public function textCount(OrderCustomization $customization): int
    {
        $texts = $customization->texts ?? [];

        return count(array_filter(
            $texts,
            fn ($entry) => is_array($entry)
                ? trim($entry['value'] ?? '') !== ''
                : trim((string) $entry) !== ''
        ));
    }

    /**
     * Full price breakdown for a customization, in JOD.
     *
     * Returns:
     *   [
     *     'garment_type' => 'tshirt',
     *     'base_price'   => 10.00,
     *     'image_count'  => 2,
     *     'image_fee'    => 2.00,
     *     'image_total'  => 4.00,
     *     'text_count'   => 1,
     *     'text_fee'     => 1.00,
     *     'text_total'   => 1.00,
     *     'total'        => 15.00,
     *   ]
     */
    public function breakdown(OrderCustomization $customization): array
    {
        $garmentType = $customization->garment_type ?? 'tshirt';

        $basePrice  = $this->basePrice($garmentType);
        $imageCount = $this->imageCount($customization);
        $textCount  = $this->textCount($customization);
        $imageFee   = $this->pricePerImage();
        $textFee    = $this->pricePerText();

        $imageTotal = round($imageCount * $imageFee, 2);
        $textTotal  = round($textCount * $textFee, 2);
        $total      = round($basePrice + $imageTotal + $textTotal, 2);

        return [
            'garment_type' => $garmentType,
            'base_price'   => $basePrice,
            'image_count'  => $imageCount,
            'image_fee'    => $imageFee,
            'image_total'  => $imageTotal,
            'text_count'   => $textCount,
            'text_fee'     => $textFee,
            'text_total'   => $textTotal,
            'total'        => $total,
        ];
    }

    // ── Admin settings page (used by Admin\CustomizationPricingSettingsController) ──

    public const SETTINGS = [
        self::KEY_TSHIRT_BASE_PRICE => [
            'label'   => 'سعر التيشيرت الأساسي',
            'default' => '10.00',
        ],
        self::KEY_PRICE_PER_IMAGE => [
            'label'   => 'رسوم كل صورة/شعار مُضاف',
            'default' => '2.00',
        ],
        self::KEY_PRICE_PER_TEXT => [
            'label'   => 'رسوم كل نص مُضاف',
            'default' => '1.00',
        ],
    ];

    /**
     * Current stored values (falling back to defaults) for the admin page.
     */
    public function getSettingsValues(): array
    {
        $values = [];

        foreach (self::SETTINGS as $key => $meta) {
            $values[$key] = Setting::get($key, $meta['default']);
        }

        return $values;
    }

    /**
     * Persist validated pricing values, normalized to 2 decimal places.
     */
    public function saveSettings(array $validated): void
    {
        foreach ($validated as $key => $value) {
            Setting::set($key, number_format((float) $value, 2, '.', ''));
        }
    }

    /**
     * Convenience: just the final JOD total for a customization.
     */
    public function totalFor(OrderCustomization $customization): float
    {
        return $this->breakdown($customization)['total'];
    }
}