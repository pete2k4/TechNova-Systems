<?php

declare(strict_types=1);

namespace App\Domain\Orders\State;

use App\Models\Order;
use DomainException;

abstract class AbstractOrderState implements OrderStateInterface
{
    /**
     * @return array<int,string>
     */
    abstract protected function allowedTransitions(): array;

    public function canTransitionTo(string $nextStatus): bool
    {
        return in_array($nextStatus, $this->allowedTransitions(), true);
    }

    public function transitionTo(Order $order, string $nextStatus): void
    {
        if (!$this->canTransitionTo($nextStatus)) {
            throw new DomainException(sprintf(
                'Cannot transition order from "%s" to "%s".',
                $this->status(),
                $nextStatus
            ));
        }

        $order->status = $nextStatus;
        $order->save();
    }
}
