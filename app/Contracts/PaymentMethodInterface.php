<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * SOLID Principle: Liskov Substitution Principle (LSP)
 * 
 * Interface for payment methods.
 * All implementations MUST be substitutable wherever PaymentMethodInterface is expected.
 * 
 * LSP states: Derived classes should be able to replace their base classes
 * without breaking the application.
 */
interface PaymentMethodInterface
{
    /**
     * Process a payment.
     * All implementations MUST return true on success, false on failure.
     * 
     * @return bool True if payment successful, false otherwise
     */
    public function process(float $amount): bool;

    /**
     * Get payment method name.
     */
    public function getName(): string;
}
