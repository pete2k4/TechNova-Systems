<?php

declare(strict_types=1);

namespace App\Services\Checkout\Validation;

final class CheckoutValidationContext
{
    /**
     * @param array<int|string, array<string, mixed>> $cart
     * @param array{type: string, value: float|int} $discountConfig
     * @param array{type: string, credential: string} $paymentData
     */
    public function __construct(
        public readonly array $cart,
        public readonly array $discountConfig,
        public readonly array $paymentData,
        public readonly string $productType,
    ) {}
}
