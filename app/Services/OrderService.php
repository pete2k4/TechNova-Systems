<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\OrderRepositoryInterface;
use App\Factories\OrderRepositoryFactory;

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
     * @param array $orderData
     * @return bool
     */
    public function createOrder(array $orderData): bool
    {
        // Business logic...
        // Validate order
        // Apply discounts
        // Check inventory
        
        // Save using factory-created repository (could be MySQL, Cache, etc.)
        // In a real implementation, you'd construct an Order object from $orderData
        // and call $this->repository->save($order)
        
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
