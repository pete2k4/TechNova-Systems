<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ProductInterface;
use App\Contracts\ProductRepositoryInterface;
use App\Factories\ProductFactory;
use App\Models\Product;
use App\Visitors\ProductFulfillmentVisitor;
use App\Visitors\ProductMarketingCopyVisitor;

class ProductNotifier
{
    private ProductRepositoryInterface $productRepository;
    private ProductFulfillmentVisitor $fulfillmentVisitor;
    private ProductMarketingCopyVisitor $marketingCopyVisitor;

    public function __construct(?ProductRepositoryInterface $productRepository = null)
    {
        $this->productRepository = $productRepository ?? app(ProductRepositoryInterface::class);
        $this->fulfillmentVisitor = new ProductFulfillmentVisitor();
        $this->marketingCopyVisitor = new ProductMarketingCopyVisitor();
    }

    /**
     * @param array $productData
     * @return void
     */
    public function notifyNewProduct(array $productData): void
    {
        $product = ProductFactory::fromArray($productData);
        $payload = $this->buildNotificationPayload($product);
        
        // Notification logic would go here
        // Get subscribers and send notifications
        // Mail::to($subscribers)->send(new NewProductMail($product, $payload));
    }

    /**
     * @param string $type
     * @param array $data
     * @return void
     */
    public function notifyNewProductByType(string $type, array $data = []): void
    {
        $product = ProductFactory::create($type, $data);
        $payload = $this->buildNotificationPayload($product);
        
        // Mail::to($subscribers)->send(new NewProductMail($product, $payload));
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
        $payload = $this->buildNotificationPayload($product);
        
        // Notification logic would go here with discount information
        // Mail::to($subscribers)->send(new PriceDropMail($product, $oldPrice, $newPrice, $discount, $payload));
    }

    /**
     * @param array $productData
     * @param float $oldPrice
     * @return void
     */
    public function notifyPriceDropByData(array $productData, float $oldPrice): void
    {
        $product = isset($productData['id'])
            ? $this->productRepository->findById((int) $productData['id'])
            : null;

        $product ??= ProductFactory::fromArray($productData);
        $this->notifyPriceDrop($product, $oldPrice);
    }

    /**
     * @return array<string, mixed>
     */
    public function buildNotificationPayload(ProductInterface $product): array
    {
        return [
            'marketing_copy' => $product->accept($this->marketingCopyVisitor),
            'fulfillment' => $product->accept($this->fulfillmentVisitor),
        ];
    }
}
