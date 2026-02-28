<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Contracts\PaymentMethodInterface;

class CreditCardPayment implements PaymentMethodInterface
{
    /**
     * @param string $cardNumber
     */
    public function __construct(
        private readonly string $cardNumber
    ) {}

    /**
     * @param float $amount
     * @return bool
     */
    public function process(float $amount): bool
    {
        // In production, this would connect to a payment gateway
        // $gateway = new PaymentGateway();
        // return $gateway->charge($this->cardNumber, $amount);
        
        return true;
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
