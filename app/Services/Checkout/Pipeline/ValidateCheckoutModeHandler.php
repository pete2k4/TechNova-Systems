<?php

declare(strict_types=1);

namespace App\Services\Checkout\Pipeline;

use RuntimeException;

class ValidateCheckoutModeHandler extends AbstractCheckoutHandler
{
    /**
     * @param array<int,array{product_id:int,price:float|int,quantity:int,type:string}> $cart
     * @param array{discount_type:string,discount_value:float|int,payment_type:string,payment_credential:string} $validated
     */
    public function handle(array $cart, array $validated): void
    {
        $supportedPaymentTypes = ['credit_card', 'paypal', 'on_delivery'];

        if (!in_array($validated['payment_type'], $supportedPaymentTypes, true)) {
            throw new RuntimeException('Payment type is not supported.');
        }

        $paymentCredential = trim((string) ($validated['payment_credential'] ?? ''));

        if ($validated['payment_type'] !== 'on_delivery' && $paymentCredential === '') {
            throw new RuntimeException('Payment credential is required.');
        }

        $this->next($cart, $validated);
    }
}
