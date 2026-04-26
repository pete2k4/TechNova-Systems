<?php

declare(strict_types=1);

namespace App\Domain\Orders\State;

use App\Models\Order;

class PlacedState extends AbstractOrderState
{
    public function status(): string
    {
        return Order::STATUS_PLACED;
    }

    protected function allowedTransitions(): array
    {
        return [];
    }
}
