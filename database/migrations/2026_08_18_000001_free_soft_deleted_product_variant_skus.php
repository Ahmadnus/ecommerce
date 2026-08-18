<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `product_variants.sku` was globally unique while the model soft-deletes,
     * so a deleted variant reserved its SKU forever and re-using it blew up
     * with a 1062 integrity constraint violation.
     *
     * A composite unique(['sku', 'deleted_at']) does NOT work here: MySQL and
     * MariaDB treat NULLs as distinct in a unique index, so two live rows
     * (sku, NULL) would not collide and live SKUs would stop being unique.
     *
     * Instead, index a generated column that holds the SKU only while the row
     * is live and NULL once it is soft-deleted. Live SKUs stay unique; deleted
     * ones drop out of the index and free the value for re-use.
     */
    public function up(): void
    {
        DB::statement(
            'ALTER TABLE product_variants
                 ADD COLUMN active_sku VARCHAR(255)
                 GENERATED ALWAYS AS (IF(deleted_at IS NULL, sku, NULL)) STORED'
        );

        Schema::table('product_variants', function ($table) {
            $table->dropUnique('product_variants_sku_unique');
            $table->unique('active_sku', 'product_variants_active_sku_unique');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function ($table) {
            $table->dropUnique('product_variants_active_sku_unique');
            $table->unique('sku', 'product_variants_sku_unique');
        });

        DB::statement('ALTER TABLE product_variants DROP COLUMN active_sku');
    }
};
