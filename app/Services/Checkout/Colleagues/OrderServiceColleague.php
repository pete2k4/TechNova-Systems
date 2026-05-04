<?php

declare(strict_types=1);

namespace App\Services\Checkout\Colleagues;

use App\Domain\Cart\CartBundleComposite;
use App\Models\Order;
use App\Models\OrderItem;

class OrderServiceColleague
{
    public function createPendingOrder(
        int $userId,
        CartBundleComposite $cart,
        float $cartTotal,
        float $discountAmount,
        float $finalTotal,
        string $paymentMethod,
        string $paymentCredential,
    ): Order {
        $credential = $paymentMethod === 'on_delivery' ? null : $paymentCredential;

        $order = Order::create([
            'user_id' => $userId,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'subtotal' => $cartTotal,
            'discount' => $discountAmount,
            'total' => $finalTotal,
            'status' => Order::STATUS_CHECKOUT_STARTED,
            'payment_method' => $paymentMethod,
            'payment_credential' => $credential,
        ]);

        foreach ($cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->getProductId(),
                'quantity' => $item->getQuantity(),
                'price' => $item->getPrice(),
            ]);
        }

        $order->markPendingPaymentPage();

        return $order;
    }
}
