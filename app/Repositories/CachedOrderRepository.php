<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\OrderRepositoryInterface;

class CachedOrderRepository implements OrderRepositoryInterface
{
    private const CACHE_TTL = 3600;

    /**
     * @param OrderRepositoryInterface $baseRepository
     */
    public function __construct(
        private readonly OrderRepositoryInterface $baseRepository
    ) {}

    /**
     * @param object $order
     * @return bool
     */
    public function save($order): bool
    {
        $result = $this->baseRepository->save($order);
        
        // Cache the saved order
        if ($result && isset($order->id)) {
            // Cache::put("order.{$order->id}", $order, self::CACHE_TTL);
        }
        
        return $result;
    }

    /**
     * @param int $id
     * @return object|null
     */
    public function findById(int $id): ?object
    {
        // Try cache first
        // if ($cached = Cache::get("order.{$id}")) {
        //     return $cached;
        // }
        
        $order = $this->baseRepository->findById($id);
        
        // Cache the result
        // if ($order) {
        //     Cache::put("order.{$id}", $order, self::CACHE_TTL);
        // }
        
        return $order;
    }

    /**
     * @param int $userId
     * @return array
     */
    public function findByUserId(int $userId): array
    {
        // For collections, caching is more complex, so we delegate directly
        // In production, you might cache the list with a time limit
        return $this->baseRepository->findByUserId($userId);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $result = $this->baseRepository->delete($id);
        
        // Invalidate cache
        // if ($result) {
        //     Cache::forget("order.{$id}");
        // }
        
        return $result;
    }

    /**
     * @return void
     */
    public function clearCache(): void
    {
        // Cache::flush(); // or more targeted cache clearing
    }

    /**
     * @return OrderRepositoryInterface
     */
    public function getBaseRepository(): OrderRepositoryInterface
    {
        return $this->baseRepository;
    }
}
