<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations; // ← ADD

class Page extends Model
{
    use HasTranslations; // ← ADD

    // ── Declare translatable fields ────────────────────────────────────────
    public array $translatable = ['name', 'content'];

    protected $fillable = [
        'name',       // json
        'slug',
        'content',    // json
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
        // DO NOT cast 'name' or 'content' — Spatie handles their JSON
    ];

    // ─── Boot ─────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $page): void {
            if (empty($page->slug)) {
                // Prefer Arabic name for slug; fall back to English
                $page->slug = static::uniqueSlug(
                    $page->getTranslation('name', 'ar', false)
                    ?: $page->getTranslation('name', 'en', false)
                );
            }
        });

        static::updating(function (self $page): void {
            if ($page->isDirty('name') && empty($page->slug)) {
                $page->slug = static::uniqueSlug(
                    $page->getTranslation('name', 'ar', false)
                    ?: $page->getTranslation('name', 'en', false),
                    $page->id
                );
            }
        });
    }

    // ─── Helpers ──────────────────────────────────────────────────────────

    public static function uniqueSlug(string $name, ?int $excludeId = null): string
    {
        $base = Str::slug($name);

        // If Str::slug produces an empty string (e.g. pure Arabic input),
        // fall back to a transliteration-friendly random suffix
        if (empty($base)) {
            $base = 'page-' . Str::lower(Str::random(6));
        }

        $slug = $base;
        $i    = 1;

        while (true) {
            $query = static::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            if (!$query->exists()) break;

            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    // ─── Scopes ───────────────────────────────────────────────────────────

    public function scopeActive($query): mixed
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query): mixed
    {
        // orderBy('name') works correctly with Spatie — it orders by the
        // JSON column, which means alphabetical on the raw JSON string.
        // For true locale-aware sorting use a raw expression if needed.
        return $query->orderBy('sort_order')->orderBy('name');
    }
}