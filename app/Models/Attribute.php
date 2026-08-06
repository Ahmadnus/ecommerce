<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Attribute extends Model
{
    use HasTranslations;

    // Spatie: which fields are translatable
    public array $translatable = ['name'];

    protected $fillable = ['name', 'slug', 'type', 'sort_order'];

    protected static function booted(): void
    {
        static::creating(function (self $attribute): void {
            // Slug is generated once from the English name (or fallback)
            if (empty($attribute->slug)) {
                $nameForSlug = is_array($attribute->name)
                    ? ($attribute->getTranslation('name', 'en') ?: reset($attribute->name))
                    : $attribute->name;

                $attribute->slug = Str::slug($nameForSlug);
            }
        });
    }

    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class)
                    ->orderBy('sort_order')
                    ->orderBy('id');
    }
}