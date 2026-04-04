<?php

declare(strict_types=1);

namespace App\Services\Marketplace\Cart;

use App\Contracts\CartStoreInterface;

class CartSessionStore implements CartStoreInterface
{
    public function getCart(): array
    {
        return (array) session()->get('cart', []);
    }

    public function putCart(array $cart): void
    {
        session()->put('cart', $cart);
    }

    public function clearCart(): void
    {
        session()->forget('cart');
    }
}
