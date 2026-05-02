<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\PrototypeInterface;
use App\Services\PriceCalculator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model implements PrototypeInterface
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

    /**
     * Create a deep clone of this product without saving to database.
     * 
     * Use case: Creating product variations, catalog templates, or bulk product operations
     * without requiring database round-trips for each cloned variant.
     * 
     * The cloned instance has no primary key, allowing it to be saved as a new product.
     * Relationships (category, discounts) are NOT cloned by default to avoid deep recursion,
     * but can be manually copied if needed.
     * 
     * @return static A new Product instance with copied attributes
     */
    public function clone()
    {
        // Create a new instance with the same attributes, excluding the primary key
        $cloned = new static($this->getAttributes());
        $cloned->forceCreate = false;

        // Remove the primary key so the clone is treated as a new record
        $cloned->offsetUnset($this->getKeyName());

        return $cloned;
    }
}