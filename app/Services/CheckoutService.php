<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\DiscountInterface;
use App\Contracts\PaymentMethodInterface;
use App\Contracts\ProductInterface;
use App\Factories\Abstractions\CommerceFactoryInterface;
use App\Factories\CommerceFactorySelector;

/**
 * Checkout Service
 * 
 * Uses the abstract commerce factory to create a cohesive family of objects
 * for processing orders. Different product types get specialized factories.
 */
class CheckoutService
{
    private readonly CommerceFactoryInterface $factory;

    /**
     * Initialize checkout with the appropriate factory based on product type.
     *
     * @param string $productType
     */
    public function __construct(string $productType = 'digital')
    {
        $this->factory = CommerceFactorySelector::getFactory($productType);
    }

    /**
     * Process a complete checkout using factory-created objects.
     *
     * @param array $productData
     * @param array $discountConfig
     * @param array $paymentData
     * @return bool
     */
    public function checkout(array $productData, array $discountConfig = [], array $paymentData = []): bool
    {
        $product = $this->factory->createProduct($productData);
        
        $discount = null;
        if (!empty($discountConfig)) {
            $discount = $this->factory->createDiscount(
                $discountConfig['type'] ?? 'percentage',
                $discountConfig['value'] ?? 0
            );
        }

        $payment = $this->factory->createPaymentMethod(
            $paymentData['type'] ?? 'credit_card',
            $paymentData['credential'] ?? ''
        );

        $repository = $this->factory->createOrderRepository();

        return $this->executeCheckout($product, $discount, $payment, $repository);
    }

    /**
     * Execute checkout with created objects.
     *
     * @param ProductInterface $product
     * @param DiscountInterface|null $discount
     * @param PaymentMethodInterface $payment
     * @param mixed $repository
     * @return bool
     */
    private function executeCheckout(
        ProductInterface $product,
        ?DiscountInterface $discount,
        PaymentMethodInterface $payment,
        $repository
    ): bool {
        $price = $product->getPrice();
        
        if ($discount) {
            $price = max(0, $price - $discount->calculate($price));
        }

        $paymentSuccess = $payment->process($price);
        
        if ($paymentSuccess) {
            // Save order to repository
            // $repository->save($order);
        }

        return $paymentSuccess;
    }

    /**
     * Get the factory being used (for introspection).
     *
     * @return CommerceFactoryInterface
     */
    public function getFactory(): CommerceFactoryInterface
    {
        return $this->factory;
    }
}
