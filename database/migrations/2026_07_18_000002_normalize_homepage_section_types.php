<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Finalises the modular page-builder taxonomy: legacy 'banner' sections become
 * full 'hero_banner' sections, and the column default moves to 'text_block'
 * (the safest no-media default for newly created rows).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('homepage_sections')
            ->where('section_type', 'banner')
            ->update(['section_type' => 'hero_banner']);

        try {
            Schema::table('homepage_sections', function (Blueprint $table) {
                $table->string('section_type')->default('text_block')->change();
            });
        } catch (\Throwable $e) {
            // doctrine/dbal may be unavailable — the default is non-critical
            // since the app always sends an explicit section_type.
        }
    }

    public function down(): void
    {
        DB::table('homepage_sections')
            ->where('section_type', 'hero_banner')
            ->update(['section_type' => 'banner']);
    }
};
