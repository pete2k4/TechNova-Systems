<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Decorators\OrderRepositoryDecorator;
use Illuminate\Support\Facades\Cache;

class CachedOrderRepository extends OrderRepositoryDecorator
{
    private const CACHE_TTL = 3600;

    private const CACHE_KEY_ORDER = 'order.%d';
    private const CACHE_KEY_USER_ORDERS = 'orders.user.%d';

    /**
     * @param Order $order
     * @return bool
     */
    public function save(Order $order): bool
    {
        $result = parent::save($order);
        
        if ($result && isset($order->id)) {
            Cache::put($this->orderCacheKey((int) $order->id), $order, self::CACHE_TTL);
            Cache::forget($this->userOrdersCacheKey((int) $order->user_id));
        }
        
        return $result;
    }

    /**
     * @param int $id
     * @return Order|null
     */
    public function findById(int $id): ?Order
    {
        return Cache::remember(
            $this->orderCacheKey($id),
            self::CACHE_TTL,
            fn() => parent::findById($id)
        );
    }

    /**
     * @param int $userId
     * @return array
     */
    public function findByUserId(int $userId): array
    {
        return Cache::remember(
            $this->userOrdersCacheKey($userId),
            self::CACHE_TTL,
            fn() => parent::findByUserId($userId)
        );
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $order = parent::findById($id);
        $result = parent::delete($id);

        if ($result) {
            Cache::forget($this->orderCacheKey($id));
            if ($order !== null) {
                Cache::forget($this->userOrdersCacheKey((int) $order->user_id));
            }
        }
        
        return $result;
    }

    /**
     * @return void
     */
    public function clearCache(): void
    {
        // Intentionally targeted invalidation only; global cache flush is avoided.
    }

    private function orderCacheKey(int $id): string
    {
        return sprintf(self::CACHE_KEY_ORDER, $id);
    }

    private function userOrdersCacheKey(int $userId): string
    {
        return sprintf(self::CACHE_KEY_USER_ORDERS, $userId);
    }
}
