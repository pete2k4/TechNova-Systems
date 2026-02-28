<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Contracts\ProductInterface;
use App\Contracts\ShippableInterface;

/**
 * SOLID Principle: Interface Segregation Principle (ISP)
 * 
 * Physical product implements ProductInterface + ShippableInterface.
 * It only implements interfaces it actually uses - no download methods forced on it.
 * 
 * ✅ Respects ISP
 */
class PhysicalProduct implements ProductInterface, ShippableInterface
{
    public function getName(): string
    {
        return 'NVIDIA RTX 4090';
    }

    public function getPrice(): float
    {
        return 1599.99;
    }

    public function getDescription(): string
    {
        return 'High-end GPU';
    }

    public function getWeight(): float
    {
        return 2.5; // kg
    }

    public function getDimensions(): array
    {
        return ['length' => 30, 'width' => 15, 'height' => 6]; // cm
    }

    public function getShippingCost(): float
    {
        return 15.00;
    }
}
