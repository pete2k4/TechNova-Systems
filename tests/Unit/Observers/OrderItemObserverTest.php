<?php

declare(strict_types=1);

namespace Tests\Unit\Observers;

use App\Models\OrderItem;
use App\Observers\OrderItemObserver;
use App\Services\Inventory\StockAdjustmentService;
use PHPUnit\Framework\TestCase;

class OrderItemObserverTest extends TestCase
{
    public function testItDelegatesStockAdjustmentWhenOrderItemIsCreated(): void
    {
        $orderItem = new OrderItem([
            'order_id' => 1,
            'product_id' => 10,
            'quantity' => 2,
            'price' => 49.99,
        ]);

        $stockAdjustmentService = $this->createMock(StockAdjustmentService::class);
        $stockAdjustmentService->expects($this->once())
            ->method('decrementStock')
            ->with($orderItem);

        $observer = new OrderItemObserver($stockAdjustmentService);
        $observer->created($orderItem);

        $this->assertTrue(true);
    }
}
