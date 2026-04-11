<?php

declare(strict_types=1);

namespace Tests\Unit\States;

use App\Models\Order;
use LogicException;
use PHPUnit\Framework\TestCase;

class OrderStateTest extends TestCase
{
    public function test_pending_order_can_transition_to_completed(): void
    {
        $order = new Order(['status' => 'pending']);

        $order->transitionToCompleted();

        $this->assertSame('completed', $order->status);
    }

    public function test_pending_order_can_transition_to_failed(): void
    {
        $order = new Order(['status' => 'pending']);

        $order->transitionToFailed();

        $this->assertSame('failed', $order->status);
    }

    public function test_completed_order_can_transition_to_refunded(): void
    {
        $order = new Order(['status' => 'completed']);

        $order->transitionToRefunded();

        $this->assertSame('refunded', $order->status);
    }

    public function test_completed_order_cannot_transition_to_failed(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Cannot fail order when state is "completed".');

        $order = new Order(['status' => 'completed']);

        $order->transitionToFailed();
    }

    public function test_failed_order_cannot_transition_to_completed(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Cannot complete order when state is "failed".');

        $order = new Order(['status' => 'failed']);

        $order->transitionToCompleted();
    }
}
