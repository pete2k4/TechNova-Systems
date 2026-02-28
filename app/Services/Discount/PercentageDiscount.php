<?php

declare(strict_types=1);

namespace App\Services\Discount;

use App\Contracts\DiscountInterface;

class PercentageDiscount implements DiscountInterface
{
    /**
     * @param float $percentage
     */
    public function __construct(
        private readonly float $percentage
    ) {}

    /**
     * @param float $price
     * @return float
     */
    public function calculate(float $price): float
    {
        return $price * ($this->percentage / 100);
    }

    /**
     * @return float
     */
    public function getPercentage(): float
    {
        return $this->percentage;
    }
}
