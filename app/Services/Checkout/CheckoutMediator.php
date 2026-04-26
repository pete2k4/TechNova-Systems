<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\Domain\Cart\CartBundleComposite;
use App\Models\Order;
use App\Services\Checkout\Colleagues\CartServiceColleague;
use App\Services\Checkout\Colleagues\InventoryServiceColleague;
use App\Services\Checkout\Colleagues\OrderServiceColleague;
use Throwable;

class CheckoutMediator
{
    public function __construct(
        private readonly CartServiceColleague $cartColleague = new CartServiceColleague(),
        private readonly InventoryServiceColleague $inventoryColleague = new InventoryServiceColleague(),
        private readonly OrderServiceColleague $orderColleague = new OrderServiceColleague(),
    ) {
    }

    /**
     * @param array<int,array{product_id:int,price:float|int,quantity:int,type:string}> $cart
     */
    public function prepareCart(array $cart): CartBundleComposite
    {
        return $this->cartColleague->prepare($cart);
    }

    public function assertInventory(CartBundleComposite $cart): void
    {
        $this->inventoryColleague->assertStockAvailable($cart);
    }

    public function createOrder(
        int $userId,
        CartBundleComposite $cart,
        float $cartTotal,
        float $discountAmount,
        float $finalTotal,
        string $paymentMethod,
        string $paymentCredential,
    ): Order {
        return $this->orderColleague->createPendingOrder(
            userId: $userId,
            cart: $cart,
            cartTotal: $cartTotal,
            discountAmount: $discountAmount,
            finalTotal: $finalTotal,
            paymentMethod: $paymentMethod,
            paymentCredential: $paymentCredential,
        );
    }

    public function paymentPlaceholderPath(Order $order): string
    {
        try {
            return route('checkout.payment-placeholder', ['orderId' => $order->id], false);
        } catch (Throwable) {
            return '/checkout/payment-placeholder/' . $order->id;
        }
    }
}
