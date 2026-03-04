<?php

declare(strict_types=1);

namespace App\Prototypes;

use App\Contracts\ProductInterface;
use App\Contracts\PrototypeInterface;
use InvalidArgumentException;

class ProductPrototypeRegistry
{
    /**
     * @var array<string, ProductInterface&PrototypeInterface>
     */
    private array $prototypes = [];

    public function register(string $type, ProductInterface&PrototypeInterface $prototype): void
    {
        $this->prototypes[strtolower($type)] = $prototype;
    }

    public function has(string $type): bool
    {
        return array_key_exists(strtolower($type), $this->prototypes);
    }

    public function getClone(string $type): ProductInterface
    {
        $normalizedType = strtolower($type);

        if (!$this->has($normalizedType)) {
            throw new InvalidArgumentException("Unknown product prototype: {$type}");
        }

        return $this->prototypes[$normalizedType]->clonePrototype();
    }
}
