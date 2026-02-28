<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Contracts\PaymentMethodInterface;

/**
 * SOLID Principle: Liskov Substitution Principle (LSP)
 * 
 * This class works with ANY PaymentMethodInterface implementation.
 * Thanks to LSP, we can substitute CreditCard, PayPal, or any future
 * payment method without changing this class.
 */
class PaymentProcessor
{
    /**
     * Process payment using any PaymentMethodInterface.
     * 
     * Because of LSP, this works with CreditCard, PayPal, or any
     * future PaymentMethodInterface implementation without modification.
     */
    public function processPayment(PaymentMethodInterface $method, float $amount): bool
    {
        // We can safely call process() on any PaymentMethodInterface implementation
        // $success = $method->process($amount);
        
        // if ($success) {
        //     Log::info("Payment processed via {$method->getName()}");
        // }
        
        return true; // Demo
    }
}
