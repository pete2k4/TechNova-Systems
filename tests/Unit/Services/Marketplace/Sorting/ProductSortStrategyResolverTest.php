<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Marketplace\Sorting;

use App\Services\Marketplace\Sorting\ProductSortStrategyResolver;
use App\Services\Marketplace\Sorting\Strategies\NameAscendingSortStrategy;
use App\Services\Marketplace\Sorting\Strategies\NewestProductsSortStrategy;
use App\Services\Marketplace\Sorting\Strategies\PriceAscendingSortStrategy;
use App\Services\Marketplace\Sorting\Strategies\PriceDescendingSortStrategy;
use Illuminate\Database\Eloquent\Builder;
use PHPUnit\Framework\TestCase;

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
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->exactly(2))
            ->method('orderByDesc')
            ->willReturnCallback(function (string $column) use ($builder): Builder {
                $this->assertContains($column, ['created_at', 'id']);
                return $builder;
            });

        $result = (new NewestProductsSortStrategy())->apply($builder);

        $this->assertSame($builder, $result);
    }

    public function testPriceAscendingStrategyOrdersByPriceAsc(): void
    {
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('orderBy')
            ->with('price', 'asc')
            ->willReturnSelf();
        $builder->expects($this->once())
            ->method('orderByDesc')
            ->with('created_at')
            ->willReturnSelf();

        $result = (new PriceAscendingSortStrategy())->apply($builder);

        $this->assertSame($builder, $result);
    }

    public function testPriceDescendingStrategyOrdersByPriceDesc(): void
    {
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('orderByDesc')
            ->with('price')
            ->willReturnSelf();
        $builder->expects($this->once())
            ->method('orderByDesc')
            ->with('created_at')
            ->willReturnSelf();

        $result = (new PriceDescendingSortStrategy())->apply($builder);

        $this->assertSame($builder, $result);
    }

    public function testNameAscendingStrategyOrdersByNameAsc(): void
    {
        $builder = $this->createMock(Builder::class);
        $builder->expects($this->once())
            ->method('orderBy')
            ->with('name', 'asc')
            ->willReturnSelf();
        $builder->expects($this->once())
            ->method('orderByDesc')
            ->with('created_at')
            ->willReturnSelf();

        $result = (new NameAscendingSortStrategy())->apply($builder);

        $this->assertSame($builder, $result);
    }
}
