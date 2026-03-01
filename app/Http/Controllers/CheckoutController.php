<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Factories\CommerceFactorySelector;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Show checkout form for cart items.
     */
    public function showCheckout(): View
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            abort(404, 'Cart is empty');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        return view('marketplace.checkout', [
            'cart' => $cart,
            'subtotal' => $subtotal,
        ]);
    }

    /**
     * Process checkout and create order.
     */
    public function process(Request $request): View
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            abort(404, 'Cart is empty');
        }

        $validated = $request->validate([
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'payment_type' => 'required|in:credit_card,paypal',
            'payment_credential' => 'required|string',
        ]);

        try {
            $cartTotal = 0;
            foreach ($cart as $item) {
                $cartTotal += $item['price'] * $item['quantity'];
            }

            $discountAmount = $validated['discount_type'] === 'percentage'
                ? $cartTotal * ($validated['discount_value'] / 100)
                : $validated['discount_value'];

            $finalTotal = max(0, $cartTotal - $discountAmount);

            $discountConfig = [
                'type' => $validated['discount_type'],
                'value' => (float) $validated['discount_value'],
            ];

            $paymentData = [
                'type' => $validated['payment_type'],
                'credential' => $validated['payment_credential'],
            ];

            // Determine product types in cart
            $productTypes = array_unique(array_map(fn($item) => $item['type'], $cart));
            $primaryType = in_array('physical', $productTypes) ? 'physical' : 'digital';

            $checkout = new CheckoutService($primaryType);
            $factory = $checkout->getFactory();

            // Create order
            $order = Order::create([
                'user_id' => auth()->id() ?? 1, // Default guest user
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'subtotal' => $cartTotal,
                'discount' => $discountAmount,
                'total' => $finalTotal,
                'status' => 'completed',
                'payment_method' => $validated['payment_type'],
                'payment_credential' => $validated['payment_credential'],
            ]);

            // Add items to order
            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                // Reduce stock for physical products
                $product = Product::find($item['product_id']);
                if ($product->isPhysical()) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            // Clear cart
            session()->forget('cart');

            return view('marketplace.order-confirmation', [
                'order' => $order,
                'factoryName' => $factory->getFamilyName(),
                'factoryClass' => class_basename($factory::class),
                'discount' => $discountConfig,
            ]);
        } catch (\Exception $e) {
            abort(404, 'Checkout failed: ' . $e->getMessage());
        }
    }
}

