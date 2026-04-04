<?php

declare(strict_types=1);

namespace App\Services\Marketplace\Sorting\Strategies;

use App\Services\Marketplace\Sorting\ProductSortingStrategyInterface;
use Illuminate\Database\Eloquent\Builder;

final class PriceDescendingSortStrategy implements ProductSortingStrategyInterface
{
    public const KEY = 'price_desc';

    public function key(): string
    {
        return self::KEY;
    }

    public function label(): string
    {
        return 'Price: high to low';
    }

    public function apply(Builder $query): Builder
    {
        return $query->orderByDesc('price')->orderByDesc('created_at');
    }
}
