<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Attribute extends Model
{
    protected $fillable = ['name', 'slug', 'type', 'sort_order'];

    protected static function booted(): void
    {
        static::creating(fn(self $a): mixed => $a->slug ??= Str::slug($a->name));
    }

    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class)->orderBy('sort_order')->orderBy('value');
    }
}