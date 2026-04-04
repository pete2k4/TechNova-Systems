<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Marketplace\Cart\Commands;

use App\Contracts\CartStoreInterface;

class InMemoryCartStore implements CartStoreInterface
{
    /**
     * @param array<int|string, array<string, mixed>> $cart
     */
    public function __construct(private array $cart = [])
    {
    }

    public function getCart(): array
    {
        return $this->cart;
    }

    public function putCart(array $cart): void
    {
        $this->cart = $cart;
    }

    public function clearCart(): void
    {
        $this->cart = [];
    }
}
