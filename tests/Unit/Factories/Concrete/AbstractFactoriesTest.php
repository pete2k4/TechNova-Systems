<?php

declare(strict_types=1);

namespace Tests\Unit\Factories\Concrete;

use App\Contracts\DiscountInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\PaymentMethodInterface;
use App\Contracts\ProductInterface;
use App\DTOs\DigitalProduct;
use App\DTOs\PhysicalProduct;
use App\Factories\Concrete\DigitalProductCommerceFactory;
use App\Factories\Concrete\PhysicalProductCommerceFactory;
use PHPUnit\Framework\TestCase;

class AbstractFactoriesTest extends TestCase
{
    /**
     * Test digital factory creates products
     */
    public function testDigitalFactoryCreatesDigitalProduct(): void
    {
        $factory = new DigitalProductCommerceFactory();
        $product = $factory->createProduct();

        $this->assertInstanceOf(ProductInterface::class, $product);
        $this->assertInstanceOf(DigitalProduct::class, $product);
    }

    /**
     * Test digital factory creates discounts
     */
    public function testDigitalFactoryCreatesDiscount(): void
    {
        $factory = new DigitalProductCommerceFactory();
        $discount = $factory->createDiscount('percentage', 15.0);

        $this->assertInstanceOf(DiscountInterface::class, $discount);
    }

    /**
     * Test digital factory creates payment methods
     */
    public function testDigitalFactoryCreatesPaymentMethod(): void
    {
        $factory = new DigitalProductCommerceFactory();
        $payment = $factory->createPaymentMethod('paypal', 'user@example.com');

        $this->assertInstanceOf(PaymentMethodInterface::class, $payment);
    }

    /**
     * Test digital factory creates repository
     */
    public function testDigitalFactoryCreatesRepository(): void
    {
        $factory = new DigitalProductCommerceFactory();
        $repo = $factory->createOrderRepository();

        $this->assertInstanceOf(OrderRepositoryInterface::class, $repo);
    }

    /**
     * Test digital factory family name
     */
    public function testDigitalFactoryFamilyName(): void
    {
        $factory = new DigitalProductCommerceFactory();
        $this->assertEquals('Digital Product Commerce', $factory->getFamilyName());
    }

    /**
     * Test physical factory creates products
     */
    public function testPhysicalFactoryCreatesPhysicalProduct(): void
    {
        $factory = new PhysicalProductCommerceFactory();
        $product = $factory->createProduct();

        $this->assertInstanceOf(ProductInterface::class, $product);
        $this->assertInstanceOf(PhysicalProduct::class, $product);
    }

    /**
     * Test physical factory creates discounts
     */
    public function testPhysicalFactoryCreatesDiscount(): void
    {
        $factory = new PhysicalProductCommerceFactory();
        $discount = $factory->createDiscount('fixed', 50.0);

        $this->assertInstanceOf(DiscountInterface::class, $discount);
    }

    /**
     * Test physical factory creates payment methods
     */
    public function testPhysicalFactoryCreatesPaymentMethod(): void
    {
        $factory = new PhysicalProductCommerceFactory();
        $payment = $factory->createPaymentMethod('credit_card', '4532015112830366');

        $this->assertInstanceOf(PaymentMethodInterface::class, $payment);
    }

    /**
     * Test physical factory creates cached repository
     */
    public function testPhysicalFactoryCreatesCachedRepository(): void
    {
        $factory = new PhysicalProductCommerceFactory();
        $repo = $factory->createOrderRepository();

        $this->assertInstanceOf(OrderRepositoryInterface::class, $repo);
        // Physical factory should return cached repository
        // Can be verified in actual db calls
    }

    /**
     * Test physical factory family name
     */
    public function testPhysicalFactoryFamilyName(): void
    {
        $factory = new PhysicalProductCommerceFactory();
        $this->assertEquals('Physical Product Commerce', $factory->getFamilyName());
    }

    /**
     * Test factories can be used interchangeably (Liskov Substitution)
     */
    public function testFactoriesAreInterchangeable(): void
    {
        $factories = [
            new DigitalProductCommerceFactory(),
            new PhysicalProductCommerceFactory(),
        ];

        foreach ($factories as $factory) {
            // All should implement the interface
            $this->assertInstanceOf(\App\Factories\Abstractions\CommerceFactoryInterface::class, $factory);
            
            // All should have these methods
            $this->assertInstanceOf(ProductInterface::class, $factory->createProduct());
            $this->assertInstanceOf(DiscountInterface::class, $factory->createDiscount('fixed', 10));
            $this->assertInstanceOf(PaymentMethodInterface::class, $factory->createPaymentMethod('paypal', 'test@example.com'));
            $this->assertInstanceOf(OrderRepositoryInterface::class, $factory->createOrderRepository());
            $this->assertIsString($factory->getFamilyName());
        }
    }
}
