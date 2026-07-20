<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Block/Cube Builder expansion for homepage_sections:
 *  - video_url         : external/self-hosted video source (used when no file
 *                        is uploaded; renders the same HTML5 loop player)
 *  - background_color  : block background (hex) — pure text/CTA cubes and
 *                        below-media stacks sit on this color
 *  - padding_settings  : vertical breathing space preset per block
 *                        (none | compact | normal | spacious)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            if (! Schema::hasColumn('homepage_sections', 'video_url')) {
                $table->string('video_url', 500)->nullable()->after('media_type');
            }
            if (! Schema::hasColumn('homepage_sections', 'background_color')) {
                $table->string('background_color', 9)->nullable()->after('button_text_color');
            }
            if (! Schema::hasColumn('homepage_sections', 'padding_settings')) {
                $table->string('padding_settings', 20)->nullable()->after('background_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            foreach (['video_url', 'background_color', 'padding_settings'] as $col) {
                if (Schema::hasColumn('homepage_sections', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
