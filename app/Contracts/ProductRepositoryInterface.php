<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function save(Product $product): bool;

    public function findById(int $id): ?ProductInterface;

    /**
     * @return ProductInterface[]
     */
    public function all(): array;

    /**
     * @return ProductInterface[]
     */
    public function findByType(string $type): array;
}