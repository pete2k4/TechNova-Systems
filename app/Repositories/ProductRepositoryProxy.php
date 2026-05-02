<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\ProductInterface;
use App\Contracts\DownloadableInterface;
use App\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProductRepositoryProxy
{
    private ProductRepository $realRepository;
    private OrderRepositoryInterface $orderRepository;

    public function __construct(ProductRepository $realRepository, OrderRepositoryInterface $orderRepository)
    {
        $this->realRepository = $realRepository;
        $this->orderRepository = $orderRepository;
    }

    public function save($product): bool
    {
        return $this->realRepository->save($product);
    }

    public function findById(int $id): ?ProductInterface
    {
        $product = $this->realRepository->findById($id);

        if ($product instanceof DownloadableInterface) {
            $user = Auth::user();

            if (!$user) {
                Log::warning("Product access denied: unauthenticated user attempted to access downloadable product {$id}");
                return null;
            }

            $orders = $this->orderRepository->findByUserId((int) $user->id);

            foreach ($orders as $order) {
                if (method_exists($order, 'isCompleted') && $order->isCompleted()) {
                    if (isset($order->items) && is_iterable($order->items)) {
                        foreach ($order->items as $item) {
                            $productId = $item->product_id ?? ($item['product_id'] ?? null);
                            if ($productId !== null && (int) $productId === $id) {
                                Log::info("Product access granted: user {$user->id} for product {$id}");
                                return $product;
                            }
                        }
                    }
                }
            }

            Log::warning("Product access denied: user {$user->id} has not purchased product {$id}");
            return null;
        }

        return $product;
    }

    public function all(): array
    {
        return $this->realRepository->all();
    }

    public function findByType(string $type): array
    {
        return $this->realRepository->findByType($type);
    }
}
