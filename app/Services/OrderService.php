<?php

declare(strict_types=1);

namespace App\Services;

use App\Builders\OrderBuilder;
use App\Contracts\OrderRepositoryInterface;
use App\Factories\OrderRepositoryFactory;
use App\Models\Order;

class OrderService
{
    private readonly OrderRepositoryInterface $repository;

    /**
     * Uses factory to create repository based on config.
     */
    public function __construct()
    {
        $this->repository = OrderRepositoryFactory::fromConfig(config('order.repository', []));
    }

    /**
     * Create an order using the Builder pattern.
     *
     * @param int $userId
     * @param array $items Array of items with product_id, quantity, and price
     * @param array $paymentInfo Payment method and credential
     * @param float $discount Optional discount amount
     * @return Order
     */
    public function createOrder(
        int $userId,
        array $items,
        array $paymentInfo,
        float $discount = 0.0
    ): Order {
        // Use Builder pattern to construct complex Order object
        $builder = new OrderBuilder();
        
        $builder->forUser($userId);
        
        // Add all items to the order
        foreach ($items as $item) {
            $builder->addItem(
                $item['product_id'],
                $item['quantity'],
                $item['price']
            );
        }
        
        // Apply discount if provided
        if ($discount > 0) {
            $builder->withDiscount($discount);
        }
        
        // Set payment method
        $builder->withPaymentMethod(
            $paymentInfo['method'],
            $paymentInfo['credential'] ?? null
        );
        
        // Build and save the order
        $order = $builder->buildAndSave();
        
        // Optional: Save using repository pattern for additional processing
        // $this->repository->save($order);
        
        return $order;
    }

    /**
     * Legacy method for backward compatibility.
     * 
     * @deprecated Use createOrder() instead
     * @param array $orderData
     * @return bool
     */
    public function createOrderLegacy(array $orderData): bool
    {
        // Save using factory-created repository (could be MySQL, Cache, etc.)
        return $this->repository->save((object) $orderData);
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getUserOrders(int $userId): array
    {
        return $this->repository->findByUserId($userId);
    }

    /**
     * @param OrderRepositoryInterface $repository
     * @return void
     */
    public function setRepository(OrderRepositoryInterface $repository): void
    {
        $this->repository = $repository;
    }
}
