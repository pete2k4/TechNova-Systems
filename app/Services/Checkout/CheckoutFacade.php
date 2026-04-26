<?php

declare(strict_types=1);

namespace App\Services\Checkout;

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
        $this->validateCheckoutRequest($cart, $validated);

        $primaryType = $this->resolvePrimaryType($cart);

        $flow = $primaryType === 'physical'
            ? new PhysicalCheckoutFlow()
            : new DigitalCheckoutFlow();

        return $flow->execute($cart, $validated, $userId);
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
