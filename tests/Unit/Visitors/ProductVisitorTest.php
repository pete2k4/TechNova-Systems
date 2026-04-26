<?php

declare(strict_types=1);

namespace Tests\Unit\Visitors;

use App\Factories\ProductFactory;
use App\Services\ProductNotifier;
use App\Visitors\ProductFulfillmentVisitor;
use App\Visitors\ProductMarketingCopyVisitor;
use PHPUnit\Framework\TestCase;

class ProductVisitorTest extends TestCase
{
    public function test_digital_product_accepts_fulfillment_visitor(): void
    {
        $product = ProductFactory::create('digital');
        $profile = $product->accept(new ProductFulfillmentVisitor());

        $this->assertSame('download', $profile['fulfillment_mode']);
        $this->assertArrayHasKey('delivery_endpoint', $profile);
        $this->assertArrayHasKey('asset_size_bytes', $profile);
    }

    public function test_physical_product_accepts_fulfillment_visitor(): void
    {
        $product = ProductFactory::create('physical');
        $profile = $product->accept(new ProductFulfillmentVisitor());

        $this->assertSame('shipping', $profile['fulfillment_mode']);
        $this->assertArrayHasKey('shipping_cost', $profile);
        $this->assertArrayHasKey('dimensions_cm', $profile);
    }

    public function test_marketing_copy_changes_by_product_type(): void
    {
        $digitalCopy = ProductFactory::create('digital')->accept(new ProductMarketingCopyVisitor());
        $physicalCopy = ProductFactory::create('physical')->accept(new ProductMarketingCopyVisitor());

        $this->assertStringContainsStringIgnoringCase('Instant delivery', $digitalCopy);
        $this->assertStringContainsStringIgnoringCase('Ships securely', $physicalCopy);
    }

    public function test_product_notifier_builds_payload_with_visitors(): void
    {
        $repository = new class implements \App\Contracts\ProductRepositoryInterface {
            public function save(\App\Models\Product $product): bool
            {
                return true;
            }

            public function findById(int $id): ?\App\Contracts\ProductInterface
            {
                return null;
            }

            public function all(): array
            {
                return [];
            }

            public function findByType(string $type): array
            {
                return [];
            }
        };

        $notifier = new ProductNotifier($repository);
        $payload = $notifier->buildNotificationPayload(ProductFactory::create('digital'));

        $this->assertArrayHasKey('marketing_copy', $payload);
        $this->assertArrayHasKey('fulfillment', $payload);
        $this->assertSame('download', $payload['fulfillment']['fulfillment_mode']);
    }
}
