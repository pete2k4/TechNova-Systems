<?php

declare(strict_types=1);

namespace App\Services\Marketplace\Sorting;

use App\Services\Marketplace\Sorting\Strategies\NameAscendingSortStrategy;
use App\Services\Marketplace\Sorting\Strategies\NewestProductsSortStrategy;
use App\Services\Marketplace\Sorting\Strategies\PriceAscendingSortStrategy;
use App\Services\Marketplace\Sorting\Strategies\PriceDescendingSortStrategy;

final class ProductSortStrategyResolver
{
    public const DEFAULT_SORT = NewestProductsSortStrategy::KEY;

    /**
     * @return array<int, ProductSortingStrategyInterface>
     */
    private function strategies(): array
    {
        return [
            new NewestProductsSortStrategy(),
            new PriceAscendingSortStrategy(),
            new PriceDescendingSortStrategy(),
            new NameAscendingSortStrategy(),
        ];
    }

    public function resolve(?string $sort): ProductSortingStrategyInterface
    {
        $normalizedSort = strtolower((string) $sort);

        foreach ($this->strategies() as $strategy) {
            if ($strategy->key() === $normalizedSort) {
                return $strategy;
            }
        }

        foreach ($this->strategies() as $strategy) {
            if ($strategy->key() === self::DEFAULT_SORT) {
                return $strategy;
            }
        }

        return $this->strategies()[0];
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public function options(): array
    {
        return array_map(
            static fn (ProductSortingStrategyInterface $strategy): array => [
                'value' => $strategy->key(),
                'label' => $strategy->label(),
            ],
            $this->strategies(),
        );
    }
}
