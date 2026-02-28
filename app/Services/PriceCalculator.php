<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\DiscountInterface;

/**
 * SOLID Principle: Open/Closed Principle (OCP)
 * 
 * The PriceCalculator works with any DiscountInterface implementation.
 * We can add new discount types without changing this class.
 * 
 * ✅ Open for extension: Add new discount classes
 * ✅ Closed for modification: This class doesn't change
 */
class PriceCalculator
{
    /**
     * Calculate final price after applying discount.
     */
    public function calculateFinalPrice(float $basePrice, DiscountInterface $discount): float
    {
        // Logic would be:
        // $discountAmount = $discount->calculate($basePrice);
        // return $basePrice - $discountAmount;
        
        return 0.0;
    }
}
