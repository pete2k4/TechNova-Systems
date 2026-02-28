<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Order;

/**
 * SOLID Principle: Dependency Inversion Principle (DIP)
 * 
 * ✅ GOOD - Abstraction (interface) that both high-level and low-level modules depend on.
 * 
 * This interface inverts the dependency:
 * - High-level OrderService depends on this abstraction
 * - Low-level implementations (MySQL, Cache) also depend on this abstraction
 * 
 * Both depend on the abstraction, not on each other.
 */
interface OrderRepositoryInterface
{
    public function save(Order $order): bool;
    public function findById(int $id): ?Order;
    public function findByUserId(int $userId): array;
}
