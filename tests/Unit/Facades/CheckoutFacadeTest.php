<?php

declare(strict_types=1);

namespace Tests\Unit\Facades;

use App\Facades\CheckoutFacade;
use App\Facades\CheckoutResult;
use App\Services\Payment\PaymentProcessor;
use App\Services\PriceCalculator;
use PHPUnit\Framework\TestCase;

class CheckoutFacadeTest extends TestCase
{
    private array $cart = [
        ['price' => 100.00, 'quantity' => 1],
        ['price' => 50.00,  'quantity' => 2],
    ]; // subtotal = 200.00

    private array $paymentData = [
        'type'       => 'credit_card',
        'credential' => '4532015112830366',
    ];

    private function makeFacade(): CheckoutFacade
    {
        return new CheckoutFacade(new PriceCalculator(), new PaymentProcessor());
    }

    /**
     * Facade returns a CheckoutResult — caller never touches subsystem classes.
     */
    public function testProcessCartReturnsCheckoutResult(): void
    {
        $result = $this->makeFacade()->processCart(
            $this->cart,
            ['type' => 'fixed', 'value' => 20.0],
            $this->paymentData,
        );

        $this->assertInstanceOf(CheckoutResult::class, $result);
    }

    /**
     * Subtotal is the sum of price × quantity across all cart items.
     */
    public function testSubtotalIsCalculatedFromCartItems(): void
    {
        $result = $this->makeFacade()->processCart(
            $this->cart,
            ['type' => 'fixed', 'value' => 0.0],
            $this->paymentData,
        );

        $this->assertEqualsWithDelta(200.0, $result->subtotal, 0.001);
    }

    /**
     * Facade delegates discount math to PriceCalculator — fixed $20 off $200.
     */
    public function testFixedDiscountIsAppliedToSubtotal(): void
    {
        $result = $this->makeFacade()->processCart(
            $this->cart,
            ['type' => 'fixed', 'value' => 20.0],
            $this->paymentData,
        );

        $this->assertEqualsWithDelta(20.0,  $result->discountAmount, 0.001);
        $this->assertEqualsWithDelta(180.0, $result->finalTotal,     0.001);
    }

    /**
     * Percentage discount: 10% of $200 = $20.
     */
    public function testPercentageDiscountIsAppliedToSubtotal(): void
    {
        $result = $this->makeFacade()->processCart(
            $this->cart,
            ['type' => 'percentage', 'value' => 10.0],
            $this->paymentData,
        );

        $this->assertEqualsWithDelta(20.0,  $result->discountAmount, 0.001);
        $this->assertEqualsWithDelta(180.0, $result->finalTotal,     0.001);
    }

    /**
     * Payment subsystem is called — successful payment is reflected in result.
     */
    public function testPaymentSuccessIsReflectedInResult(): void
    {
        $result = $this->makeFacade()->processCart(
            $this->cart,
            ['type' => 'fixed', 'value' => 0.0],
            $this->paymentData,
        );

        $this->assertTrue($result->paymentSuccess);
        $this->assertSame('credit_card', $result->paymentMethodName);
    }

    /**
     * Commerce factory is resolved and its names are surfaced in the result.
     */
    public function testFactoryNamesArePopulatedForDigitalCart(): void
    {
        $result = $this->makeFacade()->processCart(
            $this->cart,
            ['type' => 'fixed', 'value' => 0.0],
            $this->paymentData,
            'digital',
        );

        $this->assertNotEmpty($result->factoryFamilyName);
        $this->assertNotEmpty($result->factoryClass);
        $this->assertStringContainsStringIgnoringCase('digital', $result->factoryFamilyName);
    }

    /**
     * Physical product type selects the physical factory family.
     */
    public function testFactoryNamesArePopulatedForPhysicalCart(): void
    {
        $result = $this->makeFacade()->processCart(
            $this->cart,
            ['type' => 'fixed', 'value' => 0.0],
            $this->paymentData,
            'physical',
        );

        $this->assertStringContainsStringIgnoringCase('physical', $result->factoryFamilyName);
    }

    /**
     * Zero-value discount: discountAmount = 0, finalTotal = subtotal.
     */
    public function testZeroDiscountLeavesSubtotalUnchanged(): void
    {
        $result = $this->makeFacade()->processCart(
            $this->cart,
            ['type' => 'fixed', 'value' => 0.0],
            $this->paymentData,
        );

        $this->assertEqualsWithDelta(0.0,   $result->discountAmount, 0.001);
        $this->assertEqualsWithDelta(200.0, $result->finalTotal,     0.001);
    }

    /**
     * Discount cannot push the final total below zero.
     */
    public function testDiscountCannotExceedSubtotal(): void
    {
        $result = $this->makeFacade()->processCart(
            $this->cart,
            ['type' => 'fixed', 'value' => 9999.0],
            $this->paymentData,
        );

        $this->assertGreaterThanOrEqual(0.0, $result->finalTotal);
    }
}
