<?php

declare(strict_types=1);

namespace App\Flyweights;

/**
 * Flyweight that stores intrinsic catalog state shared by many order items.
 */
class ProductCatalogFlyweight
{
    public function __construct(
        private readonly int $productId,
        private readonly string $name,
        private readonly string $description,
        private readonly string $type,
        private readonly float $basePrice,
    ) {
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getBasePrice(): float
    {
        return $this->basePrice;
    }

    /**
     * Build order-item data by combining intrinsic state (flyweight)
     * with extrinsic state (quantity and optional override price).
     *
     * @return array<string, float|int|string>
     */
    public function createOrderItemData(int $quantity, ?float $overridePrice = null): array
    {
        $unitPrice = $overridePrice ?? $this->basePrice;

        return [
            'product_id' => $this->productId,
            'product_name' => $this->name,
            'product_type' => $this->type,
            'quantity' => $quantity,
            'price' => $unitPrice,
            'line_total' => $unitPrice * $quantity,
        ];
    }
}
