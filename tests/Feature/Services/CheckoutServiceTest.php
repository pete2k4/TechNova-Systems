<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Factories\Abstractions\CommerceFactoryInterface;
use App\Factories\Concrete\DigitalProductCommerceFactory;
use App\Factories\Concrete\PhysicalProductCommerceFactory;
use App\Services\CheckoutService;
use PHPUnit\Framework\TestCase;

class CheckoutServiceTest extends TestCase
{
    /**
     * Test checkout service initializes with digital factory
     */
    public function testCheckoutServiceInitializesWithDigitalFactory(): void
    {
        $service = new CheckoutService('digital');
        $factory = $service->getFactory();

        $this->assertInstanceOf(CommerceFactoryInterface::class, $factory);
        $this->assertInstanceOf(DigitalProductCommerceFactory::class, $factory);
    }

    /**
     * Test checkout service initializes with physical factory
     */
    public function testCheckoutServiceInitializesWithPhysicalFactory(): void
    {
        $service = new CheckoutService('physical');
        $factory = $service->getFactory();

        $this->assertInstanceOf(CommerceFactoryInterface::class, $factory);
        $this->assertInstanceOf(PhysicalProductCommerceFactory::class, $factory);
    }

    /**
     * Test checkout service defaults to digital
     */
    public function testCheckoutServiceDefaultsToDigital(): void
    {
        $service = new CheckoutService();
        $factory = $service->getFactory();

        $this->assertInstanceOf(DigitalProductCommerceFactory::class, $factory);
    }

    /**
     * Test checkout with minimal data
     */
    public function testCheckoutWithMinimalData(): void
    {
        $service = new CheckoutService('digital');

        $result = $service->checkout(
            ['name' => 'License', 'price' => 99.99],
            [],
            ['type' => 'credit_card', 'credential' => '4532015112830366']
        );

        $this->assertIsBool($result);
    }

    /**
     * Test checkout with discount
     */
    public function testCheckoutWithDiscount(): void
    {
        $service = new CheckoutService('physical');

        $result = $service->checkout(
            ['name' => 'GPU', 'price' => 1599.99],
            ['type' => 'percentage', 'value' => 10],
            ['type' => 'paypal', 'credential' => 'user@example.com']
        );

        $this->assertIsBool($result);
    }

    /**
     * Test digital and physical services use correct factories
     */
    public function testDifferentProductTypesUseDifferentFactories(): void
    {
        $digitalService = new CheckoutService('digital');
        $physicalService = new CheckoutService('physical');

        $digitalFactory = $digitalService->getFactory();
        $physicalFactory = $physicalService->getFactory();

        $this->assertInstanceOf(DigitalProductCommerceFactory::class, $digitalFactory);
        $this->assertInstanceOf(PhysicalProductCommerceFactory::class, $physicalFactory);
        $this->assertNotSame($digitalFactory, $physicalFactory);
    }

    /**
     * Test checkout creates coherent object family
     */
    public function testCheckoutCreatesCoherentObjectFamily(): void
    {
        $service = new CheckoutService('physical');
        $factory = $service->getFactory();

        // All objects from same factory should be compatible
        $product = $factory->createProduct();
        $discount = $factory->createDiscount('fixed', 50);
        $payment = $factory->createPaymentMethod('credit_card', '4532015112830366');
        $repository = $factory->createOrderRepository();

        $this->assertNotNull($product);
        $this->assertNotNull($discount);
        $this->assertNotNull($payment);
        $this->assertNotNull($repository);
    }

    /**
     * Test checkout with no optional data
     */
    public function testCheckoutWithNoOptionalData(): void
    {
        $service = new CheckoutService('digital');

        $result = $service->checkout(
            ['name' => 'E-book', 'price' => 19.99],
            [],
            []
        );

        $this->assertIsBool($result);
    }

    /**
     * Test factory family name is accessible
     */
    public function testFactoryFamilyNameIsAccessible(): void
    {
        $digitalService = new CheckoutService('digital');
        $physicalService = new CheckoutService('physical');

        $this->assertEquals(
            'Digital Product Commerce',
            $digitalService->getFactory()->getFamilyName()
        );
        $this->assertEquals(
            'Physical Product Commerce',
            $physicalService->getFactory()->getFamilyName()
        );
    }
}
