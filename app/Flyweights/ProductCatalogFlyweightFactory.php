<?php

declare(strict_types=1);

namespace App\Flyweights;

use App\Models\Product;
use InvalidArgumentException;

/**
 * Manages the flyweight pool of shared product catalog data.
 */
class ProductCatalogFlyweightFactory
{
    /** @var array<int, ProductCatalogFlyweight> */
    private static array $pool = [];

    public function getByProductId(int $productId): ProductCatalogFlyweight
    {
        if (!isset(self::$pool[$productId])) {
            $product = Product::query()->find($productId);

            if ($product === null) {
                throw new InvalidArgumentException("Unknown product ID: {$productId}");
            }

            self::$pool[$productId] = new ProductCatalogFlyweight(
                productId: $product->id,
                name: (string) $product->name,
                description: (string) ($product->description ?? ''),
                type: (string) $product->type,
                basePrice: (float) $product->price,
            );
        }

        return self::$pool[$productId];
    }

    public static function reset(): void
    {
        self::$pool = [];
    }

    public function count(): int
    {
        return count(self::$pool);
    }
}
