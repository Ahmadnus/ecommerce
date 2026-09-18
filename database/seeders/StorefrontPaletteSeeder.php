<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * StorefrontPaletteSeeder — writes a complete storefront palette in one go.
 *
 * Everything here is an ordinary Setting row, i.e. exactly what the admin
 * writes from /admin/settings. The seeder just saves a tenant the trouble of
 * filling in twenty fields by hand when they want a known-good starting look.
 *
 * The default palette below is the warm orange scheme. Swap the values (or
 * edit them later in the admin) to rebrand the whole storefront — no markup
 * or CSS changes are involved.
 *
 *     php artisan db:seed --class=StorefrontPaletteSeeder
 */
class StorefrontPaletteSeeder extends Seeder
{
    private const PALETTE = [
        // ── Brand ──────────────────────────────────────────────────────────
        'primary_color'   => '#e4632a',  // header, CTAs, active states
        'bg_color'        => '#f6f6f6',  // page background
        'nav_bg_color'    => '#e4632a',  // the header is solid brand colour
        'card_bg_color'   => '#ffffff',
        'footer_bg_color' => '#2a2a2a',

        // ── Storefront tokens ──────────────────────────────────────────────
        'accent_color'      => '#f55157',  // sale prices, destructive actions
        'badge_bg_color'    => '#f55157',
        'border_color'      => '#f0f0f0',
        'subtle_bg_color'   => '#f6f6f6',  // card add-to-cart rest state
        'footer_bottom_bg'  => 'transparent',  // copyright strip matches the footer

        // Square corners, flat cards.
        'radius_card'       => '0px',
        'radius_button'     => '0px',
        'radius_input'      => '4px',
        'radius_badge'      => '999px',
        'shadow_card'       => 'none',
        'shadow_card_hover' => '0 0 5px rgba(0,0,0,.20)',

        'card_image_fit'       => 'contain',
        'card_text_align'      => 'center',   // centred title + price
        'card_show_wishlist'   => 'off',      // reference card has no heart
        'card_show_badges'     => 'off',      // …and no discount pill
        'product_open_mode'    => 'modal',    // tapping a card opens the popup
        'header_brand_mode'    => 'auto',
        'card_image_height'    => '16.3rem',
        'card_image_height_sm' => '11rem',
        'card_border_width'    => '1px',
        'header_height'        => '72px',
        'logo_size'            => '38px',
        'container_max_width'  => '1280px',
        'section_gap'          => '2rem',

        // ── Text ───────────────────────────────────────────────────────────
        'navbar_text_color'        => '#ffffff',  // white on the orange header
        'body_text_color'          => '#4a4a4a',
        'heading_text_color'       => '#2a2a2a',
        'muted_text_color'         => '#aaaaaa',
        'card_text_color'          => '#6e5a5a',
        'product_title_text_color' => '#6e5a5a',
        'price_text_color'         => '#f55157',  // the sale price is red
        'button_text_color'        => '#ffffff',
        'badge_text_color'         => '#ffffff',
        'footer_text_color'        => '#aaaaaa',
        'input_text_color'         => '#2a2a2a',
    ];

    public function run(): void
    {
        foreach (self::PALETTE as $key => $value) {
            Setting::set($key, $value);
        }

        $this->command?->info('Storefront palette applied (' . count(self::PALETTE) . ' settings).');
    }
}
