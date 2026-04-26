<?php

declare(strict_types=1);

namespace App\Domain\Orders\State;

use App\Models\Order;

interface OrderStateInterface
{
    public function status(): string;

    public function canTransitionTo(string $nextStatus): bool;

    public function transitionTo(Order $order, string $nextStatus): void;
}
