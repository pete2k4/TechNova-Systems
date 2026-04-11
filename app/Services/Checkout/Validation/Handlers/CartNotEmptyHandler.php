<?php

declare(strict_types=1);

namespace App\Services\Checkout\Validation\Handlers;

use App\Services\Checkout\Validation\CheckoutValidationContext;
use InvalidArgumentException;

final class CartNotEmptyHandler extends CheckoutValidationHandler
{
    protected function validate(CheckoutValidationContext $context): void
    {
        if ($context->cart === []) {
            throw new InvalidArgumentException('Cart is empty.');
        }
    }
}
