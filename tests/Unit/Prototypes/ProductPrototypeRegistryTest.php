<?php

declare(strict_types=1);

namespace Tests\Unit\Prototypes;

use App\Contracts\ProductInterface;
use App\Contracts\PrototypeInterface;
use App\DTOs\DigitalProduct;
use App\DTOs\PhysicalProduct;
use App\Prototypes\ProductPrototypeRegistry;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * ProductPrototypeRegistry Tests - Singleton Pattern
 * 
 * Demonstrates that ProductPrototypeRegistry is a Singleton:
 * - Only one instance exists throughout the application
 * - Multiple calls to getInstance() return the same object
 **/
class ProductPrototypeRegistryTest extends TestCase
{
    protected function tearDown(): void
    {
        // Reset singleton after each test to ensure isolation
        ProductPrototypeRegistry::reset();
        parent::tearDown();
    }

    /**
     * Test getInstance returns the same instance (Singleton behavior)
     */
    public function testGetInstanceReturnsSameInstance(): void
    {
        $instance1 = ProductPrototypeRegistry::getInstance();
        $instance2 = ProductPrototypeRegistry::getInstance();

        $this->assertSame($instance1, $instance2);
    }

    /**
     * Test multiple getInstance calls return identical instance
     */
    public function testMultipleGetInstanceCallsReturnIdenticalInstance(): void
    {
        $instances = [
            ProductPrototypeRegistry::getInstance(),
            ProductPrototypeRegistry::getInstance(),
            ProductPrototypeRegistry::getInstance(),
        ];

        $this->assertSame($instances[0], $instances[1]);
        $this->assertSame($instances[1], $instances[2]);
    }

    /**
     * Test constructor is not directly callable
     */
    public function testConstructorIsPrivate(): void
    {
        $reflection = new \ReflectionClass(ProductPrototypeRegistry::class);
        $this->assertTrue($reflection->getConstructor()->isPrivate());
    }

    /**
     * Test registering prototype
     */
    public function testRegisterPrototype(): void
    {
        $registry = ProductPrototypeRegistry::getInstance();
        $digital = new DigitalProduct();

        $registry->register('digital', $digital);

        $this->assertTrue($registry->has('digital'));
    }

    /**
     * Test has returns false for unregistered type
     */
    public function testHasReturnsFalseForUnregisteredType(): void
    {
        $registry = ProductPrototypeRegistry::getInstance();

        $this->assertFalse($registry->has('unknown'));
    }

    /**
     * Test getClone returns distinct instances
     */
    public function testGetCloneReturnsDistinctInstances(): void
    {
        $registry = ProductPrototypeRegistry::getInstance();
        $prototype = new DigitalProduct();
        $registry->register('digital', $prototype);

        $clone1 = $registry->getClone('digital');
        $clone2 = $registry->getClone('digital');

        $this->assertNotSame($clone1, $clone2);
        $this->assertInstanceOf(DigitalProduct::class, $clone1);
        $this->assertInstanceOf(DigitalProduct::class, $clone2);
    }

    /**
     * Test getClone throws for unknown type
     */
    public function testGetCloneThrowsForUnknownType(): void
    {
        $registry = ProductPrototypeRegistry::getInstance();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown product prototype: unknown');

        $registry->getClone('unknown');
    }

    /**
     * Test reset destroys singleton instance
     */
    public function testResetDestroysInstance(): void
    {
        $instance1 = ProductPrototypeRegistry::getInstance();
        ProductPrototypeRegistry::reset();
        $instance2 = ProductPrototypeRegistry::getInstance();

        $this->assertNotSame($instance1, $instance2);
    }

    /**
     * Test reset clears prototype state
     */
    public function testResetClearsPrototypeState(): void
    {
        $registry1 = ProductPrototypeRegistry::getInstance();
        $digital = new DigitalProduct();
        $registry1->register('digital', $digital);
        $this->assertTrue($registry1->has('digital'));

        ProductPrototypeRegistry::reset();

        $registry2 = ProductPrototypeRegistry::getInstance();
        $this->assertFalse($registry2->has('digital'));
    }

    /**
     * Test singleton state is shared across code paths
     */
    public function testSingletonStateIsSharedAcrossCodePaths(): void
    {
        // Path 1: Register digital product
        $registry1 = ProductPrototypeRegistry::getInstance();
        $digital = new DigitalProduct();
        $registry1->register('digital', $digital);

        // Path 2: Access same singleton and verify digital is still there
        $registry2 = ProductPrototypeRegistry::getInstance();
        $this->assertTrue($registry2->has('digital'));

        // Verify they're the same instance
        $this->assertSame($registry1, $registry2);
    }

    /**
     * Test case-insensitive type handling
     */
    public function testCaseInsensitiveTypeHandling(): void
    {
        $registry = ProductPrototypeRegistry::getInstance();
        $digital = new DigitalProduct();

        $registry->register('DIGITAL', $digital);

        $this->assertTrue($registry->has('digital'));
        $this->assertTrue($registry->has('DIGITAL'));
        $this->assertTrue($registry->has('Digital'));

        $clone = $registry->getClone('diGiTaL');
        $this->assertInstanceOf(DigitalProduct::class, $clone);
    }

    /**
     * Test custom prototype registration and retrieval
     */
    public function testCustomPrototypeRegistrationAndRetrieval(): void
    {
        $registry = ProductPrototypeRegistry::getInstance();

        $customPrototype = new class implements ProductInterface, PrototypeInterface {
            public function getName(): string
            {
                return 'Custom Product';
            }

            public function getPrice(): float
            {
                return 99.99;
            }

            public function getDescription(): string
            {
                return 'A custom product';
            }

            public function clonePrototype(): static
            {
                return clone $this;
            }
        };

        $registry->register('custom', $customPrototype);

        $clone = $registry->getClone('custom');
        $this->assertEquals('Custom Product', $clone->getName());
        $this->assertEquals(99.99, $clone->getPrice());
    }
}
