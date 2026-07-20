<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;


class FooterText extends Model
{
    use HasTranslations;

    protected $fillable = [
        'slug',
        'text',
        'is_active',
        'sort_order',
    ];

    public array $translatable = [
        'text',
    ];
}