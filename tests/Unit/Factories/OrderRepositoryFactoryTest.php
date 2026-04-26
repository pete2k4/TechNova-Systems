<?php

declare(strict_types=1);

namespace Tests\Unit\Factories;

use App\Factories\OrderRepositoryFactory;
use App\Repositories\CachedOrderRepository;
use App\Repositories\Decorators\LoggingOrderRepository;
use App\Repositories\MySQLOrderRepository;
use PHPUnit\Framework\TestCase;

class OrderRepositoryFactoryTest extends TestCase
{
    public function test_create_decorator_stack_wraps_repository_in_expected_order(): void
    {
        $repository = OrderRepositoryFactory::createDecoratorStack(true, true);

        $this->assertInstanceOf(LoggingOrderRepository::class, $repository);

        $cacheLayer = $repository->getInnerRepository();
        $this->assertInstanceOf(CachedOrderRepository::class, $cacheLayer);

        $mysqlLayer = $cacheLayer->getInnerRepository();
        $this->assertInstanceOf(MySQLOrderRepository::class, $mysqlLayer);
    }

    public function test_from_config_without_decorators_returns_requested_repository_type(): void
    {
        $repository = OrderRepositoryFactory::fromConfig([
            'type' => OrderRepositoryFactory::MYSQL,
            'enable_cache' => false,
            'enable_logging' => false,
        ]);

        $this->assertInstanceOf(MySQLOrderRepository::class, $repository);
    }
}
