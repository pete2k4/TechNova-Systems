<?php

declare(strict_types=1);

namespace App\Services\Marketplace\Cart;

use App\Contracts\CartCommandInterface;

class CartCommandInvoker
{
    /**
     * @var CartCommandInterface[]
     */
    private array $history = [];

    public function execute(CartCommandInterface $command): void
    {
        $command->execute();
        $this->history[] = $command;
    }

    public function undoLast(): bool
    {
        $command = array_pop($this->history);

        if ($command === null) {
            return false;
        }

        $command->undo();
        return true;
    }
}
