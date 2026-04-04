<?php

declare(strict_types=1);

namespace App\Services\Marketplace\Sorting\Strategies;

use App\Services\Marketplace\Sorting\ProductSortingStrategyInterface;
use Illuminate\Database\Eloquent\Builder;

final class NameAscendingSortStrategy implements ProductSortingStrategyInterface
{
    public const KEY = 'name_asc';

    public function key(): string
    {
        return self::KEY;
    }

    public function label(): string
    {
        return 'Name: A to Z';
    }

    public function apply(Builder $query): Builder
    {
        return $query->orderBy('name', 'asc')->orderByDesc('created_at');
    }
}
