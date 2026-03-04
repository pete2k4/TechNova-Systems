<?php

declare(strict_types=1);

namespace Tests\Unit\Factories;

use App\Contracts\ProductInterface;
use App\Contracts\PrototypeInterface;
use App\DTOs\DigitalProduct;
use App\DTOs\PhysicalProduct;
use App\Factories\ProductFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProductFactoryTest extends TestCase
{
    /**
     * Test creating digital product
     */
    public function testCreateDigitalProduct(): void
    {
        $product = ProductFactory::create('digital');

        $this->assertInstanceOf(ProductInterface::class, $product);
        $this->assertInstanceOf(DigitalProduct::class, $product);
    }

    /**
     * Test creating physical product
     */
    public function testCreatePhysicalProduct(): void
    {
        $product = ProductFactory::create('physical');

        $this->assertInstanceOf(ProductInterface::class, $product);
        $this->assertInstanceOf(PhysicalProduct::class, $product);
    }

    /**
     * Test create digital product directly
     */
    public function testCreateDigitalProductDirectly(): void
    {
        $product = ProductFactory::createDigitalProduct();

        $this->assertInstanceOf(DigitalProduct::class, $product);
    }

    /**
     * Test create physical product directly
     */
    public function testCreatePhysicalProductDirectly(): void
    {
        $product = ProductFactory::createPhysicalProduct();

        $this->assertInstanceOf(PhysicalProduct::class, $product);
    }

    /**
     * Test case insensitivity
     */
    public function testCreateIsCaseInsensitive(): void
    {
        $product1 = ProductFactory::create('DIGITAL');
        $product2 = ProductFactory::create('Digital');
        $product3 = ProductFactory::create('digital');

        $this->assertInstanceOf(DigitalProduct::class, $product1);
        $this->assertInstanceOf(DigitalProduct::class, $product2);
        $this->assertInstanceOf(DigitalProduct::class, $product3);
    }

    /**
     * Test unknown type throws exception
     */
    public function testUnknownTypeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown product type: subscription');

        ProductFactory::create('subscription');
    }

    /**
     * Test creating from array with type
     */
    public function testCreateFromArray(): void
    {
        $data = ['type' => 'digital', 'name' => 'License'];
        $product = ProductFactory::fromArray($data);

        $this->assertInstanceOf(DigitalProduct::class, $product);
    }

    /**
     * Test fromArray with physical
     */
    public function testCreateFromArrayPhysical(): void
    {
        $data = ['type' => 'physical', 'name' => 'GPU'];
        $product = ProductFactory::fromArray($data);

        $this->assertInstanceOf(PhysicalProduct::class, $product);
    }

    /**
     * Test fromArray missing type throws exception
     */
    public function testFromArrayMissingTypeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing product type');

        ProductFactory::fromArray(['name' => 'Product']);
    }

    /**
     * Test constants are available
     */
    public function testConstantsAvailable(): void
    {
        $this->assertEquals('digital', ProductFactory::DIGITAL);
        $this->assertEquals('physical', ProductFactory::PHYSICAL);
    }

    /**
     * Test factory returns clones (Prototype pattern)
     */
    public function testFactoryReturnsDistinctInstancesFromPrototypes(): void
    {
        $product1 = ProductFactory::create('digital');
        $product2 = ProductFactory::create('digital');

        $this->assertInstanceOf(DigitalProduct::class, $product1);
        $this->assertInstanceOf(DigitalProduct::class, $product2);
        $this->assertNotSame($product1, $product2);
    }

    /**
     * Test custom prototype registration and cloning
     */
    public function testCustomPrototypeCanBeRegistered(): void
    {
        $prototype = new class implements ProductInterface, PrototypeInterface {
            public function getName(): string
            {
                return 'Prototype Subscription';
            }

            public function getPrice(): float
            {
                return 49.99;
            }

            public function getDescription(): string
            {
                return 'Custom prototype product';
            }

            public function clonePrototype(): static
            {
                return clone $this;
            }
        };

        ProductFactory::registerPrototype('custom_subscription', $prototype);

        $clone1 = ProductFactory::create('custom_subscription');
        $clone2 = ProductFactory::create('custom_subscription');

        $this->assertSame('Prototype Subscription', $clone1->getName());
        $this->assertSame(49.99, $clone1->getPrice());
        $this->assertNotSame($clone1, $clone2);
    }
}
