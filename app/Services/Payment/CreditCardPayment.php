<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Contracts\PaymentMethodInterface;

/**
 * SOLID Principle: Liskov Substitution Principle (LSP)
 * 
 * This class can be used anywhere PaymentMethodInterface is expected.
 * It respects the contract: process() returns bool, getName() returns string.
 * 
 * ✅ Substitutable for PaymentMethodInterface
 */
class CreditCardPayment implements PaymentMethodInterface
{
    public function __construct(
        private readonly string $cardNumber
    ) {}

    public function process(float $amount): bool
    {
        // Logic: Connect to payment gateway, process card payment
        // return $gateway->charge($this->cardNumber, $amount);
        
        return true; // Demo
    }

    public function getName(): string
    {
        return 'Credit Card';
    }
}
