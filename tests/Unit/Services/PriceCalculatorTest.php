<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\Discount\FixedAmountDiscount;
use App\Services\Discount\PercentageDiscount;
use App\Services\PriceCalculator;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class PriceCalculatorTest extends TestCase
{
    private PriceCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new PriceCalculator();
    }

    /** @test */
    public function it_calculates_discounted_price_with_fixed_discount(): void
    {
        $result = $this->calculator->calculateDiscountedPrice(100.00, 'fixed', 20.00);

        $this->assertEqualsWithDelta(80.00, $result, 0.001);
    }

    /** @test */
    public function it_calculates_discounted_price_with_percentage_discount(): void
    {
        $result = $this->calculator->calculateDiscountedPrice(100.00, 'percentage', 15.0);

        $this->assertEqualsWithDelta(85.00, $result, 0.001);
    }

    /** @test */
    public function it_applies_discount_to_a_discount_object(): void
    {
        $discount = new FixedAmountDiscount(30.00);
        $result = $this->calculator->applyDiscount(150.00, $discount);

        $this->assertEqualsWithDelta(120.00, $result, 0.001);
    }

    /** @test */
    public function it_never_returns_negative_price(): void
    {
        $discount = new FixedAmountDiscount(200.00);
        $result = $this->calculator->applyDiscount(50.00, $discount);

        $this->assertEqualsWithDelta(0.00, $result, 0.001);
    }

    /** @test */
    public function it_applies_loyalty_bonus_decorator_from_config(): void
    {
        $config = [
            'type' => 'fixed',
            'value' => 100.00,
            'decorators' => [
                'loyalty_bonus' => 10.0,  // 10% bonus on top
            ],
        ];

        // Base: $100, loyalty +10% = $110 discount, leaving $80
        $result = $this->calculator->calculateFromConfig(190.00, $config);

        $this->assertEqualsWithDelta(80.00, $result, 0.001);
    }

    /** @test */
    public function it_applies_minimum_purchase_decorator_from_config(): void
    {
        $config = [
            'type' => 'fixed',
            'value' => 50.00,
            'decorators' => [
                'minimum_purchase' => 100.00,
            ],
        ];

        // Meets minimum
        $resultMeets = $this->calculator->calculateFromConfig(200.00, $config);
        $this->assertEqualsWithDelta(150.00, $resultMeets, 0.001);

        // Below minimum
        $resultBelow = $this->calculator->calculateFromConfig(50.00, $config);
        $this->assertEqualsWithDelta(50.00, $resultBelow, 0.001);
    }

    /** @test */
    public function it_applies_cap_decorator_from_config(): void
    {
        $config = [
            'type' => 'percentage',
            'value' => 50.0,
            'decorators' => [
                'cap' => 25.00,
            ],
        ];

        // 50% of $200 = $100, but capped at $25
        $result = $this->calculator->calculateFromConfig(200.00, $config);

        $this->assertEqualsWithDelta(175.00, $result, 0.001);
    }

    /** @test */
    public function it_stacks_multiple_decorators_in_order(): void
    {
        $config = [
            'type' => 'fixed',
            'value' => 50.00,
            'decorators' => [
                'loyalty_bonus' => 20.0,        // +20% on base
                'minimum_purchase' => 100.00,   // Requires $100
                'cap' => 80.00,                 // Max discount $80
            ],
        ];

        // Base: $50, loyalty +20% = $60, meets minimum, under cap = $60 discount
        // $200 - $60 = $140
        $result = $this->calculator->calculateFromConfig(200.00, $config);

        $this->assertEqualsWithDelta(140.00, $result, 0.001);
    }

    /** @test */
    public function it_stacks_decorators_respecting_order(): void
    {
        // Minimum purchase returns 0 when not met, so subsequent decorators don't matter
        $config = [
            'type' => 'fixed',
            'value' => 50.00,
            'decorators' => [
                'minimum_purchase' => 200.00,   // Applied first - blocks discount
                'loyalty_bonus' => 50.0,        // Applied second - but discount is 0
            ],
        ];

        // Below minimum, so discount = 0
        $result = $this->calculator->calculateFromConfig(100.00, $config);

        $this->assertEqualsWithDelta(100.00, $result, 0.001);
    }

    /** @test */
    public function it_applies_logging_decorator_from_config(): void
    {
        $config = [
            'type' => 'percentage',
            'value' => 10.0,
            'decorators' => [
                'logging' => true,
            ],
        ];

        // Should work the same, just with logging
        $result = $this->calculator->calculateFromConfig(100.00, $config);

        $this->assertEqualsWithDelta(90.00, $result, 0.001);
    }

    /** @test */
    public function it_applies_decorator_stack_directly_to_discount_object(): void
    {
        $discount = new PercentageDiscount(20.0);

        $decoratorConfig = [
            'loyalty_bonus' => 5.0,
            'cap' => 15.00,
        ];

        $decorated = $this->calculator->applyDecoratorStack($discount, $decoratorConfig);
        $result = $decorated->calculate(100.00);

        // 20% = $20, loyalty +5% = $21, capped at $15
        $this->assertEqualsWithDelta(15.00, $result, 0.001);
    }

    /** @test */
    public function it_allows_custom_logger_for_logging_decorator(): void
    {
        $discount = new FixedAmountDiscount(25.00);

        $decoratorConfig = [
            'logging' => true,
        ];

        $logger = new NullLogger();
        $decorated = $this->calculator->applyDecoratorStack($discount, $decoratorConfig, $logger);
        $result = $decorated->calculate(100.00);

        $this->assertEqualsWithDelta(25.00, $result, 0.001);
    }

    /** @test */
    public function it_ignores_empty_decorator_config(): void
    {
        $config = [
            'type' => 'fixed',
            'value' => 30.00,
            'decorators' => [],
        ];

        $result = $this->calculator->calculateFromConfig(100.00, $config);

        $this->assertEqualsWithDelta(70.00, $result, 0.001);
    }

    /** @test */
    public function it_ignores_missing_decorator_config(): void
    {
        $config = [
            'type' => 'percentage',
            'value' => 10.0,
            // No 'decorators' key
        ];

        $result = $this->calculator->calculateFromConfig(200.00, $config);

        $this->assertEqualsWithDelta(180.00, $result, 0.001);
    }

    /** @test */
    public function it_builds_complex_discount_with_all_decorators(): void
    {
        $config = [
            'type' => 'percentage',
            'value' => 25.0,
            'decorators' => [
                'loyalty_bonus' => 10.0,        // +10% loyalty
                'minimum_purchase' => 150.00,   // Requires $150
                'cap' => 40.00,                 // Max $40
                'logging' => true,              // Log it
            ],
        ];

        // 25% of $300 = $75, loyalty +10% = $82.50, meets minimum, capped at $40
        $result = $this->calculator->calculateFromConfig(300.00, $config);

        $this->assertEqualsWithDelta(260.00, $result, 0.001);
    }
}
