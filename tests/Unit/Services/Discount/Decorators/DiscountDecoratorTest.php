<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Discount\Decorators;

use App\Services\Discount\Decorators\CappedDiscountDecorator;
use App\Services\Discount\Decorators\LoggingDecorator;
use App\Services\Discount\Decorators\LoyaltyBonusDecorator;
use App\Services\Discount\Decorators\MinimumPurchaseDecorator;
use App\Services\Discount\FixedAmountDiscount;
use App\Services\Discount\PercentageDiscount;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class DiscountDecoratorTest extends TestCase
{
    /** @test */
    public function loyalty_bonus_adds_percentage_to_base_discount(): void
    {
        $base = new FixedAmountDiscount(100.00);
        $decorated = new LoyaltyBonusDecorator(10.0, $base);

        // Base discount: $100
        // Loyalty bonus: 10% of $100 = $10
        // Total: $110
        $this->assertEqualsWithDelta(110.00, $decorated->calculate(500.00), 0.001);
    }

    /** @test */
    public function loyalty_bonus_works_with_percentage_discount(): void
    {
        $base = new PercentageDiscount(20.0);  // 20% of price
        $decorated = new LoyaltyBonusDecorator(15.0, $base);  // Plus 15% bonus on that

        // Base: 20% of $100 = $20
        // Loyalty: 15% of $20 = $3
        // Total: $23
        $this->assertEqualsWithDelta(23.00, $decorated->calculate(100.00), 0.001);
    }

    /** @test */
    public function minimum_purchase_applies_discount_when_threshold_met(): void
    {
        $base = new FixedAmountDiscount(20.00);
        $decorated = new MinimumPurchaseDecorator(100.00, $base);

        $this->assertEqualsWithDelta(20.00, $decorated->calculate(150.00), 0.001);
    }

    /** @test */
    public function minimum_purchase_returns_zero_when_below_threshold(): void
    {
        $base = new FixedAmountDiscount(20.00);
        $decorated = new MinimumPurchaseDecorator(100.00, $base);

        $this->assertEqualsWithDelta(0.00, $decorated->calculate(50.00), 0.001);
    }

    /** @test */
    public function minimum_purchase_applies_at_exact_threshold(): void
    {
        $base = new FixedAmountDiscount(20.00);
        $decorated = new MinimumPurchaseDecorator(100.00, $base);

        $this->assertEqualsWithDelta(20.00, $decorated->calculate(100.00), 0.001);
    }

    /** @test */
    public function capped_discount_limits_discount_amount(): void
    {
        $base = new PercentageDiscount(50.0);  // 50% of $200 = $100
        $decorated = new CappedDiscountDecorator(50.00, $base);

        // Without cap: $100, with cap: $50
        $this->assertEqualsWithDelta(50.00, $decorated->calculate(200.00), 0.001);
    }

    /** @test */
    public function capped_discount_passes_through_if_below_cap(): void
    {
        $base = new FixedAmountDiscount(20.00);
        $decorated = new CappedDiscountDecorator(50.00, $base);

        // $20 is below $50 cap, so it passes through
        $this->assertEqualsWithDelta(20.00, $decorated->calculate(100.00), 0.001);
    }

    /** @test */
    public function logging_decorator_delegates_to_wrapped_discount(): void
    {
        $base = new FixedAmountDiscount(30.00);
        $decorated = new LoggingDecorator(new NullLogger(), $base);

        // Should calculate same as base
        $this->assertEqualsWithDelta(30.00, $decorated->calculate(100.00), 0.001);
    }

    /** @test */
    public function stacking_decorators_builds_complex_behavior(): void
    {
        // Start with base: 10% discount
        $discount = new PercentageDiscount(10.0);

        // Add loyalty bonus: 20% of the discount
        $discount = new LoyaltyBonusDecorator(20.0, $discount);

        // Minimum purchase: $100
        $discount = new MinimumPurchaseDecorator(100.00, $discount);

        // Cap at $20
        $discount = new CappedDiscountDecorator(20.00, $discount);

        // Test at threshold with enough discount
        // 10% of $300 = $30, loyalty +20% = $36, capped at $20 = $20
        $this->assertEqualsWithDelta(20.00, $discount->calculate(300.00), 0.001);

        // Test below threshold
        $this->assertEqualsWithDelta(0.00, $discount->calculate(50.00), 0.001);
    }

    /** @test */
    public function decorator_preserves_wrapped_discount_reference(): void
    {
        $base = new FixedAmountDiscount(10.00);
        $decorated = new LoyaltyBonusDecorator(5.0, $base);

        $this->assertSame($base, $decorated->getWrappedDiscount());
    }

    /** @test */
    public function decorator_accessor_methods_work(): void
    {
        $base = new FixedAmountDiscount(10.00);
        $loyalty = new LoyaltyBonusDecorator(15.0, $base);
        $minimum = new MinimumPurchaseDecorator(100.00, $loyalty);
        $capped = new CappedDiscountDecorator(50.00, $minimum);

        $this->assertEqualsWithDelta(15.0, $loyalty->getLoyaltyBonusPercentage(), 0.001);
        $this->assertEqualsWithDelta(100.00, $minimum->getMinimumPrice(), 0.001);
        $this->assertEqualsWithDelta(50.00, $capped->getMaximumDiscount(), 0.001);
    }

    /** @test */
    public function decorator_with_logging_still_calculates_correctly(): void
    {
        $base = new PercentageDiscount(25.0);
        $loyalty = new LoyaltyBonusDecorator(10.0, $base);
        $logged = new LoggingDecorator(new NullLogger(), $loyalty);

        // 25% of $100 = $25, loyalty +10% = $27.50
        $this->assertEqualsWithDelta(27.50, $logged->calculate(100.00), 0.001);
    }

    /** @test */
    public function minimum_purchase_before_loyalty_bonus(): void
    {
        $base = new FixedAmountDiscount(50.00);
        $minimum = new MinimumPurchaseDecorator(200.00, $base);
        $loyalty = new LoyaltyBonusDecorator(10.0, $minimum);

        // Meets minimum: base $50, loyalty +$5 = $55
        $this->assertEqualsWithDelta(55.00, $loyalty->calculate(300.00), 0.001);

        // Below minimum: base $0, loyalty on $0 = $0
        $this->assertEqualsWithDelta(0.00, $loyalty->calculate(100.00), 0.001);
    }
}
