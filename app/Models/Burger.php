<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Burger extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'price',
        'image_path',
        'description',
        'stock_quantity',
        'is_archived',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_archived' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Burger $burger): void {
            if (! $burger->isDirty('name') && $burger->slug) {
                return;
            }

            $baseSlug = Str::slug($burger->name);
            $slug = $baseSlug;
            $index = 1;

            while (
                static::query()
                    ->where('slug', $slug)
                    ->where('id', '!=', $burger->id)
                    ->exists()
            ) {
                $slug = $baseSlug.'-'.$index;
                $index++;
            }

            $burger->slug = $slug;
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeClientVisible(Builder $query): Builder
    {
        return $query->where('is_archived', false);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->clientVisible()->where('stock_quantity', '>', 0);
    }
}
