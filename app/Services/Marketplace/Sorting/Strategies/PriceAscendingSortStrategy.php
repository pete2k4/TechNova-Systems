<?php

declare(strict_types=1);

namespace App\Services\Marketplace\Sorting\Strategies;

use App\Services\Marketplace\Sorting\ProductSortingStrategyInterface;
use Illuminate\Database\Eloquent\Builder;

final class PriceAscendingSortStrategy implements ProductSortingStrategyInterface
{
    public const KEY = 'price_asc';

    public function key(): string
    {
        return self::KEY;
    }

    public function label(): string
    {
        return 'Price: low to high';
    }

    public function apply(Builder $query): Builder
    {
        return $query->orderBy('price', 'asc')->orderByDesc('created_at');
    }
}
