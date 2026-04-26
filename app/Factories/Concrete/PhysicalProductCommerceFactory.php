<?php

declare(strict_types=1);

namespace App\Factories\Concrete;

use App\Contracts\DiscountInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\PaymentMethodInterface;
use App\Contracts\ProductInterface;
use App\DTOs\PhysicalProduct;
use App\Factories\Abstractions\CommerceFactoryInterface;
use App\Factories\DiscountFactory;
use App\Factories\OrderRepositoryFactory;
use App\Factories\PaymentMethodFactory;

/**
 * Physical Product Commerce Factory
 * 
 * Specializes in creating physical product ecosystems.
 * All created objects are optimized for physical product workflows (shipping, inventory, etc).
 */
class PhysicalProductCommerceFactory implements CommerceFactoryInterface
{
    /**
     * Create a physical product.
     *
     * @param array $data
     * @return ProductInterface
     */
    public function createProduct(array $data = []): ProductInterface
    {
        return (new PhysicalProduct());
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
     * Create a payment method for physical products.
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
     * Create a cached repository for physical product orders (caching important for inventory).
     *
     * @param array $config
     * @return OrderRepositoryInterface
     */
    public function createOrderRepository(array $config = []): OrderRepositoryInterface
    {
        $config['enable_cache'] = true; // Physical products benefit from caching
        return OrderRepositoryFactory::fromConfig($config);
    }

    /**
     * @return string
     */
    public function getFamilyName(): string
    {
        return 'Physical Product Commerce';
    }
}
