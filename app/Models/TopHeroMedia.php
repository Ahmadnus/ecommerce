<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TopHeroMedia extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'top_hero_medias';

    protected $fillable = [
        'type',
        'position',
        'link_url',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('hero_media')->singleFile();
    }
}
