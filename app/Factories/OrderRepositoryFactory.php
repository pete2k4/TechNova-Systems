<?php

declare(strict_types=1);

namespace App\Factories;

use App\Contracts\OrderRepositoryInterface;
use App\Repositories\CachedOrderRepository;
use App\Repositories\Decorators\LoggingOrderRepository;
use App\Repositories\MySQLOrderRepository;
use InvalidArgumentException;

class OrderRepositoryFactory
{
    public const MYSQL = 'mysql';
    public const CACHED = 'cached';
    public const LOGGED = 'logged';

    /**
     * @param string $type
     * @param OrderRepositoryInterface|null $baseRepository
     * @return OrderRepositoryInterface
     * @throws InvalidArgumentException
     */
    public static function create(
        string $type,
        ?OrderRepositoryInterface $baseRepository = null
    ): OrderRepositoryInterface {
        return match (strtolower($type)) {
            self::MYSQL => self::createMySQLRepository(),
            self::CACHED => self::createCachedRepository($baseRepository),
            self::LOGGED => self::createLoggedRepository($baseRepository),
            default => throw new InvalidArgumentException("Unknown repository type: {$type}"),
        };
    }

    /**
     * @return MySQLOrderRepository
     */
    public static function createMySQLRepository(): MySQLOrderRepository
    {
        return new MySQLOrderRepository();
    }

    /**
     * @param OrderRepositoryInterface|null $baseRepository
     * @return CachedOrderRepository
     */
    public static function createCachedRepository(
        ?OrderRepositoryInterface $baseRepository = null
    ): CachedOrderRepository {
        if ($baseRepository === null) {
            $baseRepository = self::createMySQLRepository();
        }

        return new CachedOrderRepository($baseRepository);
    }

    /**
     * @return CachedOrderRepository
     */
    public static function createCachedStack(): CachedOrderRepository
    {
        return self::createCachedRepository(self::createMySQLRepository());
    }

    /**
     * @param OrderRepositoryInterface|null $baseRepository
     * @return LoggingOrderRepository
     */
    public static function createLoggedRepository(
        ?OrderRepositoryInterface $baseRepository = null
    ): LoggingOrderRepository {
        if ($baseRepository === null) {
            $baseRepository = self::createMySQLRepository();
        }

        return new LoggingOrderRepository($baseRepository);
    }

    /**
     * @return OrderRepositoryInterface
     */
    public static function createDecoratorStack(bool $enableCache, bool $enableLogging): OrderRepositoryInterface
    {
        $repository = self::createMySQLRepository();

        if ($enableCache) {
            $repository = self::createCachedRepository($repository);
        }

        if ($enableLogging) {
            $repository = self::createLoggedRepository($repository);
        }

        return $repository;
    }

    /**
     * @param array $config
     * @return OrderRepositoryInterface
     */
    public static function fromConfig(array $config): OrderRepositoryInterface
    {
        $type = $config['type'] ?? self::MYSQL;
        $enableCache = (bool) ($config['enable_cache'] ?? false);
        $enableLogging = (bool) ($config['enable_logging'] ?? false);

        if ($enableCache || $enableLogging) {
            return self::createDecoratorStack($enableCache, $enableLogging);
        }

        return self::create($type);
    }
}
