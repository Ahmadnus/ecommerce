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
        Schema::create('social_links', function (Blueprint $table) {
            $table->id();
            $table->string('platform_name'); // اسم المنصة: فيسبوك، انستغرام...
            $table->string('url')->nullable(); // الرابط العادي
            
            // --- الإضافات الجديدة للزر العائم ---
            $table->string('whatsapp_number')->nullable(); // رقم الواتساب
            $table->boolean('is_floating')->default(false); // هل يظهر كزر عائم؟
            // ----------------------------------

            $table->text('icon_svg')->nullable(); // كود الـ SVG للأيقونة
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_links');
    }
};
