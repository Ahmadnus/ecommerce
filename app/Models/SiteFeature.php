<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class SiteFeature extends Model
{
    use HasTranslations;

    /** Spatie will automatically get/set these as JSON per-locale. */
    public array $translatable = ['title', 'description'];

    protected $fillable = ['icon', 'title', 'description', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    /** Returns only active features, ordered by sort_order. */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}