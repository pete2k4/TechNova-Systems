<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\OrderRepositoryInterface;
use App\Factories\OrderRepositoryFactory;
use App\Bootstrap\InitializeServiceRegistry;
use App\Repositories\ProductRepository;
use App\Repositories\ProductRepositoryProxy;

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

        // Bind ProductRepository to the protection proxy so downloadable products
        // are only exposed to users who purchased them.
        $this->app->bind(ProductRepository::class, function ($app) {
            $real = new ProductRepository();
            $orderRepo = $app->make(OrderRepositoryInterface::class);
            return new ProductRepositoryProxy($real, $orderRepo);
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
