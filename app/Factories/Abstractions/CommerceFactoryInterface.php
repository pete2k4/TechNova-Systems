<?php

declare(strict_types=1);

namespace App\Factories\Abstractions;

use App\Contracts\DiscountInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\PaymentMethodInterface;
use App\Contracts\ProductInterface;

/**
 * Abstract Commerce Factory - defines the interface for creating families of related objects.
 * 
 * Different concrete factories can return compatible families optimized for specific domains.
 */
interface CommerceFactoryInterface
{
    /**
     * Create a product instance.
     *
     * @param array $data
     * @return ProductInterface
     */
    public function createProduct(array $data = []): ProductInterface;

    /**
     * Create a discount strategy.
     *
     * @param string $type
     * @param float $value
     * @return DiscountInterface
     */
    public function createDiscount(string $type, float $value): DiscountInterface;

    /**
     * Create a payment method.
     *
     * @param string $type
     * @param string $credential
     * @return PaymentMethodInterface
     */
    public function createPaymentMethod(string $type, string $credential): PaymentMethodInterface;

    /**
     * Create an order repository.
     *
     * @param array $config
     * @return OrderRepositoryInterface
     */
    public function createOrderRepository(array $config = []): OrderRepositoryInterface;

    /**
     * Get the family name (describes what this factory creates).
     *
     * @return string
     */
    public function getFamilyName(): string;
}
