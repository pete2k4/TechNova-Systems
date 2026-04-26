<?php

declare(strict_types=1);

namespace App\Services\Discount\Decorators;

/**
 * Capped Discount Decorator
 *
 * Ensures discount never exceeds a maximum cap.
 *
 * Example: Discount capped at $50
 *   Base discount calculates to $100 → returned as $50
 *   Base discount calculates to $30 → returned as $30
 */
class CappedDiscountDecorator extends AbstractDiscountDecorator
{
    public function __construct(
        private readonly float $maximumDiscount,
        \App\Contracts\DiscountInterface $wrappedDiscount,
    ) {
        parent::__construct($wrappedDiscount);
    }

    /**
     * Calculate discount but cap it at the maximum.
     */
    public function calculate(float $price): float
    {
        $discount = $this->wrappedDiscount->calculate($price);

        return min($discount, $this->maximumDiscount);
    }

    public function getMaximumDiscount(): float
    {
        return $this->maximumDiscount;
    }
}
