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
    Schema::create('announcements', function (Blueprint $table) {
        $table->id();
        $table->string('content'); // النص (مثلاً: شحن مجاني)
        $table->integer('sort_order')->default(0); // الترتيب
        $table->boolean('is_active')->default(true); // تفعيل أو تعطيل
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
