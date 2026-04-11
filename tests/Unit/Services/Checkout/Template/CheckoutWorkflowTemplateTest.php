<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Checkout\Template;

use App\Services\Checkout\Mediator\CheckoutProcessMediator;
use App\Services\Checkout\Template\CheckoutWorkflowFactory;
use App\Services\Checkout\Template\DigitalCheckoutWorkflow;
use App\Services\Checkout\Template\PhysicalCheckoutWorkflow;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CheckoutWorkflowTemplateTest extends TestCase
{
    public function test_factory_selects_digital_workflow_for_digital_only_cart(): void
    {
        $workflow = CheckoutWorkflowFactory::fromCart([
            [
                'product_id' => 1,
                'price' => 49.99,
                'quantity' => 1,
                'type' => 'digital',
            ],
        ]);

        $this->assertInstanceOf(DigitalCheckoutWorkflow::class, $workflow);
    }

    public function test_factory_selects_physical_workflow_when_cart_contains_physical_item(): void
    {
        $workflow = CheckoutWorkflowFactory::fromCart([
            [
                'product_id' => 1,
                'price' => 49.99,
                'quantity' => 1,
                'type' => 'digital',
            ],
            [
                'product_id' => 2,
                'price' => 129.99,
                'quantity' => 1,
                'type' => 'physical',
            ],
        ]);

        $this->assertInstanceOf(PhysicalCheckoutWorkflow::class, $workflow);
    }

    public function test_digital_workflow_throws_if_physical_item_slips_in(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Digital workflow cannot process physical product at index 0.');

        $workflow = CheckoutWorkflowFactory::fromCart([
            [
                'product_id' => 1,
                'price' => 10,
                'quantity' => 1,
                'type' => 'digital',
            ],
        ]);

        $workflow->execute(
            [
                [
                    'product_id' => 1,
                    'price' => 10,
                    'quantity' => 1,
                    'type' => 'physical',
                ],
            ],
            ['type' => 'fixed', 'value' => 2],
            ['type' => 'paypal', 'credential' => 'payer@example.com'],
        );
    }

    public function test_mediator_uses_template_workflow_to_process_checkout(): void
    {
        $mediator = new CheckoutProcessMediator();

        $result = $mediator->mediateCheckout(
            cart: [
                [
                    'product_id' => 2,
                    'price' => 200.0,
                    'quantity' => 1,
                    'type' => 'physical',
                ],
            ],
            discountConfig: [
                'type' => 'percentage',
                'value' => 10,
            ],
            paymentData: [
                'type' => 'credit_card',
                'credential' => '4532015112830366',
            ],
        );

        $this->assertStringContainsStringIgnoringCase('physical', $result->factoryFamilyName);
        $this->assertTrue($result->paymentSuccess);
        $this->assertEqualsWithDelta(180.0, $result->finalTotal, 0.001);
    }
}
