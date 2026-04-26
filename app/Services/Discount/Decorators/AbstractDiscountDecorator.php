<?php

declare(strict_types=1);

namespace App\Services\Discount\Decorators;

use App\Contracts\DiscountInterface;

/**
 * Decorator Pattern - Abstract Base for Discount Decorators
 *
 * Wraps a DiscountInterface and delegates calculate() to the wrapped discount,
 * then adds extra behavior before or after the calculation.
 */
abstract class AbstractDiscountDecorator implements DiscountInterface
{
    public function __construct(
        protected DiscountInterface $wrappedDiscount,
    ) {
    }

    /**
     * Calculate the discount. Subclasses override to add behavior.
     */
    abstract public function calculate(float $price): float;

    /**
     * Get the wrapped discount (for debugging/introspection).
     */
    public function getWrappedDiscount(): DiscountInterface
    {
        return $this->wrappedDiscount;
    }
}
