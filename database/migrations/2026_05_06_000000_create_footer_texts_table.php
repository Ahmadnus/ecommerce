<?php
// database/migrations/2026_05_06_000000_create_footer_texts_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
   public function up(): void
    {
        Schema::create('footer_texts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('text');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_texts');
    }

};