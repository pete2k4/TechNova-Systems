<?php

declare(strict_types=1);

namespace App\Domain\Cart;

use Traversable;

final class CartBundleComposite implements CartComponentInterface
{
    /**
     * @var array<int,CartComponentInterface>
     */
    private array $children = [];

    public function __construct(private readonly string $name = 'Cart')
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function add(CartComponentInterface $component): self
    {
        $this->children[] = $component;

        return $this;
    }

    /**
     * @return array<int,CartComponentInterface>
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function getTotal(): float
    {
        $total = 0.0;

        foreach ($this as $leaf) {
            $total += $leaf->getTotal();
        }

        return $total;
    }

    public function getQuantity(): int
    {
        $quantity = 0;

        foreach ($this as $leaf) {
            $quantity += $leaf->getQuantity();
        }

        return $quantity;
    }

    public function hasPhysicalProducts(): bool
    {
        foreach ($this as $leaf) {
            if ($leaf->getType() === 'physical') {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<int,array{product_id:int,name:string,price:float,quantity:int,type:string}>
     */
    public function toCartPayload(): array
    {
        $payload = [];

        foreach ($this as $leaf) {
            $payload[] = $leaf->toCartArray();
        }

        return $payload;
    }

    public function getIterator(): Traversable
    {
        return new CartIterator($this->children);
    }

    /**
     * @param array<int|string,mixed> $cart
     */
    public static function fromSessionCart(array $cart, string $name = 'Cart'): self
    {
        $bundle = new self($name);

        foreach ($cart as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $bundle->add(self::componentFromEntry($entry));
        }

        return $bundle;
    }

    /**
     * @param array<int|string,mixed> $entry
     */
    private static function componentFromEntry(array $entry): CartComponentInterface
    {
        if (isset($entry['children']) && is_array($entry['children'])) {
            $bundle = new self((string) ($entry['name'] ?? 'Bundle'));

            foreach ($entry['children'] as $child) {
                if (!is_array($child)) {
                    continue;
                }

                $bundle->add(self::componentFromEntry($child));
            }

            return $bundle;
        }

        return CartItemLeaf::fromArray($entry);
    }
}
