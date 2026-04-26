<?php

declare(strict_types=1);

namespace Tests\Unit\Observers;

use App\Models\Order;
use App\Observers\OrderObserver;
use App\Services\Orders\OrderNotificationService;
use PHPUnit\Framework\TestCase;

class OrderObserverTest extends TestCase
{
    public function testItNotifiesAndGeneratesInvoiceWhenOrderIsCreated(): void
    {
        $order = new Order([
            'user_id' => 1,
            'order_number' => 'ORD-1001',
            'subtotal' => 200.00,
            'discount' => 20.00,
            'total' => 180.00,
            'status' => 'completed',
            'payment_method' => 'credit_card',
            'payment_credential' => '4532015112830366',
        ]);

        $notificationService = $this->createMock(OrderNotificationService::class);
        $notificationService->expects($this->once())
            ->method('sendOrderPlacedNotification')
            ->with($order);
        $notificationService->expects($this->once())
            ->method('generateInvoice')
            ->with($order);

        $observer = new OrderObserver($notificationService);
        $observer->created($order);

        $this->assertTrue(true);
    }
}
