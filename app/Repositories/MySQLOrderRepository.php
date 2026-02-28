<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\OrderRepositoryInterface;
use App\Models\Order;

/**
 * SOLID Principle: Dependency Inversion Principle (DIP)
 * 
 * ✅ GOOD - Low-level implementation depends on the abstraction.
 * This can be swapped with any other implementation without changing OrderService.
 */
class MySQLOrderRepository implements OrderRepositoryInterface
{
    public function save(Order $order): bool
    {
        // MySQL-specific implementation
        // return $order->save();
        return true;
    }

    public function findById(int $id): ?Order
    {
        // MySQL query
        // return Order::find($id);
        return null;
    }

    public function findByUserId(int $userId): array
    {
        // MySQL query
        // return Order::where('user_id', $userId)->get()->toArray();
        return [];
    }
}
