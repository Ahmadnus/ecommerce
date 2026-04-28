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
    Schema::table('hero_banners', function (Blueprint $table) {
        $table->string('background_color')->after('button_url');
        $table->string('text_color')->after('background_color');
    });
}

public function down(): void
{
    Schema::table('hero_banners', function (Blueprint $table) {
        $table->dropColumn(['background_color', 'text_color']);
    });
}

    /**
     * Reverse the migrations.
     */
   
};
