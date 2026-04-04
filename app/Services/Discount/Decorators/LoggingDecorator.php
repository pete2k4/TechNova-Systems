<?php

declare(strict_types=1);

namespace App\Services\Discount\Decorators;

use Psr\Log\LoggerInterface;

/**
 * Logging Decorator
 *
 * Logs discount calculations for debugging and analytics.
 */
class LoggingDecorator extends AbstractDiscountDecorator
{
    public function __construct(
        private readonly LoggerInterface $logger,
        \App\Contracts\DiscountInterface $wrappedDiscount,
    ) {
        parent::__construct($wrappedDiscount);
    }

    /**
     * Calculate discount and log the transaction.
     */
    public function calculate(float $price): float
    {
        $discount = $this->wrappedDiscount->calculate($price);
        $discountType = class_basename($this->wrappedDiscount);

        $this->logger->debug(
            sprintf(
                'Discount [%s] applied: %.2f on price %.2f',
                $discountType,
                $discount,
                $price,
            )
        );

        return $discount;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
