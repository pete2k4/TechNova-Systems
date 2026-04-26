<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Marketplace\Cart\Commands;

use App\Services\Marketplace\Cart\Commands\ClearCartCommand;
use PHPUnit\Framework\TestCase;

class ClearCartCommandTest extends TestCase
{
    public function testItClearsCart(): void
    {
        $store = new InMemoryCartStore([
            1 => ['product_id' => 1, 'quantity' => 1],
        ]);

        $command = new ClearCartCommand($store);
        $command->execute();

        $this->assertSame([], $store->getCart());
    }

    public function testUndoRestoresPreviousCart(): void
    {
        $store = new InMemoryCartStore([
            1 => ['product_id' => 1, 'quantity' => 1],
            2 => ['product_id' => 2, 'quantity' => 3],
        ]);

        $command = new ClearCartCommand($store);
        $command->execute();
        $this->assertSame([], $store->getCart());

        $command->undo();

        $this->assertCount(2, $store->getCart());
        $this->assertArrayHasKey(1, $store->getCart());
        $this->assertArrayHasKey(2, $store->getCart());
    }
}
