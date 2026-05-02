<?php

namespace App\Bootstrap;

use App\Services\CheckoutService;
use App\Services\OrderService;
use App\Services\PriceCalculator;
use App\Services\ProductNotifier;
use App\Singletons\ServiceRegistry;

/**
 * ServiceRegistry Bootstrap
 *
 * Initializes the singleton ServiceRegistry with critical application services.
 * This ensures these services are instantiated once and reused throughout the app.
 *
 * Called during application bootstrap in AppServiceProvider::boot()
 */
class InitializeServiceRegistry
{
    /**
     * Bootstrap the service registry with critical services.
     *
     * @return void
     */
    public static function bootstrap(): void
    {
        $registry = ServiceRegistry::getInstance();

        // Register CheckoutService as singleton
        if (!$registry->has('checkoutService')) {
            $registry->register('checkoutService', new CheckoutService());
        }

        // Register OrderService as singleton
        if (!$registry->has('orderService')) {
            $registry->register('orderService', new OrderService());
        }

        // Register PriceCalculator as singleton
        if (!$registry->has('priceCalculator')) {
            $registry->register('priceCalculator', new PriceCalculator());
        }

        // Register ProductNotifier as singleton
        if (!$registry->has('productNotifier')) {
            $registry->register('productNotifier', new ProductNotifier());
        }
    }

    /**
     * Get a critical service from the registry.
     *
     * @param string $serviceKey
     * @return object|null
     */
    public static function getService(string $serviceKey): ?object
    {
        return ServiceRegistry::getInstance()->get($serviceKey);
    }

    /**
     * Check if a service is registered in the registry.
     *
     * @param string $serviceKey
     * @return bool
     */
    public static function hasService(string $serviceKey): bool
    {
        return ServiceRegistry::getInstance()->has($serviceKey);
    }
}
