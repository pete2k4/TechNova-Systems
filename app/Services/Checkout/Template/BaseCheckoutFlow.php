<?php

declare(strict_types=1);

namespace App\Services\Checkout\Template;

use App\Services\Checkout\CheckoutContext;
use App\Services\Checkout\CheckoutMediator;
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
        $mediator = new CheckoutMediator();
        $cartComposite = $mediator->prepareCart($cart);
        $normalizedCart = $cartComposite->toCartPayload();

        if ($normalizedCart === []) {
            throw new RuntimeException('Checkout failed: cart is empty.');
        }

        $cartTotal = $cartComposite->getTotal();

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

        $mediator->assertInventory($cartComposite);

        $order = $mediator->createOrder(
            userId: $userId,
            cart: $cartComposite,
            cartTotal: $cartTotal,
            discountAmount: $discountAmount,
            finalTotal: $finalTotal,
            paymentMethod: $validated['payment_type'],
            paymentCredential: $validated['payment_credential'],
        );
        $paymentPlaceholderPath = $mediator->paymentPlaceholderPath($order);

        if (!$paymentStrategy->process($finalTotal)) {
            $order->markCanceled();
            throw new RuntimeException('Checkout failed: payment strategy rejected the transaction.');
        }

        $order->markPlaced();

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
            paymentPlaceholderPath: $paymentPlaceholderPath,
        );
    }

    abstract protected function productType(): string;
}
