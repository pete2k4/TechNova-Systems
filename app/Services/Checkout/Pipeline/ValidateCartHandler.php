<?php

declare(strict_types=1);

namespace App\Services\Checkout\Pipeline;

use RuntimeException;

class ValidateCartHandler extends AbstractCheckoutHandler
{
    /**
     * @param array<int,array{product_id:int,price:float|int,quantity:int,type:string}> $cart
     * @param array{discount_type:string,discount_value:float|int,payment_type:string,payment_credential:string} $validated
     */
    public function handle(array $cart, array $validated): void
    {
        if ($cart === []) {
            throw new RuntimeException('Cart is empty');
        }

        foreach ($cart as $item) {
            if (!isset($item['product_id'], $item['price'], $item['quantity'], $item['type'])) {
                throw new RuntimeException('Cart item payload is invalid.');
            }

            if ((int) $item['quantity'] <= 0) {
                throw new RuntimeException('Cart item quantity must be greater than zero.');
            }

            if ((float) $item['price'] < 0) {
                throw new RuntimeException('Cart item price cannot be negative.');
            }
        }

        $this->next($cart, $validated);
    }
}
