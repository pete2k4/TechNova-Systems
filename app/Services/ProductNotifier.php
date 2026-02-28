<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;

/**
 * SOLID Principle: Single Responsibility Principle (SRP)
 * 
 * This class has ONE responsibility: sending notifications about products.
 * It doesn't handle validation or persistence - just notifications.
 * 
 * By separating concerns, we can change notification logic
 * without touching validation or database code.
 */
class ProductNotifier
{
    /**
     * Notify about new product availability.
     */
    public function notifyNewProduct(Product $product): void
    {
        // Notification logic would go here
        // Mail::to($subscribers)->send(new NewProductMail($product));
    }

    /**
     * Notify about price drop.
     */
    public function notifyPriceDrop(Product $product, float $oldPrice): void
    {
        // Price drop notification logic would go here
    }
}
