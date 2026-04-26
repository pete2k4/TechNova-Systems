<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Contracts\ProductInterface;
use App\Contracts\ShippableInterface;

/**
 * Physical product - implements ProductInterface + ShippableInterface
 */
class PhysicalProduct implements ProductInterface, ShippableInterface
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'NVIDIA RTX 4090';
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return 1599.99;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'High-end GPU';
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return 2.5; // kg
    }

    /**
     * @return array
     */
    public function getDimensions(): array
    {
        return ['length' => 30, 'width' => 15, 'height' => 6]; // cm
    }

    /**
     * @return float
     */
    public function getShippingCost(): float
    {
        return 15.00;
    }
}
