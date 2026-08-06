<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Slug generation for the car-rental models.
 *
 * These models are bilingual (Spatie translatable), and an Arabic-only name
 * slugifies to an empty string — which would violate the unique index the
 * moment a second record is created. So we always fall back to a stable
 * prefix and then suffix until the slug is free.
 */
trait GeneratesUniqueSlug
{
    public static function uniqueSlug(?string $source, string $fallback = 'item'): string
    {
        $base = Str::slug((string) $source) ?: $fallback;
        $slug = $base;
        $i    = 1;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . (++$i);
        }

        return $slug;
    }
}
