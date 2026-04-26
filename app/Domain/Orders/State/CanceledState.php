<?php

declare(strict_types=1);

namespace App\Domain\Orders\State;

use App\Models\Order;

class CanceledState extends AbstractOrderState
{
    public function status(): string
    {
        return Order::STATUS_CANCELED;
    }

    protected function allowedTransitions(): array
    {
        return [];
    }
}
