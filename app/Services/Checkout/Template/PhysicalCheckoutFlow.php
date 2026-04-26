<?php

declare(strict_types=1);

namespace App\Services\Checkout\Template;

use App\Models\Product;

class PhysicalCheckoutFlow extends BaseCheckoutFlow
{
    protected function productType(): string
    {
        return 'physical';
    }

    protected function afterItemPersisted(Product $product, int $quantity): void
    {
        if ($product->isPhysical()) {
            $product->decrement('stock', $quantity);
        }
    }
}
