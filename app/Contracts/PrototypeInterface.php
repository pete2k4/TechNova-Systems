<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Prototype Interface
 * 
 * Defines the contract for objects that can be cloned using the Prototype pattern.
 * This allows creating exact copies of objects without instantiating new ones from scratch,
 * which is especially useful for complex domain objects with many relationships and properties.
 */
interface PrototypeInterface
{
    /**
     * Create a deep clone of the current object.
     * 
     * Returns a new instance with all properties and relationships copied,
     * but without the database primary key (allowing it to be persisted as a new record).
     * 
     * @return static A cloned instance of the object
     */
    public function clone();
}
