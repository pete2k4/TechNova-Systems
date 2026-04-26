<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\CheckoutService;
use RuntimeException;

class CheckoutFacade
{
    /**
     * @param array<int,array{product_id:int,price:float|int,quantity:int,type:string}> $cart
     * @param array{discount_type:string,discount_value:float|int,payment_type:string,payment_credential:string} $validated
     */
    public function process(array $cart, array $validated, int $userId): CheckoutContext
    {
        if ($cart === []) {
            throw new RuntimeException('Cart is empty');
        }

        $cartTotal = $this->calculateCartTotal($cart);
        $discountConfig = [
            'type' => $validated['discount_type'],
            'value' => (float) $validated['discount_value'],
        ];
        $paymentData = [
            'type' => $validated['payment_type'],
            'credential' => $validated['payment_credential'],
        ];

        $primaryType = $this->resolvePrimaryType($cart);

        $checkoutService = new CheckoutService($primaryType);
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
            'status' => 'completed',
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

            $product = Product::find($item['product_id']);
            if ($product !== null && $product->isPhysical()) {
                $product->decrement('stock', $item['quantity']);
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
            primaryProductType: $primaryType,
        );
    }

    /**
     * @param array<int,array{price:float|int,quantity:int}> $cart
     */
    private function calculateCartTotal(array $cart): float
    {
        $total = 0.0;

        foreach ($cart as $item) {
            $total += (float) $item['price'] * (int) $item['quantity'];
        }

        return $total;
    }

    /**
     * @param array<int,array{type:string}> $cart
     */
    private function resolvePrimaryType(array $cart): string
    {
        $productTypes = array_unique(array_map(
            static fn(array $item): string => $item['type'],
            $cart
        ));

        return in_array('physical', $productTypes, true) ? 'physical' : 'digital';
    }
}
