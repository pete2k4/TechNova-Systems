<?php

declare(strict_types=1);

namespace App\States\Order;

use App\Models\Order;

final class PendingOrderState extends AbstractOrderState
{
    public function getName(): string
    {
        return 'pending';
    }

    public function complete(Order $order): OrderStateInterface
    {
        return new CompletedOrderState();
    }

    public function fail(Order $order): OrderStateInterface
    {
        return new FailedOrderState();
    }
}
