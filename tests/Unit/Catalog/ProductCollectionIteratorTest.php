<?php

declare(strict_types=1);

namespace Tests\Unit\Catalog;

use App\Domain\Catalog\ProductCollectionIterator;
use PHPUnit\Framework\TestCase;

class ProductCollectionIteratorTest extends TestCase
{
    public function test_iterator_preserves_product_collection_order(): void
    {
        $products = [
            ['name' => 'Router'],
            ['name' => 'SSD'],
            ['name' => 'Monitor'],
        ];

        $iterator = new ProductCollectionIterator($products);

        $names = [];
        foreach ($iterator as $product) {
            $names[] = $product['name'];
        }

        $this->assertSame(['Router', 'SSD', 'Monitor'], $names);
    }

    public function test_iterator_can_be_rewound_for_second_pass(): void
    {
        $iterator = new ProductCollectionIterator([
            ['name' => 'GPU'],
            ['name' => 'RAM'],
        ]);

        $first = [];
        foreach ($iterator as $product) {
            $first[] = $product['name'];
        }

        $second = [];
        foreach ($iterator as $product) {
            $second[] = $product['name'];
        }

        $this->assertSame($first, $second);
    }
}
