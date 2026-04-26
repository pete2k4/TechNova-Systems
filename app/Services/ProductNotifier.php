<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ProductInterface;
use App\Factories\ProductFactory;
use App\Models\Product;

class ProductNotifier
{
    /**
     * @param array $productData
     * @return void
     */
    public function notifyNewProduct(array $productData): void
    {
        $product = ProductFactory::fromArray($productData);
        
        // Notification logic would go here
        // Get subscribers and send notifications
        // Mail::to($subscribers)->send(new NewProductMail($product));
    }

    /**
     * @param string $type
     * @param array $data
     * @return void
     */
    public function notifyNewProductByType(string $type, array $data = []): void
    {
        $product = ProductFactory::create($type, $data);
        
        // Mail::to($subscribers)->send(new NewProductMail($product));
    }

    /**
     * @param ProductInterface $product
     * @param float $oldPrice
     * @return void
     */
    public function notifyPriceDrop(ProductInterface $product, float $oldPrice): void
    {
        $newPrice = $product->getPrice();
        $discount = $oldPrice - $newPrice;
        
        // Notification logic would go here with discount information
        // Mail::to($subscribers)->send(new PriceDropMail($product, $oldPrice, $newPrice, $discount));
    }

    /**
     * @param array $productData
     * @param float $oldPrice
     * @return void
     */
    public function notifyPriceDropByData(array $productData, float $oldPrice): void
    {
        $product = ProductFactory::fromArray($productData);
        $this->notifyPriceDrop($product, $oldPrice);
    }
}
