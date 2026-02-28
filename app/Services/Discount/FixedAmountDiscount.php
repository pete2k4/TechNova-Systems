<?php

declare(strict_types=1);

namespace App\Services\Discount;

use App\Contracts\DiscountInterface;

class FixedAmountDiscount implements DiscountInterface
{
    /**
     * @param float $amount
     */
    public function __construct(
        private readonly float $amount
    ) {}

    /**
     * @param float $price
     * @return float
     */
    public function calculate(float $price): float
    {
        return min($this->amount, $price);
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }
}
