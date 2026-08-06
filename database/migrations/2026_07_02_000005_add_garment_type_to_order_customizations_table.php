<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add `garment_type` column to order_customizations.
 *
 * This is the permanent fix for the "always shows Varsity Jacket" bug.
 *
 * Root cause: garment_type was never stored — the admin had to guess it
 * from zone keys, which is ambiguous (A/B/G zones exist in both jacket
 * and hoodie), causing everything to fall through to the varsity_jacket
 * default in resolveConfig().
 *
 * After this migration, garment_type is stored on every submission and
 * read directly — no inference needed.
 *
 * Run: php artisan migrate
 * File: database/migrations/2024_01_01_000005_add_garment_type_to_order_customizations_table.php
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_customizations', function (Blueprint $table) {
            $table->string('garment_type', 30)
                  ->nullable()          // nullable so existing rows don't break
                  ->after('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('order_customizations', function (Blueprint $table) {
            $table->dropColumn('garment_type');
        });
    }
};