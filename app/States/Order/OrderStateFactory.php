<?php

declare(strict_types=1);

namespace App\States\Order;

use InvalidArgumentException;

final class OrderStateFactory
{
    public static function fromStatus(string $status): OrderStateInterface
    {
        return match ($status) {
            'pending' => new PendingOrderState(),
            'completed' => new CompletedOrderState(),
            'failed' => new FailedOrderState(),
            'refunded' => new RefundedOrderState(),
            default => throw new InvalidArgumentException("Unknown order status: {$status}"),
        };
    }
}
