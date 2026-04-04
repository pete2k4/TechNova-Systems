<?php

declare(strict_types=1);

namespace App\Services\Payment\Adapters;

use App\Services\Payment\Bridge\AbstractPaymentMethod;
use App\Services\Payment\Gateways\FastPayGateway;

class FastPayAdapter extends AbstractPaymentMethod
{
    /**
     * @param FastPayGateway $gateway
     * @param string $customerToken
     */
    public function __construct(
        private readonly FastPayGateway $gateway,
        private readonly string $customerToken
    ) {
        parent::__construct($gateway, $customerToken);
    }

    /**
     * @param float $amount
     * @return bool
     */
    public function process(float $amount): bool
    {
        return parent::process($amount);
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
