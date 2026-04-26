<?php

declare(strict_types=1);

namespace App\Facades;

/**
 * Value object returned by CheckoutFacade::processCart().
 *
 * Carries the computed pricing summary and payment outcome so the caller
 * (e.g. CheckoutController) only needs to read a single, well-typed object
 * instead of assembling results from multiple subsystem calls.
 */
final class CheckoutResult
{
    public function __construct(
        public readonly float $subtotal,
        public readonly float $discountAmount,
        public readonly float $finalTotal,
        public readonly bool $paymentSuccess,
        public readonly string $paymentMethodName,
        public readonly string $factoryFamilyName,
        public readonly string $factoryClass,
    ) {}
}
