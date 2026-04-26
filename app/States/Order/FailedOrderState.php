<?php

declare(strict_types=1);

namespace App\States\Order;

final class FailedOrderState extends AbstractOrderState
{
    public function getName(): string
    {
        return 'failed';
    }
}
