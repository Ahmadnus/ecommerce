<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Each row is one uploaded image for one zone in one order customization.
 * Keeping uploads in a separate table (rather than embedding paths in the
 * JSON column) makes it easy to:
 *   - clean up orphaned files
 *   - attach multiple images per zone in the future
 *   - run Phase 2 compositing jobs that iterate over uploads
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customization_uploads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_customization_id')
                  ->constrained('order_customizations')
                  ->cascadeOnDelete();

            // The zone key this image belongs to (e.g. "A", "G", "E1")
            $table->string('zone_key', 8);

            // ── File metadata ─────────────────────────────────────────────────
            // Relative path under storage/app/public/
            // e.g. "customizations/42/A_1700000000.png"
            $table->string('path');

            // Original filename as uploaded by the customer
            $table->string('original_filename')->nullable();

            // MIME type stored for Phase 2 validation
            $table->string('mime_type', 64)->nullable();

            // File size in bytes for storage quota tracking
            $table->unsignedInteger('size_bytes')->nullable();

            // ── Phase 2 hooks ──────────────────────────────────────────────────
            // Width × height of the uploaded image (pixels); filled in by Phase 2
            $table->unsignedSmallInteger('width_px')->nullable();
            $table->unsignedSmallInteger('height_px')->nullable();

            // Sort order if a zone allows multiple images
            $table->unsignedTinyInteger('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customization_uploads ');
        
    }
};
