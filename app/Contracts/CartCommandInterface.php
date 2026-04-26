<?php

declare(strict_types=1);

namespace App\Contracts;

interface CartCommandInterface
{
    public function execute(): void;

    public function undo(): void;
}
