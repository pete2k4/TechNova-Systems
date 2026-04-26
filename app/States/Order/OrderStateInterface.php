<?php

declare(strict_types=1);

namespace App\States\Order;

use App\Models\Order;

interface OrderStateInterface
{
    public function getName(): string;

    public function complete(Order $order): OrderStateInterface;

    public function fail(Order $order): OrderStateInterface;

    public function refund(Order $order): OrderStateInterface;
}
