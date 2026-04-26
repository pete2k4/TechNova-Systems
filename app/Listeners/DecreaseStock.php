<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrderPlaced;

class DecreaseStock
{
    public function handle(OrderPlaced $event): void
    {
        $items = $event->order->relationLoaded('items')
            ? $event->order->items
            : $event->order->items()->with('product')->get();

        foreach ($items as $item) {
            $product = $item->relationLoaded('product') ? $item->product : $item->product()->first();

            if ($product !== null && $product->isPhysical()) {
                $product->decrement('stock', (int) $item->quantity);
            }
        }
    }
}
