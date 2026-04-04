<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Services\Payment\Bridge\AbstractPaymentMethod;
use App\Services\Payment\Gateways\CreditCardGateway;

class CreditCardPayment extends AbstractPaymentMethod
{
    /**
     * @param string $cardNumber
     */
    public function __construct(
        private readonly string $cardNumber
    ) {
        parent::__construct(new CreditCardGateway(), $cardNumber);
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
        return 'Credit Card';
    }

    /**
     * @return string
     */
    public function getMaskedCardNumber(): string
    {
        $lastFour = substr($this->cardNumber, -4);
        return '**** **** **** ' . $lastFour;
    }
}
