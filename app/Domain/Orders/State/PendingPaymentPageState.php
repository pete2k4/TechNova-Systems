<?php

declare(strict_types=1);

namespace App\Domain\Orders\State;

use App\Models\Order;

class PendingPaymentPageState extends AbstractOrderState
{
    public function status(): string
    {
        return Order::STATUS_PENDING_PAYMENT_PAGE;
    }

    protected function allowedTransitions(): array
    {
        return [
            Order::STATUS_PLACED,
            Order::STATUS_CANCELED,
        ];
    }
}
