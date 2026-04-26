<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\OrderRepositoryInterface;
use App\Factories\OrderRepositoryFactory;
use App\Models\Order;

class OrderService
{
    private OrderRepositoryInterface $repository;

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
        $order = new Order($orderData);

        return $this->repository->save($order);
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
