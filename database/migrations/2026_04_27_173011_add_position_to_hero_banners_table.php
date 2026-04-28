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
       $table->enum('position', [
        'top',
        'after_featured',
        'after_products'
    ])->default('top')->after('sort_order');
    });
}

public function down(): void
{
    Schema::table('hero_banners', function (Blueprint $table) {
        $table->dropColumn('position');
    });
}
};
