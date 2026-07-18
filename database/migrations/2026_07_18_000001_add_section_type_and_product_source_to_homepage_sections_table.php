<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Turns homepage_sections into a fully modular, sequentially-sortable page
 * builder. Each row is now typed (banner | text_block | product_grid) and,
 * for product grids, points at a product_source (latest_products,
 * best_sellers, or a category id). Rendering order across ALL types is driven
 * purely by the existing `sort_order` column — the old fixed `position` slots
 * are no longer required (kept nullable for backward compatibility).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            // Rendering type of the block. Defaults to 'banner' so any legacy
            // media/text row keeps rendering as a rich banner until re-saved.
            $table->string('section_type')->default('banner')->after('position');

            // Only meaningful when section_type = 'product_grid'. Stores
            // 'latest_products', 'best_sellers', or a numeric category id.
            $table->string('product_source')->nullable()->after('section_type');
        });

        // The strict fixed-slot `position` is no longer mandatory now that
        // sort_order alone governs the global sequence. Make it nullable so
        // pure product_grid / text_block rows don't need a slot.
        if (Schema::hasColumn('homepage_sections', 'position')) {
            try {
                DB::statement('ALTER TABLE homepage_sections MODIFY position VARCHAR(255) NULL');
            } catch (\Throwable $e) {
                // Non-MySQL grammar (e.g. sqlite in tests) — safe to ignore;
                // those drivers already treat the column as nullable-friendly.
            }
        }

        // Backfill: infer a sensible section_type for existing rows so the
        // storefront keeps rendering them correctly under the new switch.
        DB::table('homepage_sections')
            ->whereNotNull('media_path')
            ->where('media_type', '!=', 'none')
            ->update(['section_type' => 'banner']);

        DB::table('homepage_sections')
            ->where(function ($q) {
                $q->whereNull('media_path')->orWhere('media_type', 'none');
            })
            ->update(['section_type' => 'text_block']);
    }

    public function down(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->dropColumn(['section_type', 'product_source']);
        });
    }
};
