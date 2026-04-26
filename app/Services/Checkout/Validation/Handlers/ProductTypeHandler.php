<?php

declare(strict_types=1);

namespace App\Services\Checkout\Validation\Handlers;

use App\Services\Checkout\Validation\CheckoutValidationContext;
use InvalidArgumentException;

final class ProductTypeHandler extends CheckoutValidationHandler
{
    private const ALLOWED_PRODUCT_TYPES = ['digital', 'physical'];

    protected function validate(CheckoutValidationContext $context): void
    {
        if (!in_array($context->productType, self::ALLOWED_PRODUCT_TYPES, true)) {
            throw new InvalidArgumentException('Unsupported primary product type.');
        }
    }
}
