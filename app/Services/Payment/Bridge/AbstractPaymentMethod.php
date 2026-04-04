<?php

declare(strict_types=1);

namespace App\Services\Payment\Bridge;

use App\Contracts\PaymentGatewayInterface;
use App\Contracts\PaymentMethodInterface;

/**
 * Bridge abstraction for payment methods.
 *
 * The abstraction (payment method) is separated from the implementation
 * (payment gateway), so either side can change independently.
 */
abstract class AbstractPaymentMethod implements PaymentMethodInterface
{
    public function __construct(
        protected readonly PaymentGatewayInterface $gateway,
        protected readonly string $credential,
    ) {
    }

    public function process(float $amount): bool
    {
        $response = $this->gateway->charge(
            $this->credential,
            (int) round($amount * 100)
        );

        return ($response['status'] ?? null) === 'approved';
    }

    public function getCredential(): string
    {
        return $this->credential;
    }
}