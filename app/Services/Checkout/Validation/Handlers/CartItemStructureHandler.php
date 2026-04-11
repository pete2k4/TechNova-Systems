<?php

declare(strict_types=1);

namespace App\Services\Checkout\Validation\Handlers;

use App\Services\Checkout\Validation\CheckoutValidationContext;
use InvalidArgumentException;

final class CartItemStructureHandler extends CheckoutValidationHandler
{
    private const ALLOWED_PRODUCT_TYPES = ['digital', 'physical'];

    protected function validate(CheckoutValidationContext $context): void
    {
        foreach ($context->cart as $index => $item) {
            if (!is_array($item)) {
                throw new InvalidArgumentException("Cart item {$index} is malformed.");
            }

            if (!isset($item['product_id'])) {
                throw new InvalidArgumentException("Cart item {$index} is missing product_id.");
            }

            if (!isset($item['price']) || (float) $item['price'] <= 0.0) {
                throw new InvalidArgumentException("Cart item {$index} has an invalid price.");
            }

            if (!isset($item['quantity']) || (int) $item['quantity'] <= 0) {
                throw new InvalidArgumentException("Cart item {$index} has an invalid quantity.");
            }

            if (!isset($item['type']) || !in_array($item['type'], self::ALLOWED_PRODUCT_TYPES, true)) {
                throw new InvalidArgumentException("Cart item {$index} has an invalid product type.");
            }
        }
    }
}
