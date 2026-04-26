<?php

declare(strict_types=1);

namespace App\Services\Checkout\Pipeline;

use RuntimeException;

class ValidateDiscountHandler extends AbstractCheckoutHandler
{
    /**
     * @param array<int,array{product_id:int,price:float|int,quantity:int,type:string}> $cart
     * @param array{discount_type:string,discount_value:float|int,payment_type:string,payment_credential:string} $validated
     */
    public function handle(array $cart, array $validated): void
    {
        $supportedDiscountTypes = ['percentage', 'fixed'];

        if (!in_array($validated['discount_type'], $supportedDiscountTypes, true)) {
            throw new RuntimeException('Discount type is not supported.');
        }

        if ((float) $validated['discount_value'] < 0) {
            throw new RuntimeException('Discount value must be greater than or equal to zero.');
        }

        $this->next($cart, $validated);
    }
}
