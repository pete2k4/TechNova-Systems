<?php

declare(strict_types=1);

namespace App\Proxies;

use App\Contracts\ProductInterface;
use App\Contracts\ProductRepositoryInterface;
use App\Models\Product;
use App\Repositories\ProductRepository;

/**
 * Transparent proxy for product repository access.
 *
 * Adds lazy initialization and in-memory caching while preserving the same
 * repository contract.
 */
class ProductRepositoryProxy implements ProductRepositoryInterface
{
    private ?ProductRepositoryInterface $repository = null;

    /** @var array<int, ProductInterface|null> */
    private array $byIdCache = [];

    /** @var array<string, ProductInterface[]> */
    private array $byTypeCache = [];

    /** @var ProductInterface[]|null */
    private ?array $allCache = null;

    public function __construct(?ProductRepository $repository = null)
    {
        $this->repository = $repository;
    }

    public function save(Product $product): bool
    {
        $this->allCache = null;
        $this->byIdCache = [];
        $this->byTypeCache = [];

        return $this->getRepository()->save($product);
    }

    public function findById(int $id): ?ProductInterface
    {
        if (array_key_exists($id, $this->byIdCache)) {
            return $this->byIdCache[$id];
        }

        return $this->byIdCache[$id] = $this->getRepository()->findById($id);
    }

    public function all(): array
    {
        if ($this->allCache !== null) {
            return $this->allCache;
        }

        return $this->allCache = $this->getRepository()->all();
    }

    public function findByType(string $type): array
    {
        $normalizedType = strtolower($type);

        if (array_key_exists($normalizedType, $this->byTypeCache)) {
            return $this->byTypeCache[$normalizedType];
        }

        return $this->byTypeCache[$normalizedType] = $this->getRepository()->findByType($type);
    }

    public function clearCache(): void
    {
        $this->allCache = null;
        $this->byIdCache = [];
        $this->byTypeCache = [];
    }

    public function getRepository(): ProductRepositoryInterface
    {
        if ($this->repository === null) {
            $this->repository = new ProductRepository();
        }

        return $this->repository;
    }
}