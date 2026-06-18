<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add `size` column to order_customizations.
 *
 * Run:  php artisan migrate
 * File: database/migrations/2024_01_01_000004_add_size_to_order_customizations_table.php
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_customizations', function (Blueprint $table) {
            // Nullable so existing records are unaffected
            $table->string('size', 10)->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('order_customizations', function (Blueprint $table) {
            $table->dropColumn('size');
        });
    }
};