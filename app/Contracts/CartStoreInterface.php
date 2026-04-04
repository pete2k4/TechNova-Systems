<?php

declare(strict_types=1);

namespace App\Contracts;

interface CartStoreInterface
{
    /**
     * @return array<int|string, array<string, mixed>>
     */
    public function getCart(): array;

    /**
     * @param array<int|string, array<string, mixed>> $cart
     */
    public function putCart(array $cart): void;

    public function clearCart(): void;
}
