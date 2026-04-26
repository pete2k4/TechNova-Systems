<?php

declare(strict_types=1);

namespace App\Services\Checkout\Template;

use App\Services\Checkout\Validation\CheckoutValidationContext;
use InvalidArgumentException;

final class DigitalCheckoutWorkflow extends AbstractCheckoutWorkflow
{
    /**
     * @param array<int|string, array<string, mixed>> $cart
     */
    protected function resolveProductType(array $cart): string
    {
        return 'digital';
    }

    protected function beforeValidation(CheckoutValidationContext $context): void
    {
        foreach ($context->cart as $index => $item) {
            if (($item['type'] ?? null) === 'physical') {
                throw new InvalidArgumentException("Digital workflow cannot process physical product at index {$index}.");
            }
        }
    }
}
