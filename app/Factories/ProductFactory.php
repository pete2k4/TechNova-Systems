<?php

declare(strict_types=1);

namespace App\Factories;

use App\Contracts\PrototypeInterface;
use App\Contracts\ProductInterface;
use App\DTOs\DigitalProduct;
use App\DTOs\PhysicalProduct;
use App\Models\Product;
use InvalidArgumentException;

class ProductFactory
{
    public const DIGITAL = 'digital';
    public const PHYSICAL = 'physical';

    /**
     * @var PrototypeRegistry Manages product prototypes for cloning
     */
    private static PrototypeRegistry $prototypeRegistry;

    /**
     * Get the prototype registry, lazily initializing it if needed.
     * 
     * @return PrototypeRegistry The shared prototype registry instance
     */
    private static function getPrototypeRegistry(): PrototypeRegistry
    {
        if (!isset(self::$prototypeRegistry)) {
            self::$prototypeRegistry = new PrototypeRegistry();
        }

        return self::$prototypeRegistry;
    }

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

    /**
     * Clone a Product model using the Prototype pattern.
     * 
     * Creates an exact copy of a product instance without requiring database access.
     * The cloned product has no primary key and can be saved as a new product.
     * 
     * Use case: Creating product variants, catalog templates, or bulk product operations.
     * 
     * @param Product $product The product to clone
     * @return Product A cloned instance with copied attributes
     */
    public static function cloneProduct(Product $product): Product
    {
        return $product->clone();
    }

    /**
     * Register a product prototype by key.
     * 
     * Prototypes are templates that can be cloned multiple times without database round-trips.
     * 
     * Use case: Storing base catalog products as templates for creating variants.
     * 
     * @param string $key Unique identifier for the prototype
     * @param PrototypeInterface $product The product prototype to register
     * @return void
     */
    public static function registerPrototype(string $key, PrototypeInterface $product): void
    {
        self::getPrototypeRegistry()->register($key, $product);
    }

    /**
     * Clone a registered product prototype by key.
     * 
     * Retrieves the prototype from the registry and creates a new clone.
     * 
     * @param string $key Unique identifier for the registered prototype
     * @return PrototypeInterface A cloned instance of the prototype
     * @throws InvalidArgumentException If the prototype key is not registered
     */
    public static function cloneFromPrototype(string $key): PrototypeInterface
    {
        return self::getPrototypeRegistry()->clone($key);
    }

    /**
     * Get a registered prototype by key.
     * 
     * @param string $key Unique identifier for the prototype
     * @return PrototypeInterface|null The registered prototype or null if not found
     */
    public static function getPrototype(string $key): ?PrototypeInterface
    {
        return self::getPrototypeRegistry()->get($key);
    }

    /**
     * Check if a prototype is registered.
     * 
     * @param string $key Unique identifier for the prototype
     * @return bool True if the prototype is registered, false otherwise
     */
    public static function hasPrototype(string $key): bool
    {
        return self::getPrototypeRegistry()->has($key);
    }

    /**
     * Remove a registered prototype.
     * 
     * @param string $key Unique identifier for the prototype
     * @return void
     */
    public static function removePrototype(string $key): void
    {
        self::getPrototypeRegistry()->remove($key);
    }

    /**
     * Get the prototype registry for advanced management.
     * 
     * @return PrototypeRegistry The shared prototype registry instance
     */
    public static function getRegistryInstance(): PrototypeRegistry
    {
        return self::getPrototypeRegistry();
    }
}
