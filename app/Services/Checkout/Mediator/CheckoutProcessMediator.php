<?php

declare(strict_types=1);

namespace App\Services\Checkout\Mediator;

use App\Facades\CheckoutFacade;
use App\Facades\CheckoutResult;
use App\Services\Checkout\Validation\CheckoutValidationChain;
use App\Services\Checkout\Validation\CheckoutValidationContext;
use App\Services\Payment\PaymentProcessor;
use App\Services\PriceCalculator;

final class CheckoutProcessMediator
{
    public function __construct(
        private readonly CheckoutValidationChain $validationChain = new CheckoutValidationChain(),
        private readonly CheckoutFacade $checkoutFacade = new CheckoutFacade(
            new PriceCalculator(),
            new PaymentProcessor(),
        ),
    ) {}

    /**
     * @param array<int|string, array<string, mixed>> $cart
     * @param array{type: string, value: float|int} $discountConfig
     * @param array{type: string, credential: string} $paymentData
     */
    public function mediateCheckout(array $cart, array $discountConfig, array $paymentData): CheckoutResult
    {
        $primaryProductType = $this->resolvePrimaryProductType($cart);

        $context = new CheckoutValidationContext(
            cart: $cart,
            discountConfig: $discountConfig,
            paymentData: $paymentData,
            productType: $primaryProductType,
        );

        $this->validationChain->validate($context);

        return $this->checkoutFacade->processCart(
            cart: $cart,
            discountConfig: $discountConfig,
            paymentData: $paymentData,
            productType: $primaryProductType,
        );
    }

    /**
     * @param array<int|string, array<string, mixed>> $cart
     */
    private function resolvePrimaryProductType(array $cart): string
    {
        $productTypes = array_unique(array_map(fn(array $item): string => (string) ($item['type'] ?? 'digital'), $cart));

        return in_array('physical', $productTypes, true) ? 'physical' : 'digital';
    }
}
