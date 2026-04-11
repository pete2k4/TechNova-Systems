<?php

declare(strict_types=1);

namespace App\Services\Checkout\Mediator;

use App\Facades\CheckoutResult;
use App\Services\Checkout\Template\CheckoutWorkflowFactory;

final class CheckoutProcessMediator
{
    /**
     * @param array<int|string, array<string, mixed>> $cart
     * @param array{type: string, value: float|int} $discountConfig
     * @param array{type: string, credential: string} $paymentData
     */
    public function mediateCheckout(array $cart, array $discountConfig, array $paymentData): CheckoutResult
    {
        return CheckoutWorkflowFactory::fromCart($cart)->execute($cart, $discountConfig, $paymentData);
    }
}
