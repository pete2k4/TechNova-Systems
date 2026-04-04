<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Marketplace\Cart;

use App\Contracts\CartCommandInterface;
use App\Services\Marketplace\Cart\CartCommandInvoker;
use PHPUnit\Framework\TestCase;

class CartCommandInvokerTest extends TestCase
{
    public function testExecuteCallsCommandAndStoresHistory(): void
    {
        $command = new DummyCartCommand();
        $invoker = new CartCommandInvoker();

        $invoker->execute($command);

        $this->assertTrue($command->executed);
    }

    public function testUndoLastCallsUndoOnLatestCommand(): void
    {
        $first = new DummyCartCommand();
        $second = new DummyCartCommand();
        $invoker = new CartCommandInvoker();

        $invoker->execute($first);
        $invoker->execute($second);

        $undone = $invoker->undoLast();

        $this->assertTrue($undone);
        $this->assertFalse($first->undone);
        $this->assertTrue($second->undone);
    }

    public function testUndoLastReturnsFalseWhenHistoryIsEmpty(): void
    {
        $invoker = new CartCommandInvoker();

        $this->assertFalse($invoker->undoLast());
    }
}

class DummyCartCommand implements CartCommandInterface
{
    public bool $executed = false;
    public bool $undone = false;

    public function execute(): void
    {
        $this->executed = true;
    }

    public function undo(): void
    {
        $this->undone = true;
    }
}
