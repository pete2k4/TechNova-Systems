<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Orders\State\OrderStateFactory;
use App\Domain\Orders\State\OrderStateInterface;
use App\Events\OrderPlaced;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
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
}
