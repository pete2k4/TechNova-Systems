<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * SOLID Principle: Interface Segregation Principle (ISP)
 * 
 * ✅ GOOD - Segregated interface only for shippable products.
 * Only physical products that need shipping implement this.
 * 
 * Digital products don't need to implement these methods.
 */
interface ShippableInterface
{
    public function getWeight(): float;
    public function getDimensions(): array;
    public function getShippingCost(): float;
}
