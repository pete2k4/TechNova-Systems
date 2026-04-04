<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\OrderItem;
use App\Services\Inventory\StockAdjustmentService;

class OrderItemObserver
{
    public function __construct(
        private readonly StockAdjustmentService $stockAdjustmentService,
    ) {}

    public function created(OrderItem $orderItem): void
    {
        $this->stockAdjustmentService->decrementStock($orderItem);
    }
}
