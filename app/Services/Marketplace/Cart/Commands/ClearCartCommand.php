<?php

declare(strict_types=1);

namespace App\Services\Marketplace\Cart\Commands;

use App\Contracts\CartCommandInterface;
use App\Contracts\CartStoreInterface;

class ClearCartCommand implements CartCommandInterface
{
    /**
     * @var array<int|string, array<string, mixed>>
     */
    private array $previousCart = [];

    public function __construct(
        private readonly CartStoreInterface $cartStore,
    ) {}

    public function execute(): void
    {
        $this->previousCart = $this->cartStore->getCart();
        $this->cartStore->clearCart();
    }

    public function undo(): void
    {
        $this->cartStore->putCart($this->previousCart);
    }
}
