<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_READY = 'ready';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELED = 'canceled';

    public const CLIENT_ALLOWED_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_PREPARING,
        self::STATUS_READY,
    ];

    protected $fillable = [
        'reference',
        'client_name',
        'client_phone',
        'status',
        'total_amount',
        'confirmation_message',
        'placed_at',
        'invoice_path',
        'invoice_generated_at',
        'canceled_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'placed_at' => 'datetime',
        'invoice_generated_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_PREPARING]);
    }

    public static function labels(): array
    {
        return [
            self::STATUS_PENDING => 'En attente',
            self::STATUS_PREPARING => 'En preparation',
            self::STATUS_READY => 'Prete',
            self::STATUS_PAID => 'Payee',
            self::STATUS_CANCELED => 'Annulee',
        ];
    }

    public function statusLabel(): string
    {
        return self::labels()[$this->status] ?? ucfirst($this->status);
    }
}
