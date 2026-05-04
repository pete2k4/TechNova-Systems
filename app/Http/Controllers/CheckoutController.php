<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Cart\CartBundleComposite;
use App\Services\Checkout\CheckoutFacade;
use Illuminate\Http\RedirectResponse;
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
        $cartComposite = CartBundleComposite::fromSessionCart($cart, 'Session cart');
        $normalizedCart = $cartComposite->toCartPayload();

        if ($normalizedCart === []) {
            return redirect()->route('marketplace.cart')->with('error', 'Cart is empty.');
        }

        return view('marketplace.checkout', [
            'cart' => $normalizedCart,
            'subtotal' => $cartComposite->getTotal(),
        ]);
    }

    /**
     * Process checkout and create order.
     */
    public function process(Request $request, CheckoutFacade $checkoutFacade): View|RedirectResponse
    {
        $cart = session()->get('cart', []);
        $cartComposite = CartBundleComposite::fromSessionCart($cart, 'Session cart');
        $normalizedCart = $cartComposite->toCartPayload();

        if ($normalizedCart === []) {
            return redirect()->route('marketplace.cart')->with('error', 'Cart is empty.');
        }

        $validated = $request->validate([
            'payment_type' => 'required|in:credit_card,paypal,on_delivery',
            'payment_credential' => 'required_unless:payment_type,on_delivery|nullable|string',
        ]);

        $validated['discount_type'] = 'fixed';
        $validated['discount_value'] = 0;

        try {
            $context = $checkoutFacade->process(
                $normalizedCart,
                $validated,
                (int) $request->user()->id
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
                'paymentPlaceholderPath' => $context->paymentPlaceholderPath,
            ]);
        } catch (\Throwable $e) {
            return back()
                ->with('error', 'Checkout failed: ' . $e->getMessage())
                ->withInput();
        }
    }
}

