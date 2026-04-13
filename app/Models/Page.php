<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'content',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    // ─── Boot: auto-generate slug if empty ───────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $page): void {
            if (empty($page->slug)) {
                $page->slug = static::uniqueSlug($page->name);
            }
        });

        static::updating(function (self $page): void {
            if ($page->isDirty('name') && empty($page->slug)) {
                $page->slug = static::uniqueSlug($page->name);
            }
        });
    }

    // ─── Helpers ─────────────────────────────────────────────────────────

    public static function uniqueSlug(string $name, ?int $excludeId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;

        $query = static::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->clone()->exists()) {
            $slug  = "{$base}-{$i}";
            $query = static::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            $i++;
        }

        return $slug;
    }

    // ─── Scopes ──────────────────────────────────────────────────────────

    public function scopeActive($query): mixed
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query): mixed
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
