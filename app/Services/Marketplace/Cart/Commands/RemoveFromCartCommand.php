<?php

declare(strict_types=1);

namespace App\Services\Marketplace\Cart\Commands;

use App\Contracts\CartCommandInterface;
use App\Contracts\CartStoreInterface;

class RemoveFromCartCommand implements CartCommandInterface
{
    /**
     * @var array<int|string, array<string, mixed>>
     */
    private array $previousCart = [];

    public function __construct(
        private readonly CartStoreInterface $cartStore,
        private readonly int $productId,
    ) {}

    public function execute(): void
    {
        $cart = $this->cartStore->getCart();
        $this->previousCart = $cart;

        unset($cart[$this->productId]);
        $this->cartStore->putCart($cart);
    }

    public function undo(): void
    {
        $this->cartStore->putCart($this->previousCart);
    }
}
