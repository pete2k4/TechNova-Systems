<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Checkout\CheckoutFacade;
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
    public function process(Request $request, CheckoutFacade $checkoutFacade): View
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
            $context = $checkoutFacade->process(
                $cart,
                $validated,
                (int) (auth()->id() ?? 1)
            );

            // Clear cart
            session()->forget('cart');

            return view('marketplace.order-confirmation', [
                'order' => $context->order,
                'factoryName' => $context->factoryName,
                'factoryClass' => $context->factoryClass,
                'discount' => $context->discountConfig,
                'discountStrategy' => $context->discountStrategyClass,
                'paymentStrategy' => $context->paymentStrategyName,
            ]);
        } catch (\Exception $e) {
            abort(404, 'Checkout failed: ' . $e->getMessage());
        }
    }
}

