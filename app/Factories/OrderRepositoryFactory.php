<?php

declare(strict_types=1);

namespace App\Factories;

use App\Contracts\OrderRepositoryInterface;
use App\Repositories\CachedOrderRepository;
use App\Repositories\MySQLOrderRepository;
use InvalidArgumentException;

class OrderRepositoryFactory
{
    public const MYSQL = 'mysql';
    public const CACHED = 'cached';

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
     * @param array $config
     * @return OrderRepositoryInterface
     */
    public static function fromConfig(array $config): OrderRepositoryInterface
    {
        $type = $config['type'] ?? self::MYSQL;
        $enableCache = $config['enable_cache'] ?? false;

        if ($enableCache) {
            return self::createCachedStack();
        }

        return self::create($type);
    }
}
