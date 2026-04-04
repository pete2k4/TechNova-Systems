<?php

declare(strict_types=1);

namespace App\Services\Marketplace\Cart\Commands;

use App\Contracts\CartCommandInterface;
use App\Contracts\CartStoreInterface;
use App\Models\Product;

class AddToCartCommand implements CartCommandInterface
{
    /**
     * @var array<int|string, array<string, mixed>>
     */
    private array $previousCart = [];

    public function __construct(
        private readonly CartStoreInterface $cartStore,
        private readonly Product $product,
        private readonly int $quantity,
    ) {}

    public function execute(): void
    {
        $cart = $this->cartStore->getCart();
        $this->previousCart = $cart;

        $productId = $this->product->id;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $this->quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $this->product->id,
                'name' => $this->product->name,
                'price' => (float) $this->product->price,
                'type' => $this->product->type,
                'quantity' => $this->quantity,
            ];
        }

        $this->cartStore->putCart($cart);
    }

    public function undo(): void
    {
        $this->cartStore->putCart($this->previousCart);
    }
}
