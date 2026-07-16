<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HomepageSection extends Model
{
    use HasFactory;

    // ── Predefined homepage render slots ────────────────────────────────────
    public const POSITION_TOP_HERO          = 'top_hero';
    public const POSITION_BELOW_CATEGORIES  = 'below_categories';
    public const POSITION_ABOVE_FOOTER      = 'above_footer';

    public const POSITIONS = [
        self::POSITION_TOP_HERO         => 'القسم العلوي (Top Hero)',
        self::POSITION_BELOW_CATEGORIES => 'قسم تحت التصنيفات (Below Categories)',
        self::POSITION_ABOVE_FOOTER     => 'قسم اسفل الشاشة فوق الفوتر (Above Footer)',
    ];

    // ── Text alignment choices ──────────────────────────────────────────────
    public const ALIGN_LEFT   = 'left';
    public const ALIGN_CENTER = 'center';
    public const ALIGN_RIGHT  = 'right';

    public const TEXT_ALIGNMENTS = [
        self::ALIGN_LEFT   => 'يسار (Left)',
        self::ALIGN_CENTER => 'وسط (Center)',
        self::ALIGN_RIGHT  => 'يمين (Right)',
    ];

    protected $fillable = [
        'title',
        'paragraph',
        'media_path',
        'media_type',
        'position',
        'button_text',
        'button_url',
        'section_title_accent_color',
        'text_color',
        'button_bg_color',
        'button_text_color',
        'text_alignment',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeForPosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    public function getMediaUrlAttribute(): ?string
    {
        if (! $this->media_path) {
            return null;
        }

        return Storage::disk('public')->url($this->media_path);
    }

    public function hasMedia(): bool
    {
        return $this->media_type !== 'none' && ! empty($this->media_path);
    }

    public function hasButton(): bool
    {
        return ! empty($this->button_text) && ! empty($this->button_url);
    }
}
