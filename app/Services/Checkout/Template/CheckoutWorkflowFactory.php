<?php

declare(strict_types=1);

namespace App\Services\Checkout\Template;

use App\Facades\CheckoutFacade;
use App\Services\Checkout\Validation\CheckoutValidationChain;
use App\Services\Payment\PaymentProcessor;
use App\Services\PriceCalculator;

final class CheckoutWorkflowFactory
{
    /**
     * @param array<int|string, array<string, mixed>> $cart
     */
    public static function fromCart(array $cart): AbstractCheckoutWorkflow
    {
        $validationChain = new CheckoutValidationChain();
        $checkoutFacade = new CheckoutFacade(new PriceCalculator(), new PaymentProcessor());

        foreach ($cart as $item) {
            if (($item['type'] ?? null) === 'physical') {
                return new PhysicalCheckoutWorkflow($validationChain, $checkoutFacade);
            }
        }

        return new DigitalCheckoutWorkflow($validationChain, $checkoutFacade);
    }
}
