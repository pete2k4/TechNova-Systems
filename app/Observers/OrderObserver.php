<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Order;
use App\Services\Orders\OrderNotificationService;

class OrderObserver
{
    public function __construct(
        private readonly OrderNotificationService $orderNotificationService,
    ) {}

    public function created(Order $order): void
    {
        $this->orderNotificationService->sendOrderPlacedNotification($order);
        $this->orderNotificationService->generateInvoice($order);
    }
}
