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

    // ── Per-element typography choices ──────────────────────────────────────
    public const FONT_DEFAULT      = 'default';
    public const FONT_SERIF        = 'serif';
    public const FONT_SERIF_ITALIC = 'serif_italic';
    public const FONT_SANS_SERIF   = 'sans_serif';

    public const FONT_FAMILIES = [
        self::FONT_DEFAULT      => 'الخط الافتراضي (Default)',
        self::FONT_SERIF        => 'سيريف إيطالي كلاسيكي/فاخر (Serif)',
        self::FONT_SERIF_ITALIC => 'سيريف إيطالي مائل (Serif Italic)',
        self::FONT_SANS_SERIF   => 'سان سيريف عصري ونظيف (Sans-serif)',
    ];

    public const LINK_STYLE_UNDERLINE = 'underline';
    public const LINK_STYLE_BUTTON    = 'button';

    public const LINK_STYLES = [
        self::LINK_STYLE_UNDERLINE => 'رابط نصي صغير تحته خط (Underlined Link)',
        self::LINK_STYLE_BUTTON    => 'زر (Button)',
    ];

    protected $fillable = [
        'title',
        'paragraph',
        'media_path',
        'media_type',
        'position',
        'button_text',
        'button_url',
        'link_text',
        'link_url',
        'link_color',
        'link_font_family',
        'link_style',
        'section_title_accent_color',
        'text_color',
        'title_font_family',
        'paragraph_font_family',
        'button_bg_color',
        'button_text_color',
        'text_alignment',
        'show_text_below_media',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active'             => 'boolean',
        'sort_order'            => 'integer',
        'show_text_below_media' => 'boolean',
    ];

    /**
     * Resolve an admin-selected font-family choice into a CSS font-family
     * stack + font-style, using safe system fonts (no external font loading).
     */
    public static function fontFamilyValue(?string $choice): array
    {
        return match ($choice) {
            self::FONT_SERIF        => ['family' => "Georgia, 'Times New Roman', Times, serif", 'style' => 'normal'],
            self::FONT_SERIF_ITALIC => ['family' => "Georgia, 'Times New Roman', Times, serif", 'style' => 'italic'],
            // Single-quoted font names (not double) — this value is injected
            // into an inline style="..." HTML attribute, which is itself
            // double-quoted; a literal " here would terminate the attribute early.
            self::FONT_SANS_SERIF   => ['family' => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif", 'style' => 'normal'],
            default                 => ['family' => null, 'style' => 'normal'],
        };
    }

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

        // Must match the disk HomepageSectionService actually stores to
        // (config('media-library.disk_name'), same disk the rest of the
        // app's media — Category/Product/HeroBanner — already uses).
        return Storage::disk(config('media-library.disk_name', 'public'))->url($this->media_path);
    }

    public function hasMedia(): bool
    {
        return $this->media_type !== 'none' && ! empty($this->media_path);
    }

    public function hasButton(): bool
    {
        return ! empty($this->button_text) && ! empty($this->button_url);
    }

    public function hasLink(): bool
    {
        return ! empty($this->link_text);
    }
}
