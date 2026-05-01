<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\PriceCalculator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['category_id', 'name', 'slug', 'description', 'image_url', 'price', 'type', 'sku', 'stock', 'is_active'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function discounts(): BelongsToMany
    {
        return $this->belongsToMany(Discount::class)
            ->withPivot('applied_at')
            ->withTimestamps();
    }

    public function isDigital(): bool
    {
        return $this->type === 'digital';
    }

    public function isPhysical(): bool
    {
        return $this->type === 'physical';
    }

    public function getDiscountedPriceAttribute(): float
    {
        $basePrice = (float) $this->price;
        $discounts = $this->relationLoaded('discounts')
            ? $this->discounts->filter(fn (Discount $discount): bool => $discount->is_active)
            : $this->discounts()->active()->get();

        if ($discounts->isEmpty()) {
            return $basePrice;
        }

        $calculator = new PriceCalculator();
        return $calculator->applyMultipleDiscounts($basePrice, $discounts);
    }
}