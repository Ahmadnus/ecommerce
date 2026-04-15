<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Adds the 'delivery_fee' key to the settings table.
 * Safe to run even if the key already exists (uses updateOrInsert).
 * Skips if the settings table doesn't exist yet.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Only run if a settings table already exists ───────────────────
        if (!Schema::hasTable('settings')) {
            return;
        }

        // Insert default delivery fee of 3.00 JOD
        // Uses updateOrInsert so it's idempotent — safe to re-run
        DB::table('settings')->updateOrInsert(
            ['key' => 'delivery_fee'],
            [
                'key'        => 'delivery_fee',
                'value'      => '3.00',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Also seed free-shipping threshold (used by CartService)
        DB::table('settings')->updateOrInsert(
            ['key' => 'free_delivery_threshold'],
            [
                'key'        => 'free_delivery_threshold',
                'value'      => '50.00',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        DB::table('settings')
            ->whereIn('key', ['delivery_fee', 'free_delivery_threshold'])
            ->delete();
    }
};