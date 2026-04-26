<?php

declare(strict_types=1);

namespace App\Repositories\Decorators;

use App\Contracts\OrderRepositoryInterface;
use App\Models\Order;

abstract class OrderRepositoryDecorator implements OrderRepositoryInterface
{
    public function __construct(
        protected readonly OrderRepositoryInterface $innerRepository
    ) {}

    public function save(Order $order): bool
    {
        return $this->innerRepository->save($order);
    }

    public function findById(int $id): ?Order
    {
        return $this->innerRepository->findById($id);
    }

    public function findByUserId(int $userId): array
    {
        return $this->innerRepository->findByUserId($userId);
    }

    public function delete(int $id): bool
    {
        return $this->innerRepository->delete($id);
    }

    public function getInnerRepository(): OrderRepositoryInterface
    {
        return $this->innerRepository;
    }
}
