<?php
// database/migrations/2026_05_07_000002_create_footer_company_info_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('footer_company_info', function (Blueprint $table) {
            $table->id();

            // Translatable (JSON)
            $table->json('company_name')->nullable();
            $table->json('description')->nullable();
            $table->json('location')->nullable();

            // Non-translatable
            $table->string('phone')->nullable();         // e.g. +447782281157
            $table->string('phone_country_code')->nullable(); // e.g. gb
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_company_info');
    }
};