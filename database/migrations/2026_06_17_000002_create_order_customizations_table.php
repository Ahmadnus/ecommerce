<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Stores one customization record per order line.
 *
 * Designed to be order-agnostic: it references order_id but does not
 * enforce a FK here so it works regardless of whether your orders table
 * uses integer or UUID primary keys.  Add the FK constraint in your own
 * migration if you prefer strict referential integrity.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_customizations', function (Blueprint $table) {
            $table->id();

            // ── References ───────────────────────────────────────────────────
            $table->unsignedBigInteger('order_id')->index();        // FK to orders
            $table->unsignedBigInteger('order_item_id')->nullable()->index(); // FK to order_items if you have them
            $table->unsignedBigInteger('product_id')->index();      // denormalised for easy admin display

            // ── Color choices ────────────────────────────────────────────────
            /*
             * Stored as JSON map matching the keys in customization_config.
             * Example: { "body": "#141414", "sleeve": "#f3f3f1", "rib": "#141414", "stripe": "#ffffff" }
             */
            $table->json('colors')->nullable();

            // ── Per-zone text ────────────────────────────────────────────────
            /*
             * Keyed by zone key.
             * Example: { "A": "SMITH", "B": "23", "G": "CLASS OF 2024" }
             */
            $table->json('texts')->nullable();

            // ── Selected zones ───────────────────────────────────────────────
            /*
             * Array of zone keys the customer actually activated.
             * Example: ["A", "B", "G"]
             * Phase 2 will iterate this to composite only activated zones.
             */
            $table->json('selected_zones')->nullable();

            // ── Customer notes ───────────────────────────────────────────────
            $table->text('notes')->nullable();

            // ── Processing status ────────────────────────────────────────────
            /*
             * pending   – saved, not yet processed
             * processing– Phase 2 compositing in progress
             * ready     – final render available
             * error     – compositing failed
             */
            $table->enum('status', ['pending', 'processing', 'ready', 'error'])
                  ->default('pending');

            // ── Phase 2 hook ──────────────────────────────────────────────────
            // Path to the final composited preview image; null until Phase 2 renders it
            $table->string('rendered_preview_path')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_customizations');
    }
};
