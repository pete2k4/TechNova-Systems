<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Support\Facades\Log;

class NotifyOrderReceived
{
    public function handle(OrderPlaced $event): void
    {
        Log::info('Order received notification dispatched.', [
            'order_id' => $event->order->id,
            'order_number' => $event->order->order_number,
            'user_id' => $event->order->user_id,
        ]);
    }
}
