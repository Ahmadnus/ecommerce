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
    Schema::create('footer_links', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // اسم الرابط الذي سيظهر للمستخدم (مثلاً: اتصل بنا)
        $table->string('url');  // الرابط نفسه (مثلاً: /contact أو رابط خارجي)
        $table->integer('sort_order')->default(0); // لترتيب الروابط في الفوتر
        $table->boolean('is_active')->default(true); // لإخفاء أو إظهار الرابط بسهولة
        $table->timestamps();
    });
 }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_links');
    }
};
