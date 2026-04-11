<?php

declare(strict_types=1);

namespace App\Services\Checkout\Validation\Handlers;

use App\Services\Checkout\Validation\CheckoutValidationContext;
use InvalidArgumentException;

final class DiscountConfigHandler extends CheckoutValidationHandler
{
    private const ALLOWED_DISCOUNT_TYPES = ['percentage', 'fixed'];

    protected function validate(CheckoutValidationContext $context): void
    {
        $type = $context->discountConfig['type'] ?? null;
        $value = $context->discountConfig['value'] ?? null;

        if (!is_string($type) || !in_array($type, self::ALLOWED_DISCOUNT_TYPES, true)) {
            throw new InvalidArgumentException('Invalid discount type.');
        }

        if (!is_numeric($value) || (float) $value < 0.0) {
            throw new InvalidArgumentException('Discount value must be a non-negative number.');
        }

        if ($type === 'percentage' && (float) $value > 100.0) {
            throw new InvalidArgumentException('Percentage discount cannot be greater than 100.');
        }
    }
}
