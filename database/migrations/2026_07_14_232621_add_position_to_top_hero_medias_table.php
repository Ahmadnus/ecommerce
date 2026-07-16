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
        Schema::table('top_hero_medias', function (Blueprint $table) {
            $table->enum('position', ['top', 'middle', 'bottom'])->default('top')->after('type');
            $table->index(['position', 'is_active', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('top_hero_medias', function (Blueprint $table) {
            $table->dropIndex(['position', 'is_active', 'sort_order']);
            $table->dropColumn('position');
        });
    }
};
