<?php

declare(strict_types=1);

namespace Tests\Unit\Factories;

use App\Contracts\OrderRepositoryInterface;
use App\Factories\OrderRepositoryFactory;
use App\Repositories\CachedOrderRepository;
use App\Repositories\MySQLOrderRepository;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class OrderRepositoryFactoryTest extends TestCase
{
    /**
     * Test creating MySQL repository
     */
    public function testCreateMySQLRepository(): void
    {
        $repo = OrderRepositoryFactory::create('mysql');

        $this->assertInstanceOf(OrderRepositoryInterface::class, $repo);
        $this->assertInstanceOf(MySQLOrderRepository::class, $repo);
    }

    /**
     * Test creating cached repository
     */
    public function testCreateCachedRepository(): void
    {
        $repo = OrderRepositoryFactory::create('cached');

        $this->assertInstanceOf(OrderRepositoryInterface::class, $repo);
        $this->assertInstanceOf(CachedOrderRepository::class, $repo);
    }

    /**
     * Test create MySQL directly
     */
    public function testCreateMySQLDirectly(): void
    {
        $repo = OrderRepositoryFactory::createMySQLRepository();

        $this->assertInstanceOf(MySQLOrderRepository::class, $repo);
    }

    /**
     * Test create cached repository directly
     */
    public function testCreateCachedDirectly(): void
    {
        $repo = OrderRepositoryFactory::createCachedRepository();

        $this->assertInstanceOf(CachedOrderRepository::class, $repo);
    }

    /**
     * Test cached repository uses MySQL base by default
     */
    public function testCachedRepositoryDefaultsToMySQL(): void
    {
        $repo = OrderRepositoryFactory::createCachedRepository();

        $this->assertInstanceOf(CachedOrderRepository::class, $repo);
    }

    /**
     * Test cached stack method
     */
    public function testCreateCachedStack(): void
    {
        $repo = OrderRepositoryFactory::createCachedStack();

        $this->assertInstanceOf(CachedOrderRepository::class, $repo);
    }

    /**
     * Test case insensitivity
     */
    public function testCreateIsCaseInsensitive(): void
    {
        $repo1 = OrderRepositoryFactory::create('MYSQL');
        $repo2 = OrderRepositoryFactory::create('MySQL');

        $this->assertInstanceOf(MySQLOrderRepository::class, $repo1);
        $this->assertInstanceOf(MySQLOrderRepository::class, $repo2);
    }

    /**
     * Test unknown repository type throws exception
     */
    public function testUnknownTypeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown repository type: postgresql');

        OrderRepositoryFactory::create('postgresql');
    }

    /**
     * Test cached repository with custom base repository
     */
    public function testCachedRepositoryWithCustomBase(): void
    {
        $baseRepo = new MySQLOrderRepository();
        $cachedRepo = OrderRepositoryFactory::createCachedRepository($baseRepo);

        $this->assertInstanceOf(CachedOrderRepository::class, $cachedRepo);
    }

    /**
     * Test from config with default type
     */
    public function testFromConfigDefault(): void
    {
        $config = [];
        $repo = OrderRepositoryFactory::fromConfig($config);

        // Should default to mysql
        $this->assertInstanceOf(MySQLOrderRepository::class, $repo);
    }

    /**
     * Test from config with specified type
     */
    public function testFromConfigWithType(): void
    {
        $config = ['type' => 'mysql'];
        $repo = OrderRepositoryFactory::fromConfig($config);

        $this->assertInstanceOf(MySQLOrderRepository::class, $repo);
    }

    /**
     * Test from config with caching enabled
     */
    public function testFromConfigEnableCaching(): void
    {
        $config = [
            'type' => 'mysql',
            'enable_cache' => true
        ];
        $repo = OrderRepositoryFactory::fromConfig($config);

        $this->assertInstanceOf(CachedOrderRepository::class, $repo);
    }

    /**
     * Test from config without caching
     */
    public function testFromConfigNoCaching(): void
    {
        $config = [
            'type' => 'mysql',
            'enable_cache' => false
        ];
        $repo = OrderRepositoryFactory::fromConfig($config);

        $this->assertInstanceOf(MySQLOrderRepository::class, $repo);
    }

    /**
     * Test constants are available
     */
    public function testConstantsAvailable(): void
    {
        $this->assertEquals('mysql', OrderRepositoryFactory::MYSQL);
        $this->assertEquals('cached', OrderRepositoryFactory::CACHED);
    }

    /**
     * Test repository composition works
     */
    public function testRepositoryComposition(): void
    {
        $mysql = OrderRepositoryFactory::createMySQLRepository();
        $cached = OrderRepositoryFactory::createCachedRepository($mysql);

        $this->assertInstanceOf(CachedOrderRepository::class, $cached);
        $this->assertInstanceOf(MySQLOrderRepository::class, $mysql);
    }
}
