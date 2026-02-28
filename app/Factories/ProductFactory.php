<?php

declare(strict_types=1);

namespace App\Factories;

use App\Contracts\ProductInterface;
use App\DTOs\DigitalProduct;
use App\DTOs\PhysicalProduct;
use InvalidArgumentException;

class ProductFactory
{
    public const DIGITAL = 'digital';
    public const PHYSICAL = 'physical';

    /**
     * @param string $type
     * @param array $data
     * @return ProductInterface
     * @throws InvalidArgumentException
     */
    public static function create(string $type, array $data = []): ProductInterface
    {
        return match (strtolower($type)) {
            self::DIGITAL => self::createDigitalProduct($data),
            self::PHYSICAL => self::createPhysicalProduct($data),
            default => throw new InvalidArgumentException("Unknown product type: {$type}"),
        };
    }

    /**
     * @param array $data
     * @return DigitalProduct
     */
    public static function createDigitalProduct(array $data = []): DigitalProduct
    {
        return new DigitalProduct();
    }

    /**
     * @param array $data
     * @return PhysicalProduct
     */
    public static function createPhysicalProduct(array $data = []): PhysicalProduct
    {
        return new PhysicalProduct();
    }

    /**
     * @param array $attributes
     * @return ProductInterface
     * @throws InvalidArgumentException
     */
    public static function fromArray(array $attributes): ProductInterface
    {
        $type = $attributes['type'] ?? throw new InvalidArgumentException('Missing product type');
        return self::create($type, $attributes);
    }
}
