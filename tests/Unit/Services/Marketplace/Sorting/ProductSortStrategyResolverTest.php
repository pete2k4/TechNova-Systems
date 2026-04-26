<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Marketplace\Sorting;

use App\Models\Product;
use App\Services\Marketplace\Sorting\ProductSortStrategyResolver;
use App\Services\Marketplace\Sorting\Strategies\NameAscendingSortStrategy;
use App\Services\Marketplace\Sorting\Strategies\NewestProductsSortStrategy;
use App\Services\Marketplace\Sorting\Strategies\PriceAscendingSortStrategy;
use App\Services\Marketplace\Sorting\Strategies\PriceDescendingSortStrategy;
use Tests\TestCase;

class ProductSortStrategyResolverTest extends TestCase
{
    public function testItResolvesTheNewestStrategyByDefault(): void
    {
        $resolver = new ProductSortStrategyResolver();

        $strategy = $resolver->resolve(null);

        $this->assertInstanceOf(NewestProductsSortStrategy::class, $strategy);
    }

    public function testItResolvesKnownStrategies(): void
    {
        $resolver = new ProductSortStrategyResolver();

        $this->assertInstanceOf(PriceAscendingSortStrategy::class, $resolver->resolve('price_asc'));
        $this->assertInstanceOf(PriceDescendingSortStrategy::class, $resolver->resolve('price_desc'));
        $this->assertInstanceOf(NameAscendingSortStrategy::class, $resolver->resolve('name_asc'));
    }

    public function testItFallsBackToNewestForUnknownKeys(): void
    {
        $resolver = new ProductSortStrategyResolver();

        $strategy = $resolver->resolve('unknown');

        $this->assertInstanceOf(NewestProductsSortStrategy::class, $strategy);
    }

    public function testItReturnsAllAvailableOptions(): void
    {
        $resolver = new ProductSortStrategyResolver();

        $options = $resolver->options();

        $this->assertCount(4, $options);
        $this->assertSame('newest', $options[0]['value']);
        $this->assertSame('Newest first', $options[0]['label']);
        $this->assertSame('price_asc', $options[1]['value']);
        $this->assertSame('price_desc', $options[2]['value']);
        $this->assertSame('name_asc', $options[3]['value']);
    }

    public function testNewestStrategyOrdersByCreatedAtDescThenIdDesc(): void
    {
        $query = (new NewestProductsSortStrategy())->apply(Product::query());
        $sql = strtolower($query->toSql());

        $this->assertStringContainsString('order by', $sql);
        $this->assertStringContainsString('created_at', $sql);
        $this->assertStringContainsString('id', $sql);
        $this->assertStringContainsString('desc', $sql);
    }

    public function testPriceAscendingStrategyOrdersByPriceAsc(): void
    {
        $query = (new PriceAscendingSortStrategy())->apply(Product::query());
        $sql = strtolower($query->toSql());

        $this->assertStringContainsString('price', $sql);
        $this->assertStringContainsString('asc', $sql);
        $this->assertStringContainsString('created_at', $sql);
        $this->assertStringContainsString('desc', $sql);
    }

    public function testPriceDescendingStrategyOrdersByPriceDesc(): void
    {
        $query = (new PriceDescendingSortStrategy())->apply(Product::query());
        $sql = strtolower($query->toSql());

        $this->assertStringContainsString('price', $sql);
        $this->assertStringContainsString('created_at', $sql);
        $this->assertStringContainsString('desc', $sql);
    }

    public function testNameAscendingStrategyOrdersByNameAsc(): void
    {
        $query = (new NameAscendingSortStrategy())->apply(Product::query());
        $sql = strtolower($query->toSql());

        $this->assertStringContainsString('name', $sql);
        $this->assertStringContainsString('asc', $sql);
        $this->assertStringContainsString('created_at', $sql);
        $this->assertStringContainsString('desc', $sql);
    }
}
