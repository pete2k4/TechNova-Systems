<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Marketplace\Cart\Commands;

use App\Services\Marketplace\Cart\Commands\RemoveFromCartCommand;
use PHPUnit\Framework\TestCase;

class RemoveFromCartCommandTest extends TestCase
{
    public function testItRemovesItemFromCart(): void
    {
        $store = new InMemoryCartStore([
            10 => ['product_id' => 10, 'quantity' => 2],
            11 => ['product_id' => 11, 'quantity' => 1],
        ]);

        $command = new RemoveFromCartCommand($store, 10);
        $command->execute();

        $cart = $store->getCart();
        $this->assertArrayNotHasKey(10, $cart);
        $this->assertArrayHasKey(11, $cart);
    }

    public function testUndoRestoresCart(): void
    {
        $store = new InMemoryCartStore([
            10 => ['product_id' => 10, 'quantity' => 2],
        ]);

        $command = new RemoveFromCartCommand($store, 10);
        $command->execute();
        $this->assertSame([], $store->getCart());

        $command->undo();
        $this->assertArrayHasKey(10, $store->getCart());
    }
}
