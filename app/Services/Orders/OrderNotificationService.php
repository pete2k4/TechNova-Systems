<?php

declare(strict_types=1);

namespace App\Services\Orders;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderNotificationService
{
    public function sendOrderPlacedNotification(Order $order): void
    {
        Log::info(sprintf(
            'Order %s placed for customer %d with total %s',
            (string) $order->order_number,
            (int) $order->user_id,
            (string) $order->total,
        ));
    }

    public function generateInvoice(Order $order): void
    {
        Log::info(sprintf(
            'Invoice generated for order %s with subtotal %s, discount %s and total %s',
            (string) $order->order_number,
            (string) $order->subtotal,
            (string) $order->discount,
            (string) $order->total,
        ));
    }
}
