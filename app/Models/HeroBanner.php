<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations; // ← ADD

class HeroBanner extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    use HasTranslations; // ← ADD

    // ── Translatable fields ────────────────────────────────────────────────
    public array $translatable = ['badge', 'title', 'subtitle', 'description', 'button_text'];

    protected $fillable = [
        'badge',            // json
        'title',            // json
        'subtitle',         // json
        'description',      // json
        'button_text',      // json
        'button_url',
        'background_color',
        'text_color',
        'position',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
        // DO NOT cast translatable fields — Spatie handles their JSON
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('banner_image')->singleFile();
    }
}