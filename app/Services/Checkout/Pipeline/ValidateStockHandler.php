<?php

declare(strict_types=1);

namespace App\Services\Checkout\Pipeline;

use App\Models\Product;
use RuntimeException;

class ValidateStockHandler extends AbstractCheckoutHandler
{
    /**
     * @param array<int,array{product_id:int,price:float|int,quantity:int,type:string}> $cart
     * @param array{discount_type:string,discount_value:float|int,payment_type:string,payment_credential:string} $validated
     */
    public function handle(array $cart, array $validated): void
    {
        foreach ($cart as $item) {
            $product = Product::query()->find((int) $item['product_id']);

            if ($product === null) {
                throw new RuntimeException('One or more products no longer exist.');
            }

            if (!$product->is_active) {
                throw new RuntimeException('One or more products are not active.');
            }

            if ($product->isPhysical()) {
                $availableStock = (int) ($product->stock ?? 0);
                if ($availableStock < (int) $item['quantity']) {
                    throw new RuntimeException('Insufficient stock for product: ' . $product->name);
                }
            }
        }

        $this->next($cart, $validated);
    }
}
