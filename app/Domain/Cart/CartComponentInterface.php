<?php

declare(strict_types=1);

namespace App\Domain\Cart;

use IteratorAggregate;

interface CartComponentInterface extends IteratorAggregate
{
    public function getTotal(): float;

    public function getQuantity(): int;
}
