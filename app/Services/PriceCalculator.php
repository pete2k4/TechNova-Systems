<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\DiscountInterface;
use App\Factories\DiscountFactory;
use App\Models\Discount;
use Illuminate\Database\Eloquent\Collection;

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

    /**
     * Apply multiple discounts in tiered fashion.
     * High-tier discounts are applied from base price.
     * Low-tier discounts are applied from the price after high-tier discounts.
     *
     * @param float $basePrice
     * @param Collection $discounts Collection of Discount models
     * @return float
     */
    public function applyMultipleDiscounts(float $basePrice, Collection $discounts): float
    {
        // Separate discounts by category
        $highDiscounts = $discounts->filter(fn (Discount $d) => $d->category === 'high');
        $lowDiscounts = $discounts->filter(fn (Discount $d) => $d->category === 'low');

        $currentPrice = $basePrice;

        // Apply all high-tier discounts from base price
        foreach ($highDiscounts as $discount) {
            $discountAmount = DiscountFactory::create(
                (string) $discount->type,
                (float) $discount->amount
            )->calculate($currentPrice);
            $currentPrice = max(0.0, $currentPrice - $discountAmount);
        }

        // Apply all low-tier discounts from the price after high-tier discounts
        foreach ($lowDiscounts as $discount) {
            $discountAmount = DiscountFactory::create(
                (string) $discount->type,
                (float) $discount->amount
            )->calculate($currentPrice);
            $currentPrice = max(0.0, $currentPrice - $discountAmount);
        }

        return round($currentPrice, 2);
    }
}

