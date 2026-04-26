<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\OrderRepositoryInterface;
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
        //
    }
}
