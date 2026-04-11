<?php

declare(strict_types=1);

namespace App\Services\Checkout\Validation\Handlers;

use App\Services\Checkout\Validation\CheckoutValidationContext;
use InvalidArgumentException;

final class PaymentDataHandler extends CheckoutValidationHandler
{
    private const ALLOWED_PAYMENT_TYPES = ['credit_card', 'paypal'];

    protected function validate(CheckoutValidationContext $context): void
    {
        $type = $context->paymentData['type'] ?? null;
        $credential = $context->paymentData['credential'] ?? null;

        if (!is_string($type) || !in_array($type, self::ALLOWED_PAYMENT_TYPES, true)) {
            throw new InvalidArgumentException('Invalid payment type.');
        }

        if (!is_string($credential) || trim($credential) === '') {
            throw new InvalidArgumentException('Payment credential is required.');
        }
    }
}
