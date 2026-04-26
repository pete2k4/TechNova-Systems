<?php

declare(strict_types=1);

namespace App\Services\Checkout\Validation\Handlers;

use App\Services\Checkout\Validation\CheckoutValidationContext;

abstract class CheckoutValidationHandler
{
    private ?CheckoutValidationHandler $next = null;

    public function setNext(CheckoutValidationHandler $next): CheckoutValidationHandler
    {
        $this->next = $next;

        return $next;
    }

    final public function handle(CheckoutValidationContext $context): void
    {
        $this->validate($context);

        if ($this->next !== null) {
            $this->next->handle($context);
        }
    }

    abstract protected function validate(CheckoutValidationContext $context): void;
}
