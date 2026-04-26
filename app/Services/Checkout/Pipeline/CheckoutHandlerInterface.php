<?php

declare(strict_types=1);

namespace App\Services\Checkout\Pipeline;

interface CheckoutHandlerInterface
{
    public function setNext(CheckoutHandlerInterface $handler): CheckoutHandlerInterface;

    /**
     * @param array<int,array{product_id:int,price:float|int,quantity:int,type:string}> $cart
     * @param array{discount_type:string,discount_value:float|int,payment_type:string,payment_credential:string} $validated
     */
    public function handle(array $cart, array $validated): void;
}
