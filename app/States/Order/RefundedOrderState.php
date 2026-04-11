<?php

declare(strict_types=1);

namespace App\States\Order;

final class RefundedOrderState extends AbstractOrderState
{
    public function getName(): string
    {
        return 'refunded';
    }
}
