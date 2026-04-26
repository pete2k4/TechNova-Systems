<?php

declare(strict_types=1);

namespace App\Contracts;

interface PrototypeInterface
{
    public function clonePrototype(): static;
}
