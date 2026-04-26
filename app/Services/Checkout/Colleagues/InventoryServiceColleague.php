<?php

declare(strict_types=1);

namespace App\Services\Checkout\Colleagues;

use App\Domain\Cart\CartBundleComposite;
use App\Models\Product;
use RuntimeException;

class InventoryServiceColleague
{
    public function assertStockAvailable(CartBundleComposite $cart): void
    {
        foreach ($cart as $item) {
            $product = Product::query()->find($item->getProductId());

            if ($product === null) {
                throw new RuntimeException('One or more products no longer exist.');
            }

            if (!$product->is_active) {
                throw new RuntimeException('One or more products are not active.');
            }

            if ($product->isPhysical()) {
                $availableStock = (int) ($product->stock ?? 0);
                if ($availableStock < $item->getQuantity()) {
                    throw new RuntimeException('Insufficient stock for product: ' . $product->name);
                }
            }
        }
    }
}
