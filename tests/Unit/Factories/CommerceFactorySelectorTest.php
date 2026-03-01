<?php

declare(strict_types=1);

namespace Tests\Unit\Factories;

use App\Factories\CommerceFactorySelector;
use App\Factories\Abstractions\CommerceFactoryInterface;
use App\Factories\Concrete\DigitalProductCommerceFactory;
use App\Factories\Concrete\PhysicalProductCommerceFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CommerceFactorySelectorTest extends TestCase
{
    protected function tearDown(): void
    {
        CommerceFactorySelector::clearCache();
        parent::tearDown();
    }

    /**
     * Test getting digital product factory
     */
    public function testGetDigitalFactory(): void
    {
        $factory = CommerceFactorySelector::getFactory('digital');

        $this->assertInstanceOf(CommerceFactoryInterface::class, $factory);
        $this->assertInstanceOf(DigitalProductCommerceFactory::class, $factory);
        $this->assertEquals('Digital Product Commerce', $factory->getFamilyName());
    }

    /**
     * Test getting physical product factory
     */
    public function testGetPhysicalFactory(): void
    {
        $factory = CommerceFactorySelector::getFactory('physical');

        $this->assertInstanceOf(CommerceFactoryInterface::class, $factory);
        $this->assertInstanceOf(PhysicalProductCommerceFactory::class, $factory);
        $this->assertEquals('Physical Product Commerce', $factory->getFamilyName());
    }

    /**
     * Test case insensitivity
     */
    public function testGetFactoryIsCaseInsensitive(): void
    {
        $factory1 = CommerceFactorySelector::getFactory('DIGITAL');
        $factory2 = CommerceFactorySelector::getFactory('Digital');
        $factory3 = CommerceFactorySelector::getFactory('digital');

        $this->assertInstanceOf(DigitalProductCommerceFactory::class, $factory1);
        $this->assertInstanceOf(DigitalProductCommerceFactory::class, $factory2);
        $this->assertInstanceOf(DigitalProductCommerceFactory::class, $factory3);
    }

    /**
     * Test factory caching - same instance returned
     */
    public function testFactoryCaching(): void
    {
        $factory1 = CommerceFactorySelector::getFactory('digital');
        $factory2 = CommerceFactorySelector::getFactory('digital');

        $this->assertSame($factory1, $factory2, 'Should return cached instance');
    }

    /**
     * Test unknown product type throws exception
     */
    public function testUnknownProductTypeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown product type: subscription');

        CommerceFactorySelector::getFactory('subscription');
    }

    /**
     * Test registering custom factory
     */
    public function testRegisterCustomFactory(): void
    {
        $customFactory = new DigitalProductCommerceFactory();
        CommerceFactorySelector::registerFactory('custom', $customFactory);

        $retrieved = CommerceFactorySelector::getFactory('custom');
        $this->assertSame($customFactory, $retrieved);
    }

    /**
     * Test clearing cache
     */
    public function testClearCache(): void
    {
        $factory1 = CommerceFactorySelector::getFactory('digital');
        CommerceFactorySelector::clearCache();
        $factory2 = CommerceFactorySelector::getFactory('digital');

        $this->assertNotSame($factory1, $factory2, 'Should be different instances after cache clear');
    }
}
