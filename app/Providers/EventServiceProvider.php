<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Listeners\DecreaseStock;
use App\Listeners\NotifyOrderReceived;
use App\Listeners\TrackOrderAnalytics;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string,array<int,class-string>>
     */
    protected $listen = [
        OrderPlaced::class => [
            DecreaseStock::class,
            NotifyOrderReceived::class,
            TrackOrderAnalytics::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
