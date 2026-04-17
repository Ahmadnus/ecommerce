<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteFeature extends Model
{
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