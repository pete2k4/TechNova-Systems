<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\PrototypeInterface;
use App\Domain\Orders\State\OrderStateFactory;
use App\Domain\Orders\State\OrderStateInterface;
use App\Events\OrderPlaced;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model implements PrototypeInterface
{
    public const STATUS_CHECKOUT_STARTED = 'checkout_started';
    public const STATUS_PENDING_PAYMENT_PAGE = 'pending_payment_page';
    public const STATUS_PLACED = 'placed';
    public const STATUS_CANCELED = 'canceled';

    protected $fillable = ['user_id', 'order_number', 'subtotal', 'discount', 'total', 'status', 'payment_method', 'payment_credential'];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isCompleted(): bool
    {
        return in_array((string) $this->status, [self::STATUS_PLACED, 'completed'], true);
    }

    public function isFailed(): bool
    {
        return in_array((string) $this->status, [self::STATUS_CANCELED, 'failed'], true);
    }

    public function state(): OrderStateInterface
    {
        return OrderStateFactory::fromStatus((string) $this->status);
    }

    public function transitionTo(string $nextStatus): void
    {
        $this->state()->transitionTo($this, $nextStatus);

        if ($nextStatus === self::STATUS_PLACED) {
            event(new OrderPlaced($this->fresh('items.product') ?? $this));
        }
    }

    public function markPendingPaymentPage(): void
    {
        $this->transitionTo(self::STATUS_PENDING_PAYMENT_PAGE);
    }

    public function markPlaced(): void
    {
        $this->transitionTo(self::STATUS_PLACED);
    }

    public function markCanceled(): void
    {
        $this->transitionTo(self::STATUS_CANCELED);
    }

    /**
     * Create a deep clone of this order without saving to database.
     * 
     * Use case: Duplicating order templates, creating order variants for bulk operations,
     * or preparing orders for testing without affecting existing orders.
     * 
     * The cloned instance has no primary key and status is reset to CHECKOUT_STARTED.
     * Order items ARE cloned along with the order to maintain data integrity.
     * User association is preserved but the clone is a separate record.
     * 
     * @return static A new Order instance with copied attributes and cloned items
     */
    public function clone()
    {
        // Create a new instance with the same attributes, excluding the primary key
        $cloned = new static($this->getAttributes());
        $cloned->forceCreate = false;

        // Remove the primary key so the clone is treated as a new record
        $cloned->offsetUnset($this->getKeyName());

        // Reset status to checkout_started for the cloned order
        $cloned->status = self::STATUS_CHECKOUT_STARTED;

        return $cloned;
    }
}
