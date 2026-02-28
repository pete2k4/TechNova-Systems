<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\ProductInterface;
use App\Factories\ProductFactory;
use App\Models\Product;

class ProductRepository
{
    /**
     * @param Product $product
     * @return bool
     */
    public function save(Product $product): bool
    {
        // Database save logic would go here
        // return $product->save();
        return true;
    }

    /**
     * @param int $id
     * @return ProductInterface|null
     */
    public function findById(int $id): ?ProductInterface
    {
        // In a real implementation, you'd query the database:
        // $data = Product::find($id)?->toArray();
        // if (!$data) return null;
        // return ProductFactory::fromArray($data);
        
        return null;
    }

    /**
     * @return ProductInterface[]
     */
    public function all(): array
    {
        // In a real implementation:
        // $products = Product::all();
        // return $products->map(fn($p) => ProductFactory::fromArray($p->toArray()))->toArray();
        
        return [];
    }

    /**
     * @param string $type
     * @return ProductInterface[]
     */
    public function findByType(string $type): array
    {
        // Validate that this is a known product type
        ProductFactory::create($type);
        
        // In a real implementation:
        // $products = Product::where('type', $type)->get();
        // return $products->map(fn($p) => ProductFactory::fromArray($p->toArray()))->toArray();
        
        return [];
    }
}
