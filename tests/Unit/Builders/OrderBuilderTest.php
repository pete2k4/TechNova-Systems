<?php

declare(strict_types=1);

namespace Tests\Unit\Builders;

use App\Builders\OrderBuilder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * OrderBuilder Test Suite
 * 
 * Demonstrates the Builder pattern usage for creating complex Order objects.
 */
class OrderBuilderTest extends TestCase
{
    use RefreshDatabase;

    private OrderBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new OrderBuilder();
    }

    /** @test */
    public function it_builds_a_simple_order_with_one_item(): void
    {
        $user = User::factory()->create();

        $order = $this->builder
            ->forUser($user)
            ->addItem(1, 2, 29.99)
            ->withPaymentMethod('credit_card', 'visa_1234')
            ->build();

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals(59.98, $order->subtotal);
        $this->assertEquals(0.00, $order->discount);
        $this->assertEquals(59.98, $order->total);
        $this->assertEquals('credit_card', $order->payment_method);
        $this->assertEquals('pending', $order->status);
    }

    /** @test */
    public function it_builds_order_with_multiple_items(): void
    {
        $user = User::factory()->create();

        $this->builder
            ->forUser($user->id)
            ->addItem(1, 2, 29.99)    // $59.98
            ->addItem(2, 1, 49.99)    // $49.99
            ->addItem(3, 3, 9.99)     // $29.97
            ->withPaymentMethod('paypal', 'user@example.com');

        $order = $this->builder->build();

        $this->assertEquals(139.94, $order->subtotal);
        $this->assertEquals(139.94, $order->total);
        $this->assertCount(3, $this->builder->getItems());
    }

    /** @test */
    public function it_applies_discount_correctly(): void
    {
        $user = User::factory()->create();

        $order = $this->builder
            ->forUser($user)
            ->addItem(1, 2, 50.00)    // $100.00 subtotal
            ->withDiscount(15.00)     // $15 off
            ->withPaymentMethod('credit_card')
            ->build();

        $this->assertEquals(100.00, $order->subtotal);
        $this->assertEquals(15.00, $order->discount);
        $this->assertEquals(85.00, $order->total);
    }

    /** @test */
    public function it_adds_multiple_items_at_once(): void
    {
        $user = User::factory()->create();

        $items = [
            ['product_id' => 1, 'quantity' => 2, 'price' => 29.99],
            ['product_id' => 2, 'quantity' => 1, 'price' => 49.99],
            ['product_id' => 3, 'quantity' => 3, 'price' => 9.99],
        ];

        $this->builder
            ->forUser($user)
            ->addItems($items)
            ->withPaymentMethod('credit_card');

        $order = $this->builder->build();

        $this->assertEquals(139.94, $order->subtotal);
        $this->assertCount(3, $this->builder->getItems());
    }

    /** @test */
    public function it_sets_custom_order_number(): void
    {
        $user = User::factory()->create();

        $order = $this->builder
            ->forUser($user)
            ->addItem(1, 1, 29.99)
            ->withOrderNumber('CUSTOM-12345')
            ->withPaymentMethod('credit_card')
            ->build();

        $this->assertEquals('CUSTOM-12345', $order->order_number);
    }

    /** @test */
    public function it_generates_order_number_automatically(): void
    {
        $user = User::factory()->create();

        $order = $this->builder
            ->forUser($user)
            ->addItem(1, 1, 29.99)
            ->withPaymentMethod('credit_card')
            ->build();

        $this->assertNotNull($order->order_number);
        $this->assertStringStartsWith('ORD-', $order->order_number);
    }

    /** @test */
    public function it_sets_custom_status(): void
    {
        $user = User::factory()->create();

        $order = $this->builder
            ->forUser($user)
            ->addItem(1, 1, 29.99)
            ->withStatus('completed')
            ->withPaymentMethod('credit_card')
            ->build();

        $this->assertEquals('completed', $order->status);
    }

    /** @test */
    public function it_throws_exception_when_user_is_missing(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Order must have a user');

        $this->builder
            ->addItem(1, 1, 29.99)
            ->withPaymentMethod('credit_card')
            ->build();
    }

    /** @test */
    public function it_throws_exception_when_items_are_missing(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Order must have at least one item');

        $user = User::factory()->create();

        $this->builder
            ->forUser($user)
            ->withPaymentMethod('credit_card')
            ->build();
    }

    /** @test */
    public function it_throws_exception_when_payment_method_is_missing(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Order must have a payment method');

        $user = User::factory()->create();

        $this->builder
            ->forUser($user)
            ->addItem(1, 1, 29.99)
            ->build();
    }

    /** @test */
    public function it_provides_order_summary_during_construction(): void
    {
        $user = User::factory()->create();

        $this->builder
            ->forUser($user)
            ->addItem(1, 2, 29.99)
            ->addItem(2, 1, 49.99);

        $summary = $this->builder->getSummary();

        $this->assertEquals($user->id, $summary['user_id']);
        $this->assertEquals(2, $summary['items_count']);
        $this->assertEquals(109.97, $summary['subtotal']);
        $this->assertEquals(0.00, $summary['discount']);
        $this->assertEquals(109.97, $summary['total']);
    }

    /** @test */
    public function it_can_be_reset_for_reuse(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Build first order
        $order1 = $this->builder
            ->forUser($user1)
            ->addItem(1, 1, 29.99)
            ->withPaymentMethod('credit_card')
            ->build();

        $this->assertEquals($user1->id, $order1->user_id);
        $this->assertEquals(29.99, $order1->total);

        // Reset and build second order
        $order2 = $this->builder
            ->reset()
            ->forUser($user2)
            ->addItem(2, 2, 19.99)
            ->withPaymentMethod('paypal')
            ->build();

        $this->assertEquals($user2->id, $order2->user_id);
        $this->assertEquals(39.98, $order2->total);
        $this->assertNotEquals($order1->user_id, $order2->user_id);
    }

    /** @test */
    public function it_builds_and_saves_order_with_items(): void
    {
        $user = User::factory()->create();

        // Create category to satisfy foreign key constraints
        $category = \App\Models\Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        // Create products to satisfy foreign key constraints
        $product1 = \App\Models\Product::create([
            'category_id' => $category->id,
            'name' => 'Test Product 1',
            'slug' => 'test-product-1',
            'description' => 'Test product description',
            'price' => 29.99,
            'type' => 'physical',
            'sku' => 'TEST-001',
            'stock' => 100,
        ]);

        $product2 = \App\Models\Product::create([
            'category_id' => $category->id,
            'name' => 'Test Product 2',
            'slug' => 'test-product-2',
            'description' => 'Test product description',
            'price' => 49.99,
            'type' => 'physical',
            'sku' => 'TEST-002',
            'stock' => 50,
        ]);

        $order = $this->builder
            ->forUser($user)
            ->addItem($product1->id, 2, 29.99)
            ->addItem($product2->id, 1, 49.99)
            ->withDiscount(10.00)
            ->withPaymentMethod('credit_card', 'visa_1234')
            ->buildAndSave();

        // Assert order is saved
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $user->id,
            'subtotal' => 109.97,
            'discount' => 10.00,
            'total' => 99.97,
            'payment_method' => 'credit_card',
        ]);

        // Assert order items are saved
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'price' => 29.99,
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => 49.99,
        ]);

        // Assert items are loaded
        $this->assertCount(2, $order->items);
    }

    /** @test */
    public function it_ensures_total_never_goes_negative(): void
    {
        $user = User::factory()->create();

        $order = $this->builder
            ->forUser($user)
            ->addItem(1, 1, 50.00)
            ->withDiscount(100.00)  // Discount larger than subtotal
            ->withPaymentMethod('credit_card')
            ->build();

        $this->assertEquals(50.00, $order->subtotal);
        $this->assertEquals(100.00, $order->discount);
        $this->assertEquals(0.00, $order->total); // Should be 0, not negative
    }

    /** @test */
    public function it_demonstrates_fluent_interface(): void
    {
        $user = User::factory()->create();

        // All methods return $this, enabling fluent chaining
        $order = $this->builder
            ->forUser($user)
            ->addItem(1, 2, 29.99)
            ->addItem(2, 1, 49.99)
            ->withDiscount(15.00)
            ->withPaymentMethod('credit_card', 'visa_1234')
            ->withStatus('pending')
            ->withOrderNumber('TEST-ORDER-001')
            ->build();

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('TEST-ORDER-001', $order->order_number);
    }
}
