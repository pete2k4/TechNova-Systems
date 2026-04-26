<?php

declare(strict_types=1);

namespace App\Services\Checkout\Pipeline;

abstract class AbstractCheckoutHandler implements CheckoutHandlerInterface
{
    protected ?CheckoutHandlerInterface $next = null;

    public function setNext(CheckoutHandlerInterface $handler): CheckoutHandlerInterface
    {
        $this->next = $handler;

        return $handler;
    }

    /**
     * @param array<int,array{product_id:int,price:float|int,quantity:int,type:string}> $cart
     * @param array{discount_type:string,discount_value:float|int,payment_type:string,payment_credential:string} $validated
     */
    protected function next(array $cart, array $validated): void
    {
        if ($this->next !== null) {
            $this->next->handle($cart, $validated);
        }
    }
}
