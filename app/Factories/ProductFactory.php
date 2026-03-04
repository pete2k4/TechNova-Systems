<?php

declare(strict_types=1);

namespace App\Factories;

use App\Contracts\ProductInterface;
use App\Contracts\PrototypeInterface;
use App\DTOs\DigitalProduct;
use App\DTOs\PhysicalProduct;
use App\Prototypes\ProductPrototypeRegistry;
use InvalidArgumentException;

class ProductFactory
{
    public const DIGITAL = 'digital';
    public const PHYSICAL = 'physical';

    private static bool $initialized = false;

    private static function ensureInitialized(): void
    {
        if (!self::$initialized) {
            self::registry()->register(self::DIGITAL, new DigitalProduct());
            self::registry()->register(self::PHYSICAL, new PhysicalProduct());
            self::$initialized = true;
        }
    }

    private static function registry(): ProductPrototypeRegistry
    {
        return ProductPrototypeRegistry::getInstance();
    }

    public static function registerPrototype(string $type, ProductInterface&PrototypeInterface $prototype): void
    {
        self::ensureInitialized();
        self::registry()->register($type, $prototype);
    }

    /**
     * @param string $type
     * @param array $data
     * @return ProductInterface
     * @throws InvalidArgumentException
     */
    public static function create(string $type, array $data = []): ProductInterface
    {
        self::ensureInitialized();
        
        $normalizedType = strtolower($type);

        if (!self::registry()->has($normalizedType)) {
            throw new InvalidArgumentException("Unknown product type: {$type}");
        }

        return self::registry()->getClone($normalizedType);
    }

    /**
     * @param array $data
     * @return DigitalProduct
     */
    public static function createDigitalProduct(array $data = []): DigitalProduct
    {
        self::ensureInitialized();
        
        /** @var DigitalProduct $product */
        $product = self::registry()->getClone(self::DIGITAL);
        return $product;
    }

    /**
     * @param array $data
     * @return PhysicalProduct
     */
    public static function createPhysicalProduct(array $data = []): PhysicalProduct
    {
        self::ensureInitialized();
        
        /** @var PhysicalProduct $product */
        $product = self::registry()->getClone(self::PHYSICAL);
        return $product;
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
