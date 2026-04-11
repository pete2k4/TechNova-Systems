<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOs\DigitalProduct;
use App\DTOs\PhysicalProduct;

interface ProductVisitorInterface
{
    public function visitDigitalProduct(DigitalProduct $product): mixed;

    public function visitPhysicalProduct(PhysicalProduct $product): mixed;
}
