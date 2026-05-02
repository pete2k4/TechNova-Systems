<?php

declare(strict_types=1);

namespace App\Factories;

use App\Contracts\PrototypeInterface;
use InvalidArgumentException;

/**
 * Prototype Registry
 * 
 * Centralized registry for managing prototype templates.
 * Stores pre-configured prototypes (products, orders, etc.) that can be cloned on demand.
 * 
 * This is useful for:
 * - Storing product templates (catalog base templates)
 * - Managing order templates for bulk operations
 * - Creating variants without database round-trips
 */
class PrototypeRegistry
{
    /**
     * @var array<string, PrototypeInterface> Registered prototypes by key
     */
    private array $prototypes = [];

    /**
     * Register a prototype by key.
     * 
     * @param string $key Unique identifier for the prototype
     * @param PrototypeInterface $prototype The prototype object to register
     * @return void
     */
    public function register(string $key, PrototypeInterface $prototype): void
    {
        $this->prototypes[$key] = $prototype;
    }

    /**
     * Get a registered prototype by key.
     * 
     * @param string $key Unique identifier for the prototype
     * @return PrototypeInterface|null The registered prototype or null if not found
     */
    public function get(string $key): ?PrototypeInterface
    {
        return $this->prototypes[$key] ?? null;
    }

    /**
     * Clone a registered prototype by key.
     * 
     * Creates a new clone of the registered prototype without modifying the original.
     * Useful for creating variants from a base template.
     * 
     * @param string $key Unique identifier for the prototype
     * @return PrototypeInterface A cloned instance of the prototype
     * @throws InvalidArgumentException If the prototype key is not registered
     */
    public function clone(string $key): PrototypeInterface
    {
        $prototype = $this->get($key);

        if ($prototype === null) {
            throw new InvalidArgumentException(
                "Prototype with key '{$key}' not found in registry. " .
                "Available keys: " . implode(', ', array_keys($this->prototypes))
            );
        }

        return $prototype->clone();
    }

    /**
     * Check if a prototype is registered.
     * 
     * @param string $key Unique identifier for the prototype
     * @return bool True if the prototype is registered, false otherwise
     */
    public function has(string $key): bool
    {
        return isset($this->prototypes[$key]);
    }

    /**
     * Remove a registered prototype.
     * 
     * @param string $key Unique identifier for the prototype
     * @return void
     */
    public function remove(string $key): void
    {
        unset($this->prototypes[$key]);
    }

    /**
     * Get all registered prototype keys.
     * 
     * @return array<int, string> List of all registered prototype keys
     */
    public function keys(): array
    {
        return array_keys($this->prototypes);
    }

    /**
     * Clear all registered prototypes.
     * 
     * @return void
     */
    public function clear(): void
    {
        $this->prototypes = [];
    }

    /**
     * Get the count of registered prototypes.
     * 
     * @return int The number of registered prototypes
     */
    public function count(): int
    {
        return count($this->prototypes);
    }
}
