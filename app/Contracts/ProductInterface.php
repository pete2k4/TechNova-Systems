<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * SOLID Principle: Interface Segregation Principle (ISP)
 * 
 * ✅ GOOD - Base interface with only common product attributes.
 * Specific interfaces extend this for specific product types.
 * 
 * Clients only depend on the methods they actually use.
 */
interface ProductInterface
{
    public function getName(): string;
    public function getPrice(): float;
    public function getDescription(): string;
}
