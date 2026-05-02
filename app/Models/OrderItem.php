<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\PrototypeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model implements PrototypeInterface
{
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price'];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Create a deep clone of this order item without saving to database.
     * 
     * Use case: Duplicating order items when cloning orders, creating line item templates,
     * or bulk order operations.
     * 
     * The cloned instance has no primary key and no order_id association.
     * Product reference is preserved so the clone refers to the same product.
     * 
     * @return static A new OrderItem instance with copied attributes
     */
    public function clone()
    {
        // Create a new instance with the same attributes, excluding the primary key
        $cloned = new static($this->getAttributes());
        $cloned->forceCreate = false;

        // Remove the primary key and order association so the clone is treated as a new record
        $cloned->offsetUnset($this->getKeyName());
        $cloned->order_id = null;

        return $cloned;
    }
}
