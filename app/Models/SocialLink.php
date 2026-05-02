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
    ];
}