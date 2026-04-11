<?php

declare(strict_types=1);

namespace App\Models;

use App\States\Order\OrderStateFactory;
use App\States\Order\OrderStateInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
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
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function getState(): OrderStateInterface
    {
        return OrderStateFactory::fromStatus($this->status);
    }

    public function transitionToCompleted(): self
    {
        return $this->applyState($this->getState()->complete($this));
    }

    public function transitionToFailed(): self
    {
        return $this->applyState($this->getState()->fail($this));
    }

    public function transitionToRefunded(): self
    {
        return $this->applyState($this->getState()->refund($this));
    }

    private function applyState(OrderStateInterface $state): self
    {
        $this->status = $state->getName();

        return $this;
    }
}
