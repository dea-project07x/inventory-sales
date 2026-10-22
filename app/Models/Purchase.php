<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Purchase extends Model
{
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'supplier_id', 'user_id', 'purchase_number', 'total', 'status',
        'purchased_at', 'cancelled_at', 'cancelled_by',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'purchased_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public static function generateNumber(): string
    {
        do {
            $number = 'PO-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        } while (static::where('purchase_number', $number)->exists());

        return $number;
    }
}
