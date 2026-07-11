<?php

namespace App\Services;

use App\Helpers\TypographySettingsHelper;
use App\Models\Setting;

/**
 * TypographySettingsService — business logic for the admin typography
 * settings page (font sizes + text colors stored in the settings table).
 * Never returns views/redirects.
 */
class TypographySettingsService
{
    /**
     * All data the settings page needs.
     */
    public function getIndexData(): array
    {
        $current     = TypographySettingsHelper::all();
        $fontKeys    = TypographySettingsHelper::fontSizeKeys();
        $colorKeys   = TypographySettingsHelper::colorKeys();
        $fontLabels  = $this->fontLabels();
        $colorLabels = $this->colorLabels();

        return compact('current', 'fontKeys', 'colorKeys', 'fontLabels', 'colorLabels');
    }

    public function fontKeys(): array
    {
        return array_keys(TypographySettingsHelper::fontSizeKeys());
    }

    public function colorKeys(): array
    {
        return array_keys(TypographySettingsHelper::colorKeys());
    }

    /**
     * Persist submitted typography values (skips null/empty inputs).
     * $input: key => value for all font + color keys.
     */
    public function saveSettings(array $input): void
    {
        // Save each setting — Setting::set() uses updateOrCreate internally
        foreach (array_merge($this->fontKeys(), $this->colorKeys()) as $key) {
            $value = $input[$key] ?? null;
            if ($value !== null && $value !== '') {
                Setting::set($key, trim($value));
            }
        }
    }

    public function fontLabels(): array
    {
        return [
            'base_font_size'          => 'Base / Body Font Size',
            'navbar_font_size'        => 'Navbar Font Size',
            'card_font_size'          => 'Card Text Font Size',
            'heading_font_size'       => 'Heading (H1 / H2) Font Size',
            'subheading_font_size'    => 'Sub-heading (H3 / H4) Font Size',
            'footer_font_size'        => 'Footer Font Size',
            'button_font_size'        => 'Button Font Size',
            'product_title_font_size' => 'Product Card Title Font Size',
            'product_price_font_size' => 'Product Price Font Size',
        ];
    }

    public function colorLabels(): array
    {
        return [
            'body_text_color'                => 'Body / Default Text Color',
            'heading_text_color'             => 'Heading Text Color',
            'muted_text_color'               => 'Muted / Secondary Text Color',
            'navbar_text_color'              => 'Navbar Text Color',
            'card_text_color'                => 'Card Text Color',
            'footer_text_color'              => 'Footer Text Color',
            'button_text_color'              => 'Button Text Color',
            'badge_text_color'               => 'Badge Text Color',
            'price_text_color'               => 'Price Text Color',
            'input_text_color'               => 'Input / Form Text Color',
            'product_title_text_color'       => 'Product Title Text Color',
            'product_description_text_color' => 'Product Description Text Color',
        ];
    }
}
