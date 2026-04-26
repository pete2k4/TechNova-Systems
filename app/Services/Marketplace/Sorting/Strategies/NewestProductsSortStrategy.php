<?php

declare(strict_types=1);

namespace App\Services\Marketplace\Sorting\Strategies;

use App\Services\Marketplace\Sorting\ProductSortingStrategyInterface;
use Illuminate\Database\Eloquent\Builder;

final class NewestProductsSortStrategy implements ProductSortingStrategyInterface
{
    public const KEY = 'newest';

    public function key(): string
    {
        return self::KEY;
    }

    public function label(): string
    {
        return 'Newest first';
    }

    public function apply(Builder $query): Builder
    {
        return $query->orderByDesc('created_at')->orderByDesc('id');
    }
}
