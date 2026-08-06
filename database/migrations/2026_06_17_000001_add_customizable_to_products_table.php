<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 1 – adds the is_customizable flag and a JSON zones config column
 * directly onto the existing products table.
 *
 * No existing columns are touched; this is a purely additive migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Quick flag so queries can filter customizable products efficiently
            $table->boolean('is_customizable')->default(false)->after('id');

            /*
             * Stores the product-level zone configuration as JSON.
             * Each product defines its own set of editable zones.
             *
             * Example structure (matches the SVG placement zone keys
             * already in your front-end HTML files):
             *
             * {
             *   "zones": [
             *     { "key": "A", "label": "Left chest",     "type": "both" },
             *     { "key": "B", "label": "Right chest",    "type": "both" },
             *     { "key": "G", "label": "Back panel",     "type": "image" },
             *     { "key": "E1","label": "Left sleeve top","type": "text"  }
             *   ],
             *   "garment_type": "varsity_jacket",   // maps to your SVG template
             *   "available_colors": {
             *     "body":   ["#141414","#1d2b53","#7a0c1f"],
             *     "sleeve": ["#f3f3f1","#e9e2cf"],
             *     "rib":    ["#141414","#c8102e","#c9a227"],
             *     "stripe": ["#ffffff","#c9a227"]
             *   }
             * }
             *
             * "type" per zone:  "text" | "image" | "both"
             * garment_type maps to a Blade partial / SVG template key.
             *
             * Phase 2 will read this config to drive the compositing pipeline.
             */
            $table->json('customization_config')->nullable()->after('is_customizable');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_customizable', 'customization_config']);
        });
    }
};
