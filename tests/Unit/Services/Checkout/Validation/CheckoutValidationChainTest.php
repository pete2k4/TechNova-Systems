<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Checkout\Validation;

use App\Services\Checkout\Validation\CheckoutValidationChain;
use App\Services\Checkout\Validation\CheckoutValidationContext;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CheckoutValidationChainTest extends TestCase
{
    public function test_valid_context_passes_through_chain(): void
    {
        $context = new CheckoutValidationContext(
            cart: [
                [
                    'product_id' => 10,
                    'price' => 99.99,
                    'quantity' => 2,
                    'type' => 'physical',
                ],
            ],
            discountConfig: [
                'type' => 'percentage',
                'value' => 10.0,
            ],
            paymentData: [
                'type' => 'credit_card',
                'credential' => '4532015112830366',
            ],
            productType: 'physical',
        );

        (new CheckoutValidationChain())->validate($context);

        $this->assertTrue(true);
    }

    public function test_empty_cart_fails_on_first_handler(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cart is empty.');

        $context = new CheckoutValidationContext(
            cart: [],
            discountConfig: [
                'type' => 'fixed',
                'value' => 5,
            ],
            paymentData: [
                'type' => 'paypal',
                'credential' => 'payer@example.com',
            ],
            productType: 'digital',
        );

        (new CheckoutValidationChain())->validate($context);
    }

    public function test_percentage_discount_over_100_fails(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Percentage discount cannot be greater than 100.');

        $context = new CheckoutValidationContext(
            cart: [
                [
                    'product_id' => 1,
                    'price' => 10,
                    'quantity' => 1,
                    'type' => 'digital',
                ],
            ],
            discountConfig: [
                'type' => 'percentage',
                'value' => 120,
            ],
            paymentData: [
                'type' => 'paypal',
                'credential' => 'payer@example.com',
            ],
            productType: 'digital',
        );

        (new CheckoutValidationChain())->validate($context);
    }
}
