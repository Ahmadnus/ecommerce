<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $columnsToDrop = [];

            // Drop text/color/type fields — only if they exist (safe for partial migrations)
            foreach ([
                'banner_title',
                'banner_subtitle',
                'banner_button_text',
                'banner_button_url',
                'banner_background_color',
                'banner_text_color',
                'banner_type',
            ] as $col) {
                if (Schema::hasColumn('categories', $col)) {
                    $columnsToDrop[] = $col;
                }
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }

            // Ensure banner_is_active exists (add if missing)
            if (!Schema::hasColumn('categories', 'banner_is_active')) {
                $table->boolean('banner_is_active')->default(false)->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('banner_title')->nullable();
            $table->string('banner_subtitle')->nullable();
            $table->string('banner_button_text')->nullable();
            $table->string('banner_button_url')->nullable();
            $table->string('banner_background_color', 20)->default('#0ea5e9');
            $table->string('banner_text_color', 20)->default('#ffffff');
            $table->string('banner_type', 20)->default('image');
        });
    }
};