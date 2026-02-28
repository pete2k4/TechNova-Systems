<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * SOLID Principle: Open/Closed Principle (OCP)
 * 
 * This interface defines a contract for discount calculations.
 * Classes are OPEN for extension (new discount types) but CLOSED for modification.
 * 
 * We can add new discount types without changing existing code.
 */
interface DiscountInterface
{
    /**
     * Calculate discount amount for a given price.
     */
    public function calculate(float $price): float;
}
