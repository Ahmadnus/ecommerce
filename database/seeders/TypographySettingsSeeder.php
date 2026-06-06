<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class TypographySettingsSeeder extends Seeder
{
    private array $defaults = [
        // ── Font sizes ──────────────────────────────────────────────────────
        'base_font_size'              => '16px',
        'navbar_font_size'            => '14px',
        'card_font_size'              => '13px',
        'heading_font_size'           => '32px',
        'subheading_font_size'        => '20px',
        'footer_font_size'            => '14px',
        'button_font_size'            => '14px',
        'product_title_font_size'     => '13px',
        'product_price_font_size'     => '15px',

        // ── Text colors ─────────────────────────────────────────────────────
        'body_text_color'                => '#111827',
        'heading_text_color'             => '#0f172a',
        'muted_text_color'               => '#9ca3af',
        'navbar_text_color'              => '#374151',
        'card_text_color'                => '#374151',
        'footer_text_color'              => '#9ca3af',
        'button_text_color'              => '#ffffff',
        'badge_text_color'               => '#ffffff',
        'price_text_color'               => '#dc2626',
        'input_text_color'               => '#111827',
        'product_title_text_color'       => '#111827',
        'product_description_text_color' => '#6b7280',
    ];

    public function run(): void
    {
        foreach ($this->defaults as $key => $value) {
            // updateOrCreate — never overwrites values already saved by the admin
            Setting::updateOrCreate(
                ['key'   => $key],
                ['value' => $value]
            );
        }

        $this->command->info('✓ Typography settings seeded (' . count($this->defaults) . ' keys)');
    }
}