<?php

namespace App\Contracts;

/**
 * ServiceRegistryInterface defines the contract for a singleton registry
 * that manages and provides access to critical application services.
 */
interface ServiceRegistryInterface
{
    /**
     * Register a service instance in the registry.
     *
     * @param string $key
     * @param object $service
     * @return void
     */
    public function register(string $key, object $service): void;

    /**
     * Retrieve a registered service by key.
     *
     * @param string $key
     * @return object|null
     */
    public function get(string $key): ?object;

    /**
     * Check if a service is registered.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Remove a service from the registry.
     *
     * @param string $key
     * @return void
     */
    public function remove(string $key): void;

    /**
     * Get all registered services.
     *
     * @return array
     */
    public function all(): array;

    /**
     * Clear all registered services.
     *
     * @return void
     */
    public function clear(): void;
}
