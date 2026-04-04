<?php

namespace App\Providers;

use App\Contracts\ProductRepositoryInterface;
use App\Contracts\CartStoreInterface;
use App\Models\Order;
use App\Models\OrderItem;
use App\Observers\OrderItemObserver;
use App\Observers\OrderObserver;
use App\Services\Marketplace\Cart\CartSessionStore;
use Illuminate\Support\ServiceProvider;
use App\Contracts\OrderRepositoryInterface;
use App\Proxies\ProductRepositoryProxy;
use App\Repositories\MySQLOrderRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // SOLID Principle: Dependency Inversion Principle (DIP)
        // Bind interfaces to concrete implementations
        // This allows us to change implementations without modifying dependent classes
        
        // Order Repository binding
        // Switch to CachedOrderRepository for caching layer
        $this->app->bind(
            OrderRepositoryInterface::class,
            MySQLOrderRepository::class
        );

        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepositoryProxy::class
        );

        $this->app->bind(
            CartStoreInterface::class,
            CartSessionStore::class,
        );
        
        // Example of how to use cached repository:
        // $this->app->bind(OrderRepositoryInterface::class, function ($app) {
        //     return new CachedOrderRepository(
        //         new MySQLOrderRepository()
        //     );
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Order::observe(OrderObserver::class);
        OrderItem::observe(OrderItemObserver::class);
    }
}
