<?php

declare(strict_types=1);

namespace App\Services\Discount\Decorators;

/**
 * Loyalty Bonus Decorator
 *
 * Adds an extra loyalty percentage bonus on top of the wrapped discount.
 *
 * Example: Base discount of $20, loyalty bonus of 10%
 *   Original discount: $20
 *   Loyalty bonus: $20 * (10/100) = $2
 *   Total: $22
 */
class LoyaltyBonusDecorator extends AbstractDiscountDecorator
{
    public function __construct(
        private readonly float $loyaltyBonusPercentage,
        \App\Contracts\DiscountInterface $wrappedDiscount,
    ) {
        parent::__construct($wrappedDiscount);
    }

    /**
     * Calculate wrapped discount, then add loyalty bonus percentage on top.
     */
    public function calculate(float $price): float
    {
        $baseDiscount = $this->wrappedDiscount->calculate($price);
        $loyaltyBonus = $baseDiscount * ($this->loyaltyBonusPercentage / 100);

        return $baseDiscount + $loyaltyBonus;
    }

    public function getLoyaltyBonusPercentage(): float
    {
        return $this->loyaltyBonusPercentage;
    }
}
