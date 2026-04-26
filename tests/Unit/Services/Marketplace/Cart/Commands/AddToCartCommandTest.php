<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Marketplace\Cart\Commands;

use App\Models\Product;
use App\Services\Marketplace\Cart\Commands\AddToCartCommand;
use PHPUnit\Framework\TestCase;

class AddToCartCommandTest extends TestCase
{
    public function testItAddsANewProductToTheCart(): void
    {
        $store = new InMemoryCartStore();
        $product = new Product([
            'id' => 5,
            'name' => 'RTX 4070',
            'price' => 599.99,
            'type' => 'physical',
        ]);
        $product->id = 5;

        $command = new AddToCartCommand($store, $product, 2);
        $command->execute();

        $cart = $store->getCart();

        $this->assertArrayHasKey(5, $cart);
        $this->assertSame(2, $cart[5]['quantity']);
        $this->assertSame('RTX 4070', $cart[5]['name']);
    }

    public function testItIncrementsQuantityWhenProductAlreadyExists(): void
    {
        $store = new InMemoryCartStore([
            5 => [
                'product_id' => 5,
                'name' => 'RTX 4070',
                'price' => 599.99,
                'type' => 'physical',
                'quantity' => 1,
            ],
        ]);

        $product = new Product([
            'id' => 5,
            'name' => 'RTX 4070',
            'price' => 599.99,
            'type' => 'physical',
        ]);
        $product->id = 5;

        $command = new AddToCartCommand($store, $product, 3);
        $command->execute();

        $this->assertSame(4, $store->getCart()[5]['quantity']);
    }

    public function testUndoRestoresPreviousCartState(): void
    {
        $store = new InMemoryCartStore();
        $product = new Product([
            'id' => 7,
            'name' => 'Windows License',
            'price' => 199.99,
            'type' => 'digital',
        ]);
        $product->id = 7;

        $command = new AddToCartCommand($store, $product, 1);
        $command->execute();
        $this->assertArrayHasKey(7, $store->getCart());

        $command->undo();
        $this->assertSame([], $store->getCart());
    }
}
