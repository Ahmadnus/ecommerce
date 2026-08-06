<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int         $id
 * @property int         $product_id
 * @property int|null    $user_id
 * @property string|null $reviewer_name
 * @property string|null $reviewer_email
 * @property int         $rating          1–5
 * @property string      $comment
 * @property string      $status          pending|approved|rejected
 * @property bool        $is_pinned
 */
class ProductReview extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'reviewer_name',
        'reviewer_email',
        'rating',
        'comment',
        'status',
        'is_pinned',
    ];

    protected $casts = [
        'rating'    => 'integer',
        'is_pinned' => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopePinnedFirst(Builder $query): Builder
    {
        return $query->orderByDesc('is_pinned')->orderByDesc('created_at');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Display name: authenticated user name > guest name > "مجهول"
     */
    public function displayName(): string
    {
        return $this->user?->name
            ?? $this->reviewer_name
            ?? 'مجهول';
    }

    /**
     * Returns an array of filled/empty star booleans for use in Blade.
     * e.g. rating=3 → [true, true, true, false, false]
     */
    public function starArray(): array
    {
        return array_map(
            fn(int $i) => $i <= $this->rating,
            range(1, 5)
        );
    }

    /**
     * Status label in Arabic.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'approved' => 'معتمد',
            'rejected' => 'مرفوض',
            default    => 'قيد المراجعة',
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'approved' => 'green',
            'rejected' => 'red',
            default    => 'yellow',
        };
    }
}