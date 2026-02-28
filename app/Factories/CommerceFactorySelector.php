<?php

declare(strict_types=1);

namespace App\Factories;

use App\Factories\Abstractions\CommerceFactoryInterface;
use App\Factories\Concrete\DigitalProductCommerceFactory;
use App\Factories\Concrete\PhysicalProductCommerceFactory;
use InvalidArgumentException;

/**
 * Commerce Factory Selector
 * 
 * Returns the appropriate abstract factory based on product type.
 * Acts as a factory for factories (meta-factory).
 */
class CommerceFactorySelector
{
    private static array $factories = [];

    /**
     * Get the commerce factory for a given product type.
     *
     * @param string $productType
     * @return CommerceFactoryInterface
     * @throws InvalidArgumentException
     */
    public static function getFactory(string $productType): CommerceFactoryInterface
    {
        $productType = strtolower($productType);

        if (!isset(self::$factories[$productType])) {
            self::$factories[$productType] = self::createFactory($productType);
        }

        return self::$factories[$productType];
    }

    /**
     * Create a factory instance for the given product type.
     *
     * @param string $productType
     * @return CommerceFactoryInterface
     * @throws InvalidArgumentException
     */
    private static function createFactory(string $productType): CommerceFactoryInterface
    {
        return match ($productType) {
            'digital' => new DigitalProductCommerceFactory(),
            'physical' => new PhysicalProductCommerceFactory(),
            default => throw new InvalidArgumentException("Unknown product type: {$productType}"),
        };
    }

    /**
     * Register a custom factory for a product type.
     *
     * @param string $productType
     * @param CommerceFactoryInterface $factory
     * @return void
     */
    public static function registerFactory(string $productType, CommerceFactoryInterface $factory): void
    {
        self::$factories[strtolower($productType)] = $factory;
    }

    /**
     * Clear the factory cache.
     *
     * @return void
     */
    public static function clearCache(): void
    {
        self::$factories = [];
    }
}
