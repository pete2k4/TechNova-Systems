<?php

declare(strict_types=1);

namespace App\Prototypes;

use App\Contracts\ProductInterface;
use App\Contracts\PrototypeInterface;
use InvalidArgumentException;

/**
 * ProductPrototypeRegistry - Singleton Pattern
 * 
 * Ensures only one instance of the registry exists throughout the application.
 * This guarantees a single source of truth for all product prototypes.
 */
class ProductPrototypeRegistry
{
    private static ?self $instance = null;

    /**
     * @var array<string, ProductInterface&PrototypeInterface>
     */
    private array $prototypes = [];

    /**
     * Private constructor prevents direct instantiation.
     * Use getInstance() to access the singleton instance.
     */
    private function __construct()
    {
    }

    /**
     * Get the singleton instance of ProductPrototypeRegistry.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Reset the singleton instance (primarily for testing).
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    public function register(string $type, ProductInterface&PrototypeInterface $prototype): void
    {
        $this->prototypes[strtolower($type)] = $prototype;
    }

    public function has(string $type): bool
    {
        return array_key_exists(strtolower($type), $this->prototypes);
    }

    public function getClone(string $type): ProductInterface
    {
        $normalizedType = strtolower($type);

        if (!$this->has($normalizedType)) {
            throw new InvalidArgumentException("Unknown product prototype: {$type}");
        }

        return $this->prototypes[$normalizedType]->clonePrototype();
    }
}
