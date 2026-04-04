<?php

declare(strict_types=1);

namespace App\Services\Discount\Decorators;

/**
 * Minimum Purchase Decorator
 *
 * Only returns the wrapped discount if the price meets a minimum threshold.
 * Otherwise returns zero discount.
 *
 * Example: Discount only applies if purchase >= $100
 *   Price $150 with discount: discount applies
 *   Price $50 with discount: discount does NOT apply (0.0)
 */
class MinimumPurchaseDecorator extends AbstractDiscountDecorator
{
    public function __construct(
        private readonly float $minimumPrice,
        \App\Contracts\DiscountInterface $wrappedDiscount,
    ) {
        parent::__construct($wrappedDiscount);
    }

    /**
     * Apply discount only if price meets minimum. Otherwise 0.0.
     */
    public function calculate(float $price): float
    {
        if ($price < $this->minimumPrice) {
            return 0.0;
        }

        return $this->wrappedDiscount->calculate($price);
    }

    public function getMinimumPrice(): float
    {
        return $this->minimumPrice;
    }
}
