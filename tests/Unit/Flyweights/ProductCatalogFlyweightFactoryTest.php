<?php

declare(strict_types=1);

namespace Tests\Unit\Flyweights;

use App\Flyweights\ProductCatalogFlyweightFactory;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class ProductCatalogFlyweightFactoryTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        ProductCatalogFlyweightFactory::reset();
        parent::tearDown();
    }

    /** @test */
    public function it_reuses_the_same_flyweight_instance_for_the_same_product(): void
    {
        $category = Category::create([
            'name' => 'GPUs',
            'slug' => 'gpus',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'RTX 5090',
            'slug' => 'rtx-5090',
            'description' => 'Flagship GPU',
            'price' => 1999.99,
            'type' => 'physical',
            'sku' => 'GPU-5090',
            'stock' => 5,
        ]);

        $factory = new ProductCatalogFlyweightFactory();

        $first = $factory->getByProductId($product->id);
        $second = $factory->getByProductId($product->id);

        $this->assertSame($first, $second);
        $this->assertSame(1, $factory->count());
    }

    /** @test */
    public function it_throws_for_unknown_product_id(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown product ID: 999999');

        $factory = new ProductCatalogFlyweightFactory();
        $factory->getByProductId(999999);
    }
}
