<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\OrderRepositoryInterface;
use App\Factories\OrderRepositoryFactory;
use App\Bootstrap\InitializeServiceRegistry;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Order repository can now be transparently wrapped by decorators from config.
        $this->app->bind(OrderRepositoryInterface::class, function () {
            return OrderRepositoryFactory::fromConfig(config('order.repository', []));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Initialize the Singleton ServiceRegistry with critical services
        InitializeServiceRegistry::bootstrap();
    }
}
