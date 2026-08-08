<?php

namespace App\Models;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model implements HasMedia
{
    use InteractsWithMedia;

   protected $fillable = [
    'platform_name',
    'url',
    'sort_order',
    'is_active',
    'whatsapp_number',
    'is_floating',
    'icon_svg',
];

    /**
     * Without these the flags come back as 0/1 ints, which made the toggle
     * logic and the storefront visibility checks depend on loose comparison.
     */
    protected $casts = [
        'is_active'   => 'boolean',
        'is_floating' => 'boolean',
        'sort_order'  => 'integer',
    ];
}