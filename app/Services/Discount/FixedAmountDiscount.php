<?php

declare(strict_types=1);

namespace App\Services\Discount;

use App\Contracts\DiscountInterface;

/**
 * SOLID Principle: Open/Closed Principle (OCP)
 * 
 * Another concrete implementation - we EXTENDED functionality
 * without MODIFYING the existing PercentageDiscount class.
 * 
 * This is the essence of OCP: open for extension, closed for modification.
 */
class FixedAmountDiscount implements DiscountInterface
{
    public function __construct(
        private readonly float $amount
    ) {}

    /**
     * Calculate fixed amount discount.
     */
    public function calculate(float $price): float
    {
        // Logic: return min($this->amount, $price);
        return 0.0;
    }
}
