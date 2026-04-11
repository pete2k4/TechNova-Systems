<?php

declare(strict_types=1);

namespace App\Services\Checkout\Validation;

use App\Services\Checkout\Validation\Handlers\CartItemStructureHandler;
use App\Services\Checkout\Validation\Handlers\CartNotEmptyHandler;
use App\Services\Checkout\Validation\Handlers\CheckoutValidationHandler;
use App\Services\Checkout\Validation\Handlers\DiscountConfigHandler;
use App\Services\Checkout\Validation\Handlers\PaymentDataHandler;
use App\Services\Checkout\Validation\Handlers\ProductTypeHandler;

final class CheckoutValidationChain
{
    private readonly CheckoutValidationHandler $rootHandler;

    public function __construct(?CheckoutValidationHandler $rootHandler = null)
    {
        $this->rootHandler = $rootHandler ?? $this->defaultChain();
    }

    public function validate(CheckoutValidationContext $context): void
    {
        $this->rootHandler->handle($context);
    }

    private function defaultChain(): CheckoutValidationHandler
    {
        $cartNotEmpty = new CartNotEmptyHandler();
        $cartStructure = new CartItemStructureHandler();
        $discount = new DiscountConfigHandler();
        $payment = new PaymentDataHandler();
        $productType = new ProductTypeHandler();

        $cartNotEmpty
            ->setNext($cartStructure)
            ->setNext($discount)
            ->setNext($payment)
            ->setNext($productType);

        return $cartNotEmpty;
    }
}
