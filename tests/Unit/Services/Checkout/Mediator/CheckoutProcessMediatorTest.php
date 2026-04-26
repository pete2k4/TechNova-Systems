<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Checkout\Mediator;

use App\Services\Checkout\Mediator\CheckoutProcessMediator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CheckoutProcessMediatorTest extends TestCase
{
    public function test_mediator_processes_physical_cart_with_chain_and_facade(): void
    {
        $mediator = new CheckoutProcessMediator();

        $result = $mediator->mediateCheckout(
            cart: [
                [
                    'product_id' => 10,
                    'price' => 100.0,
                    'quantity' => 2,
                    'type' => 'physical',
                ],
            ],
            discountConfig: [
                'type' => 'fixed',
                'value' => 20.0,
            ],
            paymentData: [
                'type' => 'credit_card',
                'credential' => '4532015112830366',
            ],
        );

        $this->assertTrue($result->paymentSuccess);
        $this->assertStringContainsStringIgnoringCase('physical', $result->factoryFamilyName);
        $this->assertEqualsWithDelta(200.0, $result->subtotal, 0.001);
        $this->assertEqualsWithDelta(180.0, $result->finalTotal, 0.001);
    }

    public function test_mediator_resolves_digital_factory_when_no_physical_products_exist(): void
    {
        $mediator = new CheckoutProcessMediator();

        $result = $mediator->mediateCheckout(
            cart: [
                [
                    'product_id' => 1,
                    'price' => 50.0,
                    'quantity' => 1,
                    'type' => 'digital',
                ],
            ],
            discountConfig: [
                'type' => 'percentage',
                'value' => 10.0,
            ],
            paymentData: [
                'type' => 'paypal',
                'credential' => 'payer@example.com',
            ],
        );

        $this->assertStringContainsStringIgnoringCase('digital', $result->factoryFamilyName);
        $this->assertEqualsWithDelta(50.0, $result->subtotal, 0.001);
        $this->assertEqualsWithDelta(45.0, $result->finalTotal, 0.001);
    }

    public function test_mediator_stops_when_validation_chain_fails(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cart is empty.');

        $mediator = new CheckoutProcessMediator();

        $mediator->mediateCheckout(
            cart: [],
            discountConfig: [
                'type' => 'fixed',
                'value' => 5,
            ],
            paymentData: [
                'type' => 'paypal',
                'credential' => 'payer@example.com',
            ],
        );
    }
}
