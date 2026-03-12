<?php

declare(strict_types=1);

namespace App\Services\Payment\Adapters;

use App\Contracts\PaymentMethodInterface;
use App\Services\Payment\Gateways\FastPayGateway;

class FastPayAdapter implements PaymentMethodInterface
{
    /**
     * @param FastPayGateway $gateway
     * @param string $customerToken
     */
    public function __construct(
        private readonly FastPayGateway $gateway,
        private readonly string $customerToken
    ) {}

    /**
     * @param float $amount
     * @return bool
     */
    public function process(float $amount): bool
    {
        $response = $this->gateway->charge(
            $this->customerToken,
            (int) round($amount * 100)
        );

        return ($response['status'] ?? null) === 'approved';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'FastPay';
    }

    /**
     * @return string
     */
    public function getCustomerToken(): string
    {
        return $this->customerToken;
    }
}
