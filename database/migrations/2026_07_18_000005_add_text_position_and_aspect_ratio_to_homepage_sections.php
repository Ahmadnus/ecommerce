<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Block-builder layout controls for media sections:
 *
 *  - text_position: where the title/paragraph/CTA sit relative to the media
 *      'overlay_center' | 'overlay_left' | 'overlay_right' | 'below_image'
 *  - aspect_ratio: the media frame shape
 *      'landscape' (wide banner) | 'portrait' (tall luxury) | 'square' | 'full'
 *      ('full' = edge-to-edge full-screen — preserves the existing hero look)
 *
 * button_text / button_url already exist on this table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->string('text_position')->default('overlay_center')->after('product_source');
            $table->string('aspect_ratio')->default('landscape')->after('text_position');
        });

        // Backfill so every existing section keeps rendering EXACTLY as it
        // does today under the new layout engine:
        //  - hero_banner: full-screen media with centered overlay
        //  - banner (stacked legacy): full-screen media with text below
        //  - custom_image: wide standalone banner, any text below
        DB::table('homepage_sections')->where('section_type', 'hero_banner')
            ->update(['text_position' => 'overlay_center', 'aspect_ratio' => 'full']);

        DB::table('homepage_sections')->where('section_type', 'banner')
            ->update(['text_position' => 'below_image', 'aspect_ratio' => 'full']);

        DB::table('homepage_sections')->where('section_type', 'custom_image')
            ->update(['text_position' => 'below_image', 'aspect_ratio' => 'landscape']);
    }

    public function down(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->dropColumn(['text_position', 'aspect_ratio']);
        });
    }
};
