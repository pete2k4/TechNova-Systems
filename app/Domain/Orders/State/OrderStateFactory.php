<?php

declare(strict_types=1);

namespace App\Domain\Orders\State;

use DomainException;

class OrderStateFactory
{
    public static function fromStatus(string $status): OrderStateInterface
    {
        return match ($status) {
            'checkout_started' => new CheckoutStartedState(),
            'pending_payment_page' => new PendingPaymentPageState(),
            'placed' => new PlacedState(),
            'canceled' => new CanceledState(),
            default => throw new DomainException('Unknown order status: ' . $status),
        };
    }
}
