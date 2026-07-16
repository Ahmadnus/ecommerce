<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->string('text_color')->nullable()->after('button_url');
            $table->string('button_bg_color')->nullable()->after('text_color');
            $table->string('button_text_color')->nullable()->after('button_bg_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->dropColumn(['text_color', 'button_bg_color', 'button_text_color']);
        });
    }
};
