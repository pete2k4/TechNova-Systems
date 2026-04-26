<?php

declare(strict_types=1);

namespace App\Repositories\Decorators;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class LoggingOrderRepository extends OrderRepositoryDecorator
{
    public function save(Order $order): bool
    {
        $result = parent::save($order);

        Log::info('OrderRepository.save', [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'result' => $result,
        ]);

        return $result;
    }

    public function findById(int $id): ?Order
    {
        $order = parent::findById($id);

        Log::info('OrderRepository.findById', [
            'order_id' => $id,
            'found' => $order !== null,
        ]);

        return $order;
    }

    public function findByUserId(int $userId): array
    {
        $orders = parent::findByUserId($userId);

        Log::info('OrderRepository.findByUserId', [
            'user_id' => $userId,
            'count' => count($orders),
        ]);

        return $orders;
    }

    public function delete(int $id): bool
    {
        $result = parent::delete($id);

        Log::warning('OrderRepository.delete', [
            'order_id' => $id,
            'result' => $result,
        ]);

        return $result;
    }
}
