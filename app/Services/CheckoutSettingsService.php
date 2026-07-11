<?php

namespace App\Services;

/**
 * CheckoutSettingsService — business logic for the admin checkout-behaviour
 * settings page (currently: guest_checkout_enabled, stored via the OTP
 * settings helpers). Never returns views/redirects.
 */
class CheckoutSettingsService
{
    public function isGuestCheckoutEnabled(): bool
    {
        return get_otp_setting('guest_checkout_enabled', '0') == '1';
    }

    /**
     * Persist the flag. Returns the normalized stored value ('1' or '0').
     */
    public function setGuestCheckoutEnabled(?string $input): string
    {
        $value = $input === '1' ? '1' : '0';

        set_otp_setting('guest_checkout_enabled', $value);

        return $value;
    }
}
