<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Product;

/**
 * SOLID Principle: Single Responsibility Principle (SRP)
 * 
 * This class has ONE responsibility: handling product data persistence.
 * It doesn't validate data or apply business rules - just database operations.
 * 
 * Each class should have only one reason to change.
 */
class ProductRepository
{
    /**
     * Save a product to the database.
     */
    public function save(Product $product): bool
    {
        // Database save logic would go here
        // return $product->save();
        return true;
    }

    /**
     * Find a product by ID.
     */
    public function findById(int $id): ?Product
    {
        // Database query logic would go here
        // return Product::find($id);
        return null;
    }

    /**
     * Get all products.
     */
    public function all(): array
    {
        // return Product::all()->toArray();
        return [];
    }
}
