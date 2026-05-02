<?php

namespace App\Singletons;

use App\Contracts\ServiceRegistryInterface;

/**
 * ServiceRegistry Singleton Pattern Implementation
 *
 * Ensures only one instance of the service registry exists throughout
 * the application lifecycle. This registry manages and provides access
 * to critical application services without recreating them.
 *
 * Usage:
 *   $registry = ServiceRegistry::getInstance();
 *   $registry->register('checkoutService', $checkoutService);
 *   $service = $registry->get('checkoutService');
 */
class ServiceRegistry implements ServiceRegistryInterface
{
    /**
     * The single instance of ServiceRegistry (Singleton pattern).
     *
     * @var self|null
     */
    private static ?self $instance = null;

    /**
     * Registered services storage.
     *
     * @var array
     */
    private array $services = [];

    /**
     * Private constructor prevents direct instantiation.
     * Use getInstance() to obtain the singleton instance.
     */
    private function __construct()
    {
        // Private constructor ensures singleton behavior
    }

    /**
     * Prevent cloning of the singleton instance.
     *
     * @throws \Exception
     */
    public function __clone()
    {
        throw new \Exception('ServiceRegistry is a singleton and cannot be cloned.');
    }

    /**
     * Prevent unserialization of the singleton instance.
     *
     * @throws \Exception
     */
    public function __wakeup()
    {
        throw new \Exception('ServiceRegistry is a singleton and cannot be unserialized.');
    }

    /**
     * Get or create the singleton instance of ServiceRegistry.
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
     * Register a service instance in the registry.
     *
     * @param string $key
     * @param object $service
     * @return void
     */
    public function register(string $key, object $service): void
    {
        $this->services[$key] = $service;
    }

    /**
     * Retrieve a registered service by key.
     *
     * @param string $key
     * @return object|null
     */
    public function get(string $key): ?object
    {
        return $this->services[$key] ?? null;
    }

    /**
     * Check if a service is registered.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->services[$key]);
    }

    /**
     * Remove a service from the registry.
     *
     * @param string $key
     * @return void
     */
    public function remove(string $key): void
    {
        unset($this->services[$key]);
    }

    /**
     * Get all registered services.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->services;
    }

    /**
     * Clear all registered services.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->services = [];
    }
}
