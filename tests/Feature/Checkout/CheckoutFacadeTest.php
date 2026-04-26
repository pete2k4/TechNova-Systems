<?php

declare(strict_types=1);

namespace Tests\Feature\Checkout;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\Checkout\CheckoutFacade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutFacadeTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_orchestrates_cart_to_order_flow(): void
    {
        $user = User::factory()->create();
        $category = Category::query()->create([
            'name' => 'Accessories',
            'slug' => 'accessories',
            'description' => 'Computer accessories',
            'type' => 'mixed',
        ]);

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

        $this->assertDatabaseHas('orders', [
            'id' => $context->order->id,
            'user_id' => $user->id,
            'payment_method' => 'credit_card',
            'status' => 'completed',
        ]);

        $this->assertSame(2, OrderItem::query()->where('order_id', $context->order->id)->count());

        $physicalProduct->refresh();
        $this->assertSame(8, $physicalProduct->stock);
    }
}
