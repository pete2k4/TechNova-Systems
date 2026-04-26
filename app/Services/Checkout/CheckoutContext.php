<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\Models\Order;

class CheckoutContext
{
    /**
     * @param array{type:string,value:float} $discountConfig
     */
    public function __construct(
        public readonly Order $order,
        public readonly array $discountConfig,
        public readonly string $factoryName,
        public readonly string $factoryClass,
        public readonly string $discountStrategyClass,
        public readonly string $paymentStrategyName,
        public readonly float $cartTotal,
        public readonly float $discountAmount,
        public readonly float $finalTotal,
        public readonly string $primaryProductType,
    ) {}
}
