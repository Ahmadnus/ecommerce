<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Attribute types (Color, Size, Material …) ────────────────────────
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('type')->default('select')
                  ->comment('select | color | image');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // ── Possible values per attribute (Red, XL, 42 …) ───────────────────
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained()->cascadeOnDelete();
            $table->string('value');
            $table->string('label')->nullable()->comment('Display override');
            $table->string('color_hex', 7)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['attribute_id', 'value']);
            $table->index('attribute_id');
        });

        // ── Product Variants ─────────────────────────────────────────────────
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->decimal('price_override', 10, 2)->nullable()
                  ->comment('NULL = inherit product base_price');
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->string('variant_image')->nullable()
                  ->comment('storage/ relative path');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_id', 'is_active']);
        });

        // ── Pivot: which attribute-values compose each variant ───────────────
        Schema::create('product_variant_attribute_values', function (Blueprint $table) {
            $table->foreignId('product_variant_id')
                  ->constrained('product_variants')
                  ->cascadeOnDelete();
            $table->foreignId('attribute_value_id')
                  ->constrained('attribute_values')
                  ->cascadeOnDelete();
            $table->primary(
                ['product_variant_id', 'attribute_value_id'],
                'pvav_primary'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_attribute_values');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
    }
};