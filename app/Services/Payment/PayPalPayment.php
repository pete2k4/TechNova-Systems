<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Contracts\PaymentMethodInterface;

/**
 * SOLID Principle: Liskov Substitution Principle (LSP)
 * 
 * Another implementation that fully respects the PaymentMethodInterface contract.
 * Can be substituted anywhere PaymentMethodInterface is used.
 * 
 * ✅ Substitutable for PaymentMethodInterface
 */
class PayPalPayment implements PaymentMethodInterface
{
    public function __construct(
        private readonly string $email
    ) {}

    public function process(float $amount): bool
    {
        // Logic: Connect to PayPal API, process payment
        // return $paypalApi->charge($this->email, $amount);
        
        return true; // Demo
    }

    public function getName(): string
    {
        return 'PayPal';
    }
}
