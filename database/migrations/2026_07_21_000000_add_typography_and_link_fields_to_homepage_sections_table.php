<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            // title_font_family / paragraph_font_family already exist on this
            // table (added out-of-band, ahead of this migration) — only add
            // what's actually missing so this stays safe to run anywhere.
            if (! Schema::hasColumn('homepage_sections', 'title_font_family')) {
                $table->string('title_font_family')->nullable()->default('default')->after('section_title_accent_color');
            }
            if (! Schema::hasColumn('homepage_sections', 'paragraph_font_family')) {
                $table->string('paragraph_font_family')->nullable()->default('default')->after('text_color');
            }

            // Small underlined link — separate content + styling from the big CTA button,
            // so a section can show a subtle text link instead of (or alongside) a button.
            if (! Schema::hasColumn('homepage_sections', 'link_text')) {
                $table->string('link_text')->nullable()->after('button_url');
            }
            if (! Schema::hasColumn('homepage_sections', 'link_url')) {
                $table->string('link_url')->nullable()->after('link_text');
            }
            if (! Schema::hasColumn('homepage_sections', 'link_color')) {
                $table->string('link_color')->nullable()->after('link_url');
            }
            if (! Schema::hasColumn('homepage_sections', 'link_font_family')) {
                $table->string('link_font_family')->nullable()->default('default')->after('link_color');
            }
            if (! Schema::hasColumn('homepage_sections', 'link_style')) {
                $table->string('link_style')->nullable()->default('underline')->after('link_font_family');
            }

            // Toggle: for media-only sections (e.g. extra blocks below products),
            // let the admin choose to hide the text/caption block entirely.
            if (! Schema::hasColumn('homepage_sections', 'show_text_below_media')) {
                $table->boolean('show_text_below_media')->default(true)->after('text_alignment');
            }
        });
    }

    public function down(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            foreach (['link_text', 'link_url', 'link_color', 'link_font_family', 'link_style', 'show_text_below_media'] as $column) {
                if (Schema::hasColumn('homepage_sections', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
