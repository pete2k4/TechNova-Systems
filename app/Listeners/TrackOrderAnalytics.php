<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Support\Facades\Log;

class TrackOrderAnalytics
{
    public function handle(OrderPlaced $event): void
    {
        Log::info('Order analytics tracked.', [
            'order_id' => $event->order->id,
            'total' => (float) $event->order->total,
            'items_count' => $event->order->items()->count(),
        ]);
    }
}
