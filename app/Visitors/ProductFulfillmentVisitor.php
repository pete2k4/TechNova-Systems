<?php

declare(strict_types=1);

namespace App\Visitors;

use App\Contracts\ProductVisitorInterface;
use App\DTOs\DigitalProduct;
use App\DTOs\PhysicalProduct;

final class ProductFulfillmentVisitor implements ProductVisitorInterface
{
    /**
     * @return array<string, mixed>
     */
    public function visitDigitalProduct(DigitalProduct $product): mixed
    {
        return [
            'fulfillment_mode' => 'download',
            'delivery_endpoint' => $product->getDownloadUrl(),
            'asset_size_bytes' => $product->getFileSize(),
            'requires_license' => $product->getLicenseKey() !== null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function visitPhysicalProduct(PhysicalProduct $product): mixed
    {
        return [
            'fulfillment_mode' => 'shipping',
            'shipping_cost' => $product->getShippingCost(),
            'weight_kg' => $product->getWeight(),
            'dimensions_cm' => $product->getDimensions(),
        ];
    }
}
