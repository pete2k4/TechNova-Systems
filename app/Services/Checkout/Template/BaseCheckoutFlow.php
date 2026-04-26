<?php

declare(strict_types=1);

namespace App\Services\Checkout\Template;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\Checkout\CheckoutContext;
use App\Services\CheckoutService;
use RuntimeException;

abstract class BaseCheckoutFlow
{
    /**
     * @param array<int,array{product_id:int,price:float|int,quantity:int,type:string}> $cart
     * @param array{discount_type:string,discount_value:float|int,payment_type:string,payment_credential:string} $validated
     */
    final public function execute(array $cart, array $validated, int $userId): CheckoutContext
    {
        $cartTotal = $this->calculateCartTotal($cart);

        $discountConfig = [
            'type' => $validated['discount_type'],
            'value' => (float) $validated['discount_value'],
        ];
        $paymentData = [
            'type' => $validated['payment_type'],
            'credential' => $validated['payment_credential'],
        ];

        $checkoutService = new CheckoutService($this->productType());
        $factory = $checkoutService->getFactory();

        $discountStrategy = $factory->createDiscount(
            $discountConfig['type'],
            $discountConfig['value']
        );

        $discountAmount = min($discountStrategy->calculate($cartTotal), $cartTotal);
        $finalTotal = max(0, $cartTotal - $discountAmount);

        $paymentStrategy = $factory->createPaymentMethod(
            $paymentData['type'],
            $paymentData['credential']
        );

        if (!$paymentStrategy->process($finalTotal)) {
            throw new RuntimeException('Checkout failed: payment strategy rejected the transaction.');
        }

        $order = Order::create([
            'user_id' => $userId,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'subtotal' => $cartTotal,
            'discount' => $discountAmount,
            'total' => $finalTotal,
            'status' => $this->successfulOrderStatus(),
            'payment_method' => $validated['payment_type'],
            'payment_credential' => $validated['payment_credential'],
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);

            $product = Product::query()->find((int) $item['product_id']);
            if ($product !== null) {
                $this->afterItemPersisted($product, (int) $item['quantity']);
            }
        }

        return new CheckoutContext(
            order: $order,
            discountConfig: $discountConfig,
            factoryName: $factory->getFamilyName(),
            factoryClass: class_basename($factory::class),
            discountStrategyClass: class_basename($discountStrategy::class),
            paymentStrategyName: $paymentStrategy->getName(),
            cartTotal: $cartTotal,
            discountAmount: $discountAmount,
            finalTotal: $finalTotal,
            primaryProductType: $this->productType(),
        );
    }

    /**
     * @param array<int,array{price:float|int,quantity:int}> $cart
     */
    protected function calculateCartTotal(array $cart): float
    {
        $total = 0.0;

        foreach ($cart as $item) {
            $total += (float) $item['price'] * (int) $item['quantity'];
        }

        return $total;
    }

    protected function successfulOrderStatus(): string
    {
        return 'completed';
    }

    protected function afterItemPersisted(Product $product, int $quantity): void
    {
        // Hook for concrete checkout flows.
    }

    abstract protected function productType(): string;
}
