<?php

declare(strict_types=1);

namespace Tests\Unit\Proxies;

use App\Contracts\ProductInterface;
use App\Contracts\ProductVisitorInterface;
use App\DTOs\DigitalProduct;
use App\Models\Product;
use App\Proxies\ProductRepositoryProxy;
use App\Repositories\ProductRepository;
use PHPUnit\Framework\TestCase;

class ProductRepositoryProxyTest extends TestCase
{
    private function makeProduct(int $id = 1, string $name = 'RTX 5090'): ProductInterface
    {
        return new class($id, $name) implements ProductInterface {
            public function __construct(
                private readonly int $id,
                private readonly string $name,
            ) {
            }

            public function accept(ProductVisitorInterface $visitor): mixed
            {
                return $visitor->visitDigitalProduct(new DigitalProduct());
            }

            public function getName(): string
            {
                return $this->name;
            }

            public function getPrice(): float
            {
                return 1999.99;
            }

            public function getDescription(): string
            {
                return 'Proxy test product';
            }
        };
    }

    /** @test */
    public function it_caches_find_by_id_results(): void
    {
        $product = $this->makeProduct(1);

        $repository = new class($product) extends ProductRepository {
            public int $findByIdCalls = 0;
            public function __construct(private readonly ProductInterface $product)
            {
            }

            public function findById(int $id): ?ProductInterface
            {
                $this->findByIdCalls++;
                return $this->product;
            }
        };

        $proxy = new ProductRepositoryProxy($repository);

        $first = $proxy->findById(1);
        $second = $proxy->findById(1);

        $this->assertSame($first, $second);
        $this->assertSame(1, $repository->findByIdCalls);
    }

    /** @test */
    public function it_caches_all_results(): void
    {
        $product = $this->makeProduct(1);

        $repository = new class($product) extends ProductRepository {
            public int $allCalls = 0;

            public function __construct(private readonly ProductInterface $product)
            {
            }

            public function all(): array
            {
                $this->allCalls++;
                return [$this->product];
            }
        };

        $proxy = new ProductRepositoryProxy($repository);

        $first = $proxy->all();
        $second = $proxy->all();

        $this->assertSame($first, $second);
        $this->assertSame(1, $repository->allCalls);
    }

    /** @test */
    public function it_caches_find_by_type_results(): void
    {
        $product = $this->makeProduct(1);

        $repository = new class($product) extends ProductRepository {
            public int $findByTypeCalls = 0;

            public function __construct(private readonly ProductInterface $product)
            {
            }

            public function findByType(string $type): array
            {
                $this->findByTypeCalls++;
                return [$this->product];
            }
        };

        $proxy = new ProductRepositoryProxy($repository);

        $first = $proxy->findByType('physical');
        $second = $proxy->findByType('physical');

        $this->assertSame($first, $second);
        $this->assertSame(1, $repository->findByTypeCalls);
    }

    /** @test */
    public function it_invalidates_cache_on_save(): void
    {
        $product = Product::make([
            'name' => 'Cached Product',
            'slug' => 'cached-product',
            'description' => 'Cached product',
            'price' => 99.99,
            'type' => 'physical',
            'sku' => 'CACHE-001',
            'stock' => 1,
        ]);

        $repository = new class($product) extends ProductRepository {
            public int $findByIdCalls = 0;
            public int $findByTypeCalls = 0;
            public int $allCalls = 0;
            public int $saveCalls = 0;

            public function __construct(private readonly Product $product)
            {
            }

            public function findById(int $id): ?ProductInterface
            {
                $this->findByIdCalls++;
                return $this->product;
            }

            public function findByType(string $type): array
            {
                $this->findByTypeCalls++;
                return [$this->product];
            }

            public function all(): array
            {
                $this->allCalls++;
                return [$this->product];
            }

            public function save(Product $product): bool
            {
                $this->saveCalls++;
                return true;
            }
        };

        $proxy = new ProductRepositoryProxy($repository);
        $proxy->findById(1);
        $proxy->findByType('physical');
        $proxy->all();

        $proxy->save($product);

        $proxy->findById(1);
        $proxy->findByType('physical');
        $proxy->all();

        $this->assertSame(1, $repository->saveCalls);
        $this->assertSame(2, $repository->findByIdCalls);
        $this->assertSame(2, $repository->findByTypeCalls);
        $this->assertSame(2, $repository->allCalls);
    }
}
