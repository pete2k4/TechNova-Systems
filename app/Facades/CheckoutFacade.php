<?php

declare(strict_types=1);

namespace App\Facades;

use App\Factories\CommerceFactorySelector;
use App\Services\Payment\PaymentProcessor;
use App\Services\PriceCalculator;

class CheckoutFacade
{
    public function __construct(
        private readonly PriceCalculator $priceCalculator,
        private readonly PaymentProcessor $paymentProcessor,
    ) {}

    /**
     * Process the commerce portion of a checkout.
     *
     * Orchestrates:
     *   1. Cart subtotal calculation
     *   2. Discount application  (via PriceCalculator)
     *   3. Payment processing    (via PaymentProcessor)
     *   4. Factory family lookup (via CommerceFactorySelector)
     *
     * @param array<int|string, array{price: float|int, quantity: int}> $cart
     * @param array{type: string, value: float|int}                     $discountConfig
     * @param array{type: string, credential: string}                   $paymentData
     * @param string                                                     $productType 'digital'|'physical'
     * @return CheckoutResult
     */
    public function processCart(
        array $cart,
        array $discountConfig,
        array $paymentData,
        string $productType = 'digital',
    ): CheckoutResult {
        // 1. Calculate cart subtotal from raw items
        $subtotal = (float) array_sum(
            array_map(
                fn (array $item): float => (float) $item['price'] * (int) $item['quantity'],
                $cart,
            )
        );

        // 2. Apply discount —  PriceCalculator delegates to DiscountFactory internally
        $finalTotal = $this->priceCalculator->calculateFromConfig($subtotal, $discountConfig);
        $discountAmount = $subtotal - $finalTotal;

        // 3. Process payment through the payment subsystem (supports all adapters)
        $paymentMethod = $this->paymentProcessor->processPaymentFromConfig($paymentData, $finalTotal);
        $paymentSuccess = $paymentMethod;

        // 4. Resolve factory family for metadata the controller needs in its view
        $factory = CommerceFactorySelector::getFactory($productType);

        return new CheckoutResult(
            subtotal: $subtotal,
            discountAmount: $discountAmount,
            finalTotal: $finalTotal,
            paymentSuccess: $paymentSuccess,
            paymentMethodName: $paymentData['type'],
            factoryFamilyName: $factory->getFamilyName(),
            factoryClass: class_basename($factory::class),
        );
    }
}
