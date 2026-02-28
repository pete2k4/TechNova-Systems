<?php

declare(strict_types=1);

namespace App\Services\Discount;

use App\Contracts\DiscountInterface;

/**
 * SOLID Principle: Open/Closed Principle (OCP)
 * 
 * This is a concrete implementation of DiscountInterface.
 * We can add new discount types by creating new classes
 * without modifying existing discount classes.
 */
class PercentageDiscount implements DiscountInterface
{
    public function __construct(
        private readonly float $percentage
    ) {}

    /**
     * Calculate percentage-based discount.
     */
    public function calculate(float $price): float
    {
        // Logic: return $price * ($this->percentage / 100);
        return 0.0;
    }
}
