<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->string('title_font_family')->nullable()->after('font_family');
            $table->string('paragraph_font_family')->nullable()->after('title_font_family');
        });

        // Carry the old single font_family value over to the new title
        // column, since previously it only ever styled the H1 title.
        DB::table('homepage_sections')
            ->whereNotNull('font_family')
            ->update(['title_font_family' => DB::raw('font_family')]);

        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->dropColumn('font_family');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->string('font_family')->nullable()->after('text_alignment');
        });

        DB::table('homepage_sections')
            ->whereNotNull('title_font_family')
            ->update(['font_family' => DB::raw('title_font_family')]);

        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->dropColumn(['title_font_family', 'paragraph_font_family']);
        });
    }
};
