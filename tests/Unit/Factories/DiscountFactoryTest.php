<?php

declare(strict_types=1);

namespace Tests\Unit\Factories;

use App\Contracts\DiscountInterface;
use App\Factories\DiscountFactory;
use App\Services\Discount\CompositeDiscount;
use App\Services\Discount\FixedAmountDiscount;
use App\Services\Discount\PercentageDiscount;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DiscountFactoryTest extends TestCase
{
    /**
     * Test creating fixed amount discount
     */
    public function testCreateFixedAmountDiscount(): void
    {
        $discount = DiscountFactory::create('fixed', 10.0);

        $this->assertInstanceOf(DiscountInterface::class, $discount);
        $this->assertInstanceOf(FixedAmountDiscount::class, $discount);
    }

    /**
     * Test creating percentage discount
     */
    public function testCreatePercentageDiscount(): void
    {
        $discount = DiscountFactory::create('percentage', 15.0);

        $this->assertInstanceOf(DiscountInterface::class, $discount);
        $this->assertInstanceOf(PercentageDiscount::class, $discount);
    }

    /**
     * Test create fixed amount discount directly
     */
    public function testCreateFixedAmountDirectly(): void
    {
        $discount = DiscountFactory::createFixedAmountDiscount(25.0);

        $this->assertInstanceOf(FixedAmountDiscount::class, $discount);
    }

    /**
     * Test create percentage discount directly
     */
    public function testCreatePercentageDirectly(): void
    {
        $discount = DiscountFactory::createPercentageDiscount(20.0);

        $this->assertInstanceOf(PercentageDiscount::class, $discount);
    }

    /**
     * Test case insensitivity
     */
    public function testCreateIsCaseInsensitive(): void
    {
        $discount1 = DiscountFactory::create('FIXED', 10);
        $discount2 = DiscountFactory::create('Fixed', 10);

        $this->assertInstanceOf(FixedAmountDiscount::class, $discount1);
        $this->assertInstanceOf(FixedAmountDiscount::class, $discount2);
    }

    /**
     * Test negative value throws exception
     */
    public function testNegativeValueThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Discount value cannot be negative');

        DiscountFactory::create('fixed', -5.0);
    }

    /**
     * Test percentage > 100 throws exception
     */
    public function testPercentageOver100ThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Percentage discount must be between 0 and 100');

        DiscountFactory::create('percentage', 105.0);
    }

    /**
     * Test percentage < 0 throws exception
     */
    public function testPercentageUnder0ThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Percentage discount must be between 0 and 100');

        DiscountFactory::createPercentageDiscount(-5.0);
    }

    /**
     * Test unknown type throws exception
     */
    public function testUnknownTypeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown discount type: coupon');

        DiscountFactory::create('coupon', 10.0);
    }

    /**
     * Test valid percentage boundaries
     */
    public function testValidPercentageBoundaries(): void
    {
        $discount0 = DiscountFactory::create('percentage', 0);
        $discount100 = DiscountFactory::create('percentage', 100);

        $this->assertInstanceOf(PercentageDiscount::class, $discount0);
        $this->assertInstanceOf(PercentageDiscount::class, $discount100);
    }

    /**
     * Test from config array
     */
    public function testFromConfigArray(): void
    {
        $config = ['type' => 'percentage', 'value' => 15];
        $discount = DiscountFactory::fromConfig($config);

        $this->assertInstanceOf(PercentageDiscount::class, $discount);
    }

    /**
     * Test from config missing type
     */
    public function testFromConfigMissingType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing discount type');

        DiscountFactory::fromConfig(['value' => 10]);
    }

    /**
     * Test from config missing value
     */
    public function testFromConfigMissingValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing discount value');

        DiscountFactory::fromConfig(['type' => 'fixed']);
    }

    /**
     * Test constants are available
     */
    public function testConstantsAvailable(): void
    {
        $this->assertEquals('fixed', DiscountFactory::FIXED_AMOUNT);
        $this->assertEquals('percentage', DiscountFactory::PERCENTAGE);
    }

    /**
     * createComposite returns a CompositeDiscount implementing DiscountInterface
     */
    public function testCreateCompositeReturnsCompositeDiscount(): void
    {
        $composite = DiscountFactory::createComposite([
            ['type' => 'fixed', 'value' => 10.0],
            ['type' => 'percentage', 'value' => 20.0],
        ]);

        $this->assertInstanceOf(DiscountInterface::class, $composite);
        $this->assertInstanceOf(CompositeDiscount::class, $composite);
        $this->assertCount(2, $composite->getDiscounts());
    }

    /**
     * createComposite with a single config behaves like that leaf
     */
    public function testCreateCompositeSingleDiscount(): void
    {
        $composite = DiscountFactory::createComposite([
            ['type' => 'fixed', 'value' => 25.0],
        ]);

        $this->assertInstanceOf(CompositeDiscount::class, $composite);
        $this->assertSame(25.0, $composite->calculate(100.0));
    }

    /**
     * createComposite applies children sequentially through calculate()
     *
     * 10% of $200 = $20 → remaining $180, then $30 off = $30 → total $50
     */
    public function testCreateCompositeAppliesDiscountsSequentially(): void
    {
        $composite = DiscountFactory::createComposite([
            ['type' => 'percentage', 'value' => 10.0],
            ['type' => 'fixed', 'value' => 30.0],
        ]);

        $this->assertEqualsWithDelta(50.0, $composite->calculate(200.0), 0.001);
    }

    /**
     * createComposite with empty array throws exception
     */
    public function testCreateCompositeEmptyArrayThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Composite discount requires at least one discount');

        DiscountFactory::createComposite([]);
    }
}
