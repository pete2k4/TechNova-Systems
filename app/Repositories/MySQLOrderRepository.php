<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\OrderRepositoryInterface;

class MySQLOrderRepository implements OrderRepositoryInterface
{
    /**
     * @param object $order
     * @return bool
     */
    public function save($order): bool
    {
        // MySQL-specific implementation
        // return $order->save();
        return true;
    }

    /**
     * @param int $id
     * @return object|null
     */
    public function findById(int $id): ?object
    {
        // MySQL query: select * from orders where id = ?
        // return Order::find($id);
        return null;
    }

    /**
     * @param int $userId
     * @return array
     */
    public function findByUserId(int $userId): array
    {
        // MySQL query: select * from orders where user_id = ?
        // return Order::where('user_id', $userId)->get()->toArray();
        return [];
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        // return Order::destroy($id) > 0;
        return false;
    }
}
