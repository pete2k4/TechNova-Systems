<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\DiscountInterface;
use App\Factories\DiscountFactory;

class PriceCalculator
{
    /**
     * @param float $basePrice
     * @param string $discountType
     * @param float $discountValue
     * @return float
     */
    public function calculateDiscountedPrice(float $basePrice, string $discountType, float $discountValue): float
    {
        $discount = DiscountFactory::create($discountType, $discountValue);
        return $this->applyDiscount($basePrice, $discount);
    }

    /**
     * @param float $basePrice
     * @param array $discountConfig
     * @return float
     */
    public function calculateFromConfig(float $basePrice, array $discountConfig): float
    {
        $discount = DiscountFactory::fromConfig($discountConfig);
        return $this->applyDiscount($basePrice, $discount);
    }

    /**
     * @param float $basePrice
     * @param DiscountInterface $discount
     * @return float
     */
    public function applyDiscount(float $basePrice, DiscountInterface $discount): float
    {
        $discountAmount = $discount->calculate($basePrice);
        return max(0.0, $basePrice - $discountAmount);
    }
}
