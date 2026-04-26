<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\OrderRepositoryInterface;
use App\Models\Order;

class MySQLOrderRepository implements OrderRepositoryInterface
{
    /**
     * @param Order $order
     * @return bool
     */
    public function save(Order $order): bool
    {
        return $order->save();
    }

    /**
     * @param int $id
     * @return Order|null
     */
    public function findById(int $id): ?Order
    {
        return Order::query()->find($id);
    }

    /**
     * @param int $userId
     * @return array
     */
    public function findByUserId(int $userId): array
    {
        return Order::query()
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->get()
            ->all();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return Order::query()->whereKey($id)->delete() > 0;
    }
}
