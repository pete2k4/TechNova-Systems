<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use Iterator;

final class ProductCollectionIterator implements Iterator
{
    /**
     * @var array<int,mixed>
     */
    private array $products;

    private int $position = 0;

    /**
     * @param iterable<int,mixed> $products
     */
    public function __construct(iterable $products)
    {
        $this->products = is_array($products)
            ? array_values($products)
            : array_values(iterator_to_array($products, false));
    }

    public function current(): mixed
    {
        return $this->products[$this->position];
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->products[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}
