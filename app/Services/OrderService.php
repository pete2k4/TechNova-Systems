<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\OrderRepositoryInterface;
use App\Models\Order;

/**
 * SOLID Principle: Dependency Inversion Principle (DIP)
 * 
 * ✅ GOOD - High-level class depends on abstraction (OrderRepositoryInterface).
 * 
 * Key benefits:
 * - We can inject ANY implementation (MySQL, Cache, Mock for testing)
 * - OrderService doesn't know or care about the concrete implementation
 * - Easy to test: inject a mock repository
 * - Easy to switch: change binding in service provider
 * 
 * This is DIP in action: both high-level and low-level modules depend on abstraction.
 */
class OrderService
{
    /**
     * Depends on abstraction, not concrete implementation.
     * Constructor injection via Laravel's service container.
     */
    public function __construct(
        private readonly OrderRepositoryInterface $repository
    ) {}

    /**
     * Create a new order.
     * Works with ANY OrderRepositoryInterface implementation.
     */
    public function createOrder(Order $order): bool
    {
        // Business logic...
        // Validate order
        // Apply discounts
        // Check inventory
        
        // Save using injected repository (could be MySQL, Cache, Mock, etc.)
        return $this->repository->save($order);
    }

    /**
     * Get user's orders.
     */
    public function getUserOrders(int $userId): array
    {
        return $this->repository->findByUserId($userId);
    }
}
