<?php

declare(strict_types=1);

namespace App\States\Order;

use App\Models\Order;

final class CompletedOrderState extends AbstractOrderState
{
    public function getName(): string
    {
        return 'completed';
    }

    public function refund(Order $order): OrderStateInterface
    {
        return new RefundedOrderState();
    }
}
