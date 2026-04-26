<?php

declare(strict_types=1);

namespace App\Domain\Cart;

use ArrayIterator;
use Traversable;

final class CartItemLeaf implements CartComponentInterface
{
    public function __construct(
        private readonly int $productId,
        private readonly string $name,
        private readonly float $price,
        private readonly int $quantity,
        private readonly string $type,
    ) {
    }

    /**
     * @param array{product_id:int,price:float|int,quantity:int,type:string,name?:string} $item
     */
    public static function fromArray(array $item): self
    {
        return new self(
            productId: (int) $item['product_id'],
            name: (string) ($item['name'] ?? 'Unnamed product'),
            price: (float) $item['price'],
            quantity: (int) $item['quantity'],
            type: (string) $item['type'],
        );
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTotal(): float
    {
        return $this->price * $this->quantity;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return array{product_id:int,name:string,price:float,quantity:int,type:string}
     */
    public function toCartArray(): array
    {
        return [
            'product_id' => $this->productId,
            'name' => $this->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'type' => $this->type,
        ];
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator([$this]);
    }
}
