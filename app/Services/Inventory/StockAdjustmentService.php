<?php

declare(strict_types=1);

namespace App\Services\Inventory;

use App\Models\OrderItem;
use App\Models\Product;
use RuntimeException;

class StockAdjustmentService
{
    public function decrementStock(OrderItem $orderItem): void
    {
        $product = Product::find($orderItem->product_id);

        if ($product === null || !$product->isPhysical()) {
            return;
        }

        if ($product->stock < $orderItem->quantity) {
            throw new RuntimeException(sprintf(
                'Insufficient stock for product %d',
                $product->id,
            ));
        }

        $product->decrement('stock', $orderItem->quantity);
    }
}
