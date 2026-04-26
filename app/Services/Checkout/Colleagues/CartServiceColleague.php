<?php

declare(strict_types=1);

namespace App\Services\Checkout\Colleagues;

use App\Domain\Cart\CartBundleComposite;

class CartServiceColleague
{
    /**
     * @param array<int,array{product_id:int,price:float|int,quantity:int,type:string}> $cart
     */
    public function prepare(array $cart, string $name = 'Checkout cart'): CartBundleComposite
    {
        return CartBundleComposite::fromSessionCart($cart, $name);
    }
}
