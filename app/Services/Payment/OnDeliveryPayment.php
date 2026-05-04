<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Contracts\PaymentMethodInterface;

class OnDeliveryPayment implements PaymentMethodInterface
{
    public function process(float $amount): bool
    {
        return true;
    }

    public function getName(): string
    {
        return 'On Delivery';
    }
}
