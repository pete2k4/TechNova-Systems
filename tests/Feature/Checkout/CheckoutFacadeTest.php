<?php

declare(strict_types=1);

namespace Tests\Feature\Checkout;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\Checkout\CheckoutFacade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class CheckoutFacadeTest extends TestCase
{
    use RefreshDatabase;

    private function createCategory(): Category
    {
        return Category::query()->create([
            'name' => 'Accessories',
            'slug' => 'accessories',
            'description' => 'Computer accessories',
            'type' => 'mixed',
        ]);
    }

    public function test_process_orchestrates_cart_to_order_flow(): void
    {
        $user = User::factory()->create();
        $category = $this->createCategory();

        $physicalProduct = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Gaming Mouse',
            'slug' => 'gaming-mouse',
            'description' => 'Wired gaming mouse',
            'price' => 100,
            'type' => 'physical',
            'sku' => 'SKU-MOUSE-001',
            'stock' => 10,
            'is_active' => true,
        ]);

        $digitalProduct = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Antivirus License',
            'slug' => 'antivirus-license',
            'description' => 'One year antivirus license',
            'price' => 50,
            'type' => 'digital',
            'sku' => 'SKU-AV-001',
            'stock' => null,
            'is_active' => true,
        ]);

        $cart = [
            [
                'product_id' => $physicalProduct->id,
                'price' => 100,
                'quantity' => 2,
                'type' => 'physical',
            ],
            [
                'product_id' => $digitalProduct->id,
                'price' => 50,
                'quantity' => 1,
                'type' => 'digital',
            ],
        ];

        $validated = [
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'payment_type' => 'credit_card',
            'payment_credential' => '4111111111111111',
        ];

        $facade = app(CheckoutFacade::class);
        $context = $facade->process($cart, $validated, $user->id);

        $this->assertSame(250.0, $context->cartTotal);
        $this->assertSame(25.0, $context->discountAmount);
        $this->assertSame(225.0, $context->finalTotal);
        $this->assertSame('physical', $context->primaryProductType);
        $this->assertSame('/checkout/payment-placeholder/' . $context->order->id, $context->paymentPlaceholderPath);

        $this->assertDatabaseHas('orders', [
            'id' => $context->order->id,
            'user_id' => $user->id,
            'payment_method' => 'credit_card',
            'status' => 'placed',
        ]);

        $this->assertSame(2, OrderItem::query()->where('order_id', $context->order->id)->count());

        $physicalProduct->refresh();
        $this->assertLessThan(10, (int) $physicalProduct->stock);
        $this->assertLessThanOrEqual(8, (int) $physicalProduct->stock);
    }

    public function test_process_fails_when_stock_is_insufficient(): void
    {
        $user = User::factory()->create();
        $category = $this->createCategory();

        $physicalProduct = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Mechanical Keyboard',
            'slug' => 'mechanical-keyboard',
            'description' => 'Compact mechanical keyboard',
            'price' => 120,
            'type' => 'physical',
            'sku' => 'SKU-KB-001',
            'stock' => 1,
            'is_active' => true,
        ]);

        $cart = [
            [
                'product_id' => $physicalProduct->id,
                'price' => 120,
                'quantity' => 2,
                'type' => 'physical',
            ],
        ];

        $validated = [
            'discount_type' => 'fixed',
            'discount_value' => 10,
            'payment_type' => 'paypal',
            'payment_credential' => 'student@example.com',
        ];

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Insufficient stock for product');

        app(CheckoutFacade::class)->process($cart, $validated, $user->id);
    }

    public function test_process_uses_digital_template_flow_for_digital_cart(): void
    {
        $user = User::factory()->create();
        $category = $this->createCategory();

        $digitalProduct = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Cloud Backup License',
            'slug' => 'cloud-backup-license',
            'description' => 'One year cloud backup license',
            'price' => 80,
            'type' => 'digital',
            'sku' => 'SKU-CLOUD-001',
            'stock' => null,
            'is_active' => true,
        ]);

        $cart = [
            [
                'product_id' => $digitalProduct->id,
                'price' => 80,
                'quantity' => 1,
                'type' => 'digital',
            ],
        ];

        $validated = [
            'discount_type' => 'fixed',
            'discount_value' => 5,
            'payment_type' => 'paypal',
            'payment_credential' => 'student@example.com',
        ];

        $context = app(CheckoutFacade::class)->process($cart, $validated, $user->id);

        $this->assertSame('digital', $context->primaryProductType);
        $this->assertSame('Digital Product Commerce', $context->factoryName);
        $this->assertSame(75.0, $context->finalTotal);
        $this->assertSame('/checkout/payment-placeholder/' . $context->order->id, $context->paymentPlaceholderPath);
    }

    public function test_process_accepts_session_cart_shape_with_associative_keys(): void
    {
        $user = User::factory()->create();
        $category = $this->createCategory();

        $physicalProduct = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'External SSD',
            'slug' => 'external-ssd',
            'description' => 'Portable SSD storage',
            'price' => 200,
            'type' => 'physical',
            'sku' => 'SKU-SSD-001',
            'stock' => 5,
            'is_active' => true,
        ]);

        $cart = [
            $physicalProduct->id => [
                'product_id' => $physicalProduct->id,
                'name' => 'External SSD',
                'price' => 200,
                'quantity' => 1,
                'type' => 'physical',
            ],
        ];

        $validated = [
            'discount_type' => 'fixed',
            'discount_value' => 20,
            'payment_type' => 'paypal',
            'payment_credential' => 'student@example.com',
        ];

        $context = app(CheckoutFacade::class)->process($cart, $validated, $user->id);

        $this->assertSame(200.0, $context->cartTotal);
        $this->assertSame(180.0, $context->finalTotal);
        $this->assertSame('physical', $context->primaryProductType);
        $this->assertSame('/checkout/payment-placeholder/' . $context->order->id, $context->paymentPlaceholderPath);
    }
}
