<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\OrderRepositoryInterface;
use App\Models\Order;

/**
 * SOLID Principle: Dependency Inversion Principle (DIP)
 * 
 * ✅ GOOD - Another low-level implementation.
 * We can inject this instead of MySQL without changing OrderService.
 * 
 * This demonstrates the power of DIP: swap implementations at runtime.
 */
class CachedOrderRepository implements OrderRepositoryInterface
{
    public function __construct(
        private readonly OrderRepositoryInterface $baseRepository
    ) {}

    public function save(Order $order): bool
    {
        // Save to cache, then delegate to base repository
        // Cache::put("order.{$order->id}", $order, 3600);
        // return $this->baseRepository->save($order);
        return true;
    }

    public function findById(int $id): ?Order
    {
        // Try cache first
        // if ($cached = Cache::get("order.{$id}")) {
        //     return $cached;
        // }
        // return $this->baseRepository->findById($id);
        return null;
    }

    public function findByUserId(int $userId): array
    {
        // Delegate to base repository
        // return $this->baseRepository->findByUserId($userId);
        return [];
    }
}
