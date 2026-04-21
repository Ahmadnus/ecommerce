<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
     * Adds calling_code to both tables:
     *
     * countries.calling_code  — the dialling prefix for the whole country
     *                           (e.g. 963 for Syria, 966 for Saudi Arabia).
     *                           Stored as a nullable string so it can hold
     *                           codes like "1" (US/CA) or "44" (UK) without
     *                           leading-zero confusion.
     *
     * zones.calling_code      — optional override at the zone/state level.
     *                           Usually the same as the parent country code,
     *                           but some countries have region-specific codes.
     *                           Also nullable so existing zones are unaffected.
     */
   public function up(): void
    {
        // لاحظ هنا استخدمنا create وليس table
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->string('code', 3)->unique(); // مثل SY, SA, AE
            $table->string('calling_code', 10)->nullable(); // الحقل الجديد الذي سبب المشكلة
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};