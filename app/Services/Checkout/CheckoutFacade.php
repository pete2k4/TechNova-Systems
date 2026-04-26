<?php

declare(strict_types=1);

namespace App\Services\Checkout;

use App\Domain\Cart\CartBundleComposite;
use App\Services\Checkout\Pipeline\ValidateCartHandler;
use App\Services\Checkout\Pipeline\ValidateCheckoutModeHandler;
use App\Services\Checkout\Pipeline\ValidateDiscountHandler;
use App\Services\Checkout\Pipeline\ValidateStockHandler;
use App\Services\Checkout\Template\DigitalCheckoutFlow;
use App\Services\Checkout\Template\PhysicalCheckoutFlow;

class CheckoutFacade
{
    /**
     * @param array<int,array{product_id:int,price:float|int,quantity:int,type:string}> $cart
     * @param array{discount_type:string,discount_value:float|int,payment_type:string,payment_credential:string} $validated
     */
    public function process(array $cart, array $validated, int $userId): CheckoutContext
    {
        $cartComposite = CartBundleComposite::fromSessionCart($cart, 'Checkout cart');
        $normalizedCart = $cartComposite->toCartPayload();

        $this->validateCheckoutRequest($normalizedCart, $validated);

        $primaryType = $this->resolvePrimaryType($cartComposite);

        $flow = $primaryType === 'physical'
            ? new PhysicalCheckoutFlow()
            : new DigitalCheckoutFlow();

        return $flow->execute($normalizedCart, $validated, $userId);
    }

    private function resolvePrimaryType(CartBundleComposite $cart): string
    {
        return $cart->hasPhysicalProducts() ? 'physical' : 'digital';
    }

    /**
     * @param array<int,array{product_id:int,price:float|int,quantity:int,type:string}> $cart
     * @param array{discount_type:string,discount_value:float|int,payment_type:string,payment_credential:string} $validated
     */
    private function validateCheckoutRequest(array $cart, array $validated): void
    {
        $cartValidator = new ValidateCartHandler();
        $stockValidator = new ValidateStockHandler();
        $discountValidator = new ValidateDiscountHandler();
        $checkoutModeValidator = new ValidateCheckoutModeHandler();

        $cartValidator
            ->setNext($stockValidator)
            ->setNext($discountValidator)
            ->setNext($checkoutModeValidator);

        $cartValidator->handle($cart, $validated);
    }
}
