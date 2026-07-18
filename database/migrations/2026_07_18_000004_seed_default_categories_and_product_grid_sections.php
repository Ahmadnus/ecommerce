<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Content-visibility restore. When the hardcoded homepage grids were replaced
 * by the modular builder, stores whose homepage_sections table contained no
 * 'categories_grid' or 'product_grid' rows lost their category tiles and
 * product listings entirely — the loop had nothing of those types to render.
 *
 * Seed one of each (only when absent) so every store renders its categories
 * and products out of the box; the admin can then re-order or delete them
 * freely from /admin/homepage-sections like any other section.
 */
return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        if (! DB::table('homepage_sections')->where('section_type', 'categories_grid')->exists()) {
            DB::table('homepage_sections')->insert([
                'title'        => 'تسوق حسب الفئة',
                'section_type' => 'categories_grid',
                'media_type'   => 'none',
                'is_active'    => 1,
                'sort_order'   => 2,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }

        if (! DB::table('homepage_sections')->where('section_type', 'product_grid')->exists()) {
            DB::table('homepage_sections')->insert([
                'title'          => 'أحدث المنتجات',
                'section_type'   => 'product_grid',
                'product_source' => 'latest_products',
                'media_type'     => 'none',
                'is_active'      => 1,
                'sort_order'     => 3,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }
    }

    public function down(): void
    {
        // Intentionally left empty: these are content rows the admin now owns.
    }
};
