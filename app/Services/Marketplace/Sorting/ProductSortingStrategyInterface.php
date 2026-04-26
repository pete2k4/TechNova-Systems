<?php

declare(strict_types=1);

namespace App\Services\Marketplace\Sorting;

use Illuminate\Database\Eloquent\Builder;

interface ProductSortingStrategyInterface
{
    public function key(): string;

    public function label(): string;

    public function apply(Builder $query): Builder;
}
