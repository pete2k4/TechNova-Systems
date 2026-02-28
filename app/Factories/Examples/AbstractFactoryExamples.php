<?php

declare(strict_types=1);

namespace App\Factories\Examples;

use App\Factories\CommerceFactorySelector;
use App\Services\CheckoutService;

/**
 * Abstract Factory Usage Examples
 * 
 * Demonstrates how to use the CommerceFactoryInterface and CommerceFactorySelector
 * to create coordinated families of objects.
 */
class AbstractFactoryExamples
{
    /**
     * Example 1: Digital Product Checkout
     * Uses DigitalProductCommerceFactory implicitly
     */
    public function digitalProductCheckout(): void
    {
        $checkout = new CheckoutService('digital');

        $result = $checkout->checkout(
            ['name' => 'Windows 11 Pro', 'price' => 199.99],
            ['type' => 'percentage', 'value' => 10],
            ['type' => 'credit_card', 'credential' => '4532015112830366']
        );

        // $result is true if payment successful
    }

    /**
     * Example 2: Physical Product Checkout
     * Uses PhysicalProductCommerceFactory (with caching enabled by default)
     */
    public function physicalProductCheckout(): void
    {
        $checkout = new CheckoutService('physical');

        $result = $checkout->checkout(
            ['name' => 'NVIDIA RTX 4090', 'price' => 1599.99],
            ['type' => 'fixed', 'value' => 50],
            ['type' => 'paypal', 'credential' => 'customer@example.com']
        );

        // Physical products use cached repository
    }

    /**
     * Example 3: Direct Factory Usage
     * Get the factory and use its methods independently
     */
    public function directFactoryUsage(): void
    {
        $factory = CommerceFactorySelector::getFactory('digital');

        // Create a product
        $product = $factory->createProduct(['name' => 'Photoshop License']);

        // Create a discount
        $discount = $factory->createDiscount('percentage', 20);

        // Create a payment method
        $payment = $factory->createPaymentMethod(
            'credit_card',
            '4532015112830366'
        );

        // Create a repository for orders
        $repository = $factory->createOrderRepository();

        // Use objects in business logic
        $price = $product->getPrice();
        $discountedPrice = $price - $discount->calculate($price);
        $paymentSuccess = $payment->process($discountedPrice);
    }

    /**
     * Example 4: Factory Introspection
     * Get information about the factory family
     */
    public function factoryIntrospection(): void
    {
        $digitalFactory = CommerceFactorySelector::getFactory('digital');
        $physicalFactory = CommerceFactorySelector::getFactory('physical');

        echo $digitalFactory->getFamilyName();  // "Digital Product Commerce"
        echo $physicalFactory->getFamilyName(); // "Physical Product Commerce"

        // Can use this for logging, analytics, etc.
    }

    /**
     * Example 5: Custom Factory Registration
     * Extend the pattern with new product families
     */
    public function customFactoryRegistration(): void
    {
        // In a provider or configuration file:
        // CommerceFactorySelector::registerFactory('subscription', new SubscriptionCommerceFactory());

        // Then use it:
        // $factory = CommerceFactorySelector::getFactory('subscription');
    }

    /**
     * Example 6: Testing with Abstract Factory
     * Mock the factory for unit tests
     */
    public function testingWithAbstractFactory(): void
    {
        // In tests, register a mock factory:
        // $mockFactory = Mockery::mock(CommerceFactoryInterface::class);
        // $mockFactory->shouldReceive('createProduct')->andReturn($fakeProduct);
        // CommerceFactorySelector::registerFactory('test', $mockFactory);
        //
        // $checkout = new CheckoutService('test');
        // $result = $checkout->checkout([...]);
        //
        // All objects come from mock, making test isolated and fast
    }

    /**
     * Example 7: Switching Factories Dynamically
     * Select factory based on runtime conditions
     */
    public function dynamicFactorySelection(): void
    {
        $productType = request()->input('product_type'); // 'digital' or 'physical'
        
        $checkout = new CheckoutService($productType);

        // Different factory behavior based on product type
        $result = $checkout->checkout(
            request()->input('product'),
            request()->input('discount'),
            request()->input('payment')
        );
    }

    /**
     * Example 8: Multi-Product Order
     * Create multiple product families without mixing
     */
    public function multiProductOrder(): void
    {
        $digitalFactory = CommerceFactorySelector::getFactory('digital');
        $physicalFactory = CommerceFactorySelector::getFactory('physical');

        // Each factory creates objects optimized for its domain
        $license = $digitalFactory->createProduct(['name' => 'License']);
        $gpu = $physicalFactory->createProduct(['name' => 'GPU']);

        // Can apply different discount strategies
        $digitalDiscount = $digitalFactory->createDiscount('percentage', 15);
        $physicalDiscount = $physicalFactory->createDiscount('fixed', 50);

        // Process each with their optimized workflows
        $digitalPrice = $license->getPrice() - $digitalDiscount->calculate($license->getPrice());
        $physicalPrice = $gpu->getPrice() - $physicalDiscount->calculate($gpu->getPrice());
    }
}
