<?php

declare(strict_types=1);

namespace App\Domain\Orders\State;

use App\Models\Order;

class CheckoutStartedState extends AbstractOrderState
{
    public function status(): string
    {
        return Order::STATUS_CHECKOUT_STARTED;
    }

    protected function allowedTransitions(): array
    {
        return [
            Order::STATUS_PENDING_PAYMENT_PAGE,
            Order::STATUS_CANCELED,
        ];
    }
}
