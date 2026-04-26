<?php

declare(strict_types=1);

namespace App\States\Order;

use App\Models\Order;
use LogicException;

abstract class AbstractOrderState implements OrderStateInterface
{
    public function complete(Order $order): OrderStateInterface
    {
        throw $this->invalidTransition('complete');
    }

    public function fail(Order $order): OrderStateInterface
    {
        throw $this->invalidTransition('fail');
    }

    public function refund(Order $order): OrderStateInterface
    {
        throw $this->invalidTransition('refund');
    }

    protected function invalidTransition(string $action): LogicException
    {
        return new LogicException(
            sprintf('Cannot %s order when state is "%s".', $action, $this->getName())
        );
    }
}
