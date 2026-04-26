<?php

declare(strict_types=1);

namespace App\Domain\Cart;

use Iterator;

final class CartIterator implements Iterator
{
    /**
     * @var array<int,CartItemLeaf>
     */
    private array $flatItems;

    private int $position = 0;

    /**
     * @param iterable<int,CartComponentInterface> $components
     */
    public function __construct(iterable $components)
    {
        $this->flatItems = $this->flatten($components);
    }

    public function current(): CartItemLeaf
    {
        return $this->flatItems[$this->position];
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
        return isset($this->flatItems[$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * @param iterable<int,CartComponentInterface> $components
     * @return array<int,CartItemLeaf>
     */
    private function flatten(iterable $components): array
    {
        $items = [];

        foreach ($components as $component) {
            foreach ($component as $leaf) {
                $items[] = $leaf;
            }
        }

        return $items;
    }
}
