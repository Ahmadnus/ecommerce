<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('banner_title')->nullable()->after('description');
            $table->string('banner_subtitle')->nullable()->after('banner_title');
            $table->string('banner_button_text')->nullable()->after('banner_subtitle');
            $table->string('banner_button_url')->nullable()->after('banner_button_text');
            $table->string('banner_background_color', 20)->default('#0ea5e9')->after('banner_button_url');
            $table->string('banner_text_color', 20)->default('#ffffff')->after('banner_background_color');
            $table->boolean('banner_is_active')->default(false)->after('banner_text_color');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'banner_title',
                'banner_subtitle',
                'banner_button_text',
                'banner_button_url',
                'banner_background_color',
                'banner_text_color',
                'banner_is_active',
            ]);
        });
    }
};