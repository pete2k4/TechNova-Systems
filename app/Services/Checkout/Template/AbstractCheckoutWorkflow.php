<?php

declare(strict_types=1);

namespace App\Services\Checkout\Template;

use App\Facades\CheckoutFacade;
use App\Facades\CheckoutResult;
use App\Services\Checkout\Validation\CheckoutValidationChain;
use App\Services\Checkout\Validation\CheckoutValidationContext;

abstract class AbstractCheckoutWorkflow
{
    public function __construct(
        protected readonly CheckoutValidationChain $validationChain,
        protected readonly CheckoutFacade $checkoutFacade,
    ) {}

    /**
     * @param array<int|string, array<string, mixed>> $cart
     * @param array{type: string, value: float|int} $discountConfig
     * @param array{type: string, credential: string} $paymentData
     */
    final public function execute(array $cart, array $discountConfig, array $paymentData): CheckoutResult
    {
        $productType = $this->resolveProductType($cart);

        $context = new CheckoutValidationContext(
            cart: $cart,
            discountConfig: $discountConfig,
            paymentData: $paymentData,
            productType: $productType,
        );

        $this->beforeValidation($context);
        $this->validationChain->validate($context);
        $this->afterValidation($context);

        $result = $this->checkoutFacade->processCart(
            cart: $cart,
            discountConfig: $discountConfig,
            paymentData: $paymentData,
            productType: $productType,
        );

        return $this->afterCheckout($result);
    }

    /**
     * @param array<int|string, array<string, mixed>> $cart
     */
    abstract protected function resolveProductType(array $cart): string;

    protected function beforeValidation(CheckoutValidationContext $context): void
    {
    }

    protected function afterValidation(CheckoutValidationContext $context): void
    {
    }

    protected function afterCheckout(CheckoutResult $result): CheckoutResult
    {
        return $result;
    }
}
