<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('products', function (Blueprint $table) {
    Schema::disableForeignKeyConstraints();
    $table->id();

    // ── Translatable fields → json columns ──────────────────────────
    $table->json('name');                          // was: string
    $table->json('description')->nullable();       // was: text
    $table->json('short_description')->nullable(); // was: text
    // ────────────────────────────────────────────────────────────────

    $table->string('slug')->unique();
    $table->decimal('base_price', 10, 2);
    $table->decimal('discount_price', 10, 2)->nullable();
    $table->string('sku')->unique()->nullable();
    $table->string('image')->nullable();
    $table->json('images')->nullable();
    $table->string('status')->default('active');
    $table->boolean('is_featured')->default(false);
    $table->unsignedInteger('sort_order')->default(0);
    $table->json('meta')->nullable();
    $table->timestamps();
    $table->softDeletes();

    $table->index(['status', 'is_featured']);
    $table->index('slug');
});

        // Many-to-many: product can appear in multiple categories
        Schema::create('category_product', function (Blueprint $table) {
             Schema::disableForeignKeyConstraints();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->primary(['category_id', 'product_id']);
            $table->timestamps();
        });
    }

   public function down(): void
{ Schema::disableForeignKeyConstraints();
    // تعطيل الرقابة على المفاتيح الأجنبية مؤقتاً
    Schema::disableForeignKeyConstraints();
    
    Schema::dropIfExists('products');
    
    Schema::enableForeignKeyConstraints();
}
};