<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Layout-regression fix. The previous migration blanket-renamed legacy
 * 'banner' sections to 'hero_banner', which changed their composition from
 * "tall media, then text/CTA stacked BELOW" to "text/CTA overlaid ON TOP of
 * the media" — scrambling every pre-existing section.
 *
 * Restore those rows to the legacy 'banner' type, whose renderer keeps the
 * original stacked (media above, text below) composition. 'hero_banner'
 * remains available for NEW sections where the overlay is intentional.
 *
 * Scope guard: only rows created before this refactor (i.e. rows that were
 * renamed by the previous migration) are affected. Any hero_banner created
 * through the new admin UI after the rename has media_type freely chosen by
 * the admin under the new semantics — we can't distinguish those by data
 * alone, so we use created_at against this migration batch's deploy date.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('homepage_sections')
            ->where('section_type', 'hero_banner')
            ->where('created_at', '<', '2026-07-18 00:00:00')
            ->update(['section_type' => 'banner']);
    }

    public function down(): void
    {
        DB::table('homepage_sections')
            ->where('section_type', 'banner')
            ->update(['section_type' => 'hero_banner']);
    }
};
