<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('hero_banners', function (Blueprint $table) {
        $table->string('layout')->default('text_image')->after('position');
    });
}

public function down()
{
    Schema::table('hero_banners', function (Blueprint $table) {
        $table->dropColumn('layout');
    });
}
};
