<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Checkout\Mediator\CheckoutProcessMediator;
use Illuminate\Support\Facades\DB;
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
    public function process(Request $request, CheckoutProcessMediator $checkoutMediator): View
    {
        $cart = session()->get('cart', []);

        $validated = $request->validate([
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'payment_type' => 'required|in:credit_card,paypal',
            'payment_credential' => 'required|string',
        ]);

        try {
            $discountConfig = [
                'type' => $validated['discount_type'],
                'value' => (float) $validated['discount_value'],
            ];

            $paymentData = [
                'type' => $validated['payment_type'],
                'credential' => $validated['payment_credential'],
            ];

            $result = $checkoutMediator->mediateCheckout($cart, $discountConfig, $paymentData);

            $order = DB::transaction(function () use ($cart, $result, $validated): Order {
                $order = Order::create([
                    'user_id' => auth()->id() ?? 1, // Default guest user
                    'order_number' => 'ORD-' . strtoupper(uniqid()),
                    'subtotal' => $result->subtotal,
                    'discount' => $result->discountAmount,
                    'total' => $result->finalTotal,
                    'status' => 'pending',
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
                }

                if ($result->paymentSuccess) {
                    $order->transitionToCompleted()->save();
                } else {
                    $order->transitionToFailed()->save();
                }

                return $order;
            });

            // Clear cart
            session()->forget('cart');

            return view('marketplace.order-confirmation', [
                'order' => $order,
                'factoryName' => $result->factoryFamilyName,
                'factoryClass' => $result->factoryClass,
                'discount' => $discountConfig,
            ]);
        } catch (\Exception $e) {
            abort(404, 'Checkout failed: ' . $e->getMessage());
        }
    }
}

