<?php

declare(strict_types=1);

namespace App\Factories\Concrete;

use App\Contracts\DiscountInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\PaymentMethodInterface;
use App\Contracts\ProductInterface;
use App\DTOs\DigitalProduct;
use App\Factories\Abstractions\CommerceFactoryInterface;
use App\Factories\DiscountFactory;
use App\Factories\OrderRepositoryFactory;
use App\Factories\PaymentMethodFactory;

/**
 * Digital Product Commerce Factory
 * 
 * Specializes in creating digital product ecosystems.
 * All created objects are optimized for digital product workflows.
 */
class DigitalProductCommerceFactory implements CommerceFactoryInterface
{
    /**
     * Create a digital product.
     *
     * @param array $data
     * @return ProductInterface
     */
    public function createProduct(array $data = []): ProductInterface
    {
        return (new DigitalProduct());
    }

    /**
     * Create a discount (percentage or fixed).
     *
     * @param string $type
     * @param float $value
     * @return DiscountInterface
     */
    public function createDiscount(string $type, float $value): DiscountInterface
    {
        return DiscountFactory::create($type, $value);
    }

    /**
     * Create a payment method for digital products (support PayPal, credit card).
     *
     * @param string $type
     * @param string $credential
     * @return PaymentMethodInterface
     */
    public function createPaymentMethod(string $type, string $credential): PaymentMethodInterface
    {
        return PaymentMethodFactory::create($type, $credential);
    }

    /**
     * Create a repository optimized for digital product orders.
     *
     * @param array $config
     * @return OrderRepositoryInterface
     */
    public function createOrderRepository(array $config = []): OrderRepositoryInterface
    {
        return OrderRepositoryFactory::fromConfig($config);
    }

    /**
     * @return string
     */
    public function getFamilyName(): string
    {
        return 'Digital Product Commerce';
    }
}
