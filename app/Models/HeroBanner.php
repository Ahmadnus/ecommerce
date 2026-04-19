<?php

namespace App\Models;
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;




class HeroBanner extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'badge', 'title', 'subtitle', 'description', 
        'button_text', 'button_url', 'sort_order', 'is_active'
    ];

    // تعريف الـ Collection للصور
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('banner_image')->singleFile();
    }
}