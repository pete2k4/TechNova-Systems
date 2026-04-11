<?php

declare(strict_types=1);

namespace App\Visitors;

use App\Contracts\ProductVisitorInterface;
use App\DTOs\DigitalProduct;
use App\DTOs\PhysicalProduct;

final class ProductMarketingCopyVisitor implements ProductVisitorInterface
{
    public function visitDigitalProduct(DigitalProduct $product): mixed
    {
        return sprintf(
            'Instant delivery: %s is ready to download right after purchase.',
            $product->getName(),
        );
    }

    public function visitPhysicalProduct(PhysicalProduct $product): mixed
    {
        return sprintf(
            'Ships securely: %s will be packed and delivered to your address.',
            $product->getName(),
        );
    }
}
