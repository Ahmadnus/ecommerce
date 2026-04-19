<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

  const STATUS_PENDING    = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED    = 'shipped';
    const STATUS_DELIVERED  = 'delivered';
    const STATUS_CANCELLED  = 'cancelled';
 
    const PAYMENT_COD     = 'cod';
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID    = 'paid';
 
    // ─── All statuses with Arabic labels for the UI ───────────────────────────
 
    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING    => ['label' => 'قيد الانتظار',  'color' => 'yellow'],
            self::STATUS_PROCESSING => ['label' => 'جارٍ التجهيز',  'color' => 'blue'],
            self::STATUS_SHIPPED    => ['label' => 'تم الشحن',      'color' => 'purple'],
            self::STATUS_DELIVERED  => ['label' => 'تم التسليم',    'color' => 'green'],
            self::STATUS_CANCELLED  => ['label' => 'ملغي',          'color' => 'red'],
        ];
    }
 
    // ─── Fillable ─────────────────────────────────────────────────────────────
 
 protected $fillable = [
    'user_id', 'order_number', 'status', 'payment_method', 'payment_status',
    'subtotal', 'delivery_fee', 'shipping_amount', 'total_amount',
    'shipping_name', 'shipping_email', 'shipping_phone', 'shipping_address', 
    'shipping_city', 'shipping_zip', 'shipping_country', 'notes'
];
    protected $casts = [
        'subtotal'        => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount'    => 'decimal:2',
        'paid_at'         => 'datetime',
    ];
 
    // ─── Relationships ────────────────────────────────────────────────────────
 
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
 
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
 
    // ─── Accessors ────────────────────────────────────────────────────────────
 
    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status]['label'] ?? $this->status;
    }
 
    public function getStatusColorAttribute(): string
    {
        return self::statuses()[$this->status]['color'] ?? 'gray';
    }
 
    // ─── Helpers ─────────────────────────────────────────────────────────────
 
    public static function generateOrderNumber(): string
    {
        return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
    }



    // ─── Relationships ────────────────────────────────────────────────────────

  
 

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'pending'    => 'yellow',
            'processing' => 'blue',
            'shipped'    => 'purple',
            'delivered'  => 'green',
            'cancelled'  => 'red',
            'refunded'   => 'gray',
            default      => 'gray',
        };
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
    public function zone(): \Illuminate\Database\Eloquent\Relations\BelongsTo
{
    return $this->belongsTo(\App\Models\Zone::class);
}


}
