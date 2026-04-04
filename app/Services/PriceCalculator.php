<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\DiscountInterface;
use App\Factories\DiscountFactory;
use App\Services\Discount\Decorators\CappedDiscountDecorator;
use App\Services\Discount\Decorators\LoggingDecorator;
use App\Services\Discount\Decorators\LoyaltyBonusDecorator;
use App\Services\Discount\Decorators\MinimumPurchaseDecorator;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class PriceCalculator
{
    /**
     * @param float $basePrice
     * @param string $discountType
     * @param float $discountValue
     * @return float
     */
    public function calculateDiscountedPrice(float $basePrice, string $discountType, float $discountValue): float
    {
        $discount = DiscountFactory::create($discountType, $discountValue);
        return $this->applyDiscount($basePrice, $discount);
    }

    /**
     * @param float $basePrice
     * @param array $discountConfig
     * @return float
     */
    public function calculateFromConfig(float $basePrice, array $discountConfig): float
    {
        $discount = DiscountFactory::fromConfig($discountConfig);

        // Apply decorator stack if specified in config
        if (!empty($discountConfig['decorators'])) {
            $discount = $this->applyDecoratorStack($discount, $discountConfig['decorators']);
        }

        return $this->applyDiscount($basePrice, $discount);
    }

    /**
     * @param float $basePrice
     * @param DiscountInterface $discount
     * @return float
     */
    public function applyDiscount(float $basePrice, DiscountInterface $discount): float
    {
        $discountAmount = $discount->calculate($basePrice);
        return max(0.0, $basePrice - $discountAmount);
    }

    /**
     * Apply a stack of decorators to a discount based on config array.
     *
     * Decorator order matters—applied sequentially.
     *
     * Example config:
     * [
     *     'loyalty_bonus' => 5.0,           // Add 5% loyalty bonus
     *     'minimum_purchase' => 100.00,     // Requires $100 minimum
     *     'cap' => 50.00,                   // Cap discount at $50
     *     'logging' => true,                // Enable logging (uses NullLogger if no logger provided)
     * ]
     *
     * @param DiscountInterface $discount Base discount to decorate
     * @param array $decoratorConfig Decorator configuration
     * @param LoggerInterface|null $logger Optional logger for LoggingDecorator
     * @return DiscountInterface Decorated discount
     */
    public function applyDecoratorStack(
        DiscountInterface $discount,
        array $decoratorConfig,
        ?LoggerInterface $logger = null,
    ): DiscountInterface {
        // Apply loyalty bonus if specified
        if (isset($decoratorConfig['loyalty_bonus'])) {
            $discount = new LoyaltyBonusDecorator((float) $decoratorConfig['loyalty_bonus'], $discount);
        }

        // Apply minimum purchase requirement if specified
        if (isset($decoratorConfig['minimum_purchase'])) {
            $discount = new MinimumPurchaseDecorator((float) $decoratorConfig['minimum_purchase'], $discount);
        }

        // Apply discount cap if specified
        if (isset($decoratorConfig['cap'])) {
            $discount = new CappedDiscountDecorator((float) $decoratorConfig['cap'], $discount);
        }

        // Apply logging if specified
        if (!empty($decoratorConfig['logging'])) {
            $logger ??= new NullLogger();
            $discount = new LoggingDecorator($logger, $discount);
        }

        return $discount;
    }
}
