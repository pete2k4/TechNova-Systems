<?php

declare(strict_types=1);

namespace App\Services\Checkout\Template;

use App\Services\Checkout\Validation\CheckoutValidationContext;
use InvalidArgumentException;

final class PhysicalCheckoutWorkflow extends AbstractCheckoutWorkflow
{
    /**
     * @param array<int|string, array<string, mixed>> $cart
     */
    protected function resolveProductType(array $cart): string
    {
        return 'physical';
    }

    protected function beforeValidation(CheckoutValidationContext $context): void
    {
        $hasPhysical = false;

        foreach ($context->cart as $item) {
            if (($item['type'] ?? null) === 'physical') {
                $hasPhysical = true;
                break;
            }
        }

        if (!$hasPhysical) {
            throw new InvalidArgumentException('Physical workflow requires at least one physical product.');
        }
    }
}
