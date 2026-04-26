<?php

declare(strict_types=1);

namespace Tests\Unit\Cart;

use App\Domain\Cart\CartBundleComposite;
use PHPUnit\Framework\TestCase;

class CartCompositeIteratorTest extends TestCase
{
    public function test_composite_calculates_total_and_quantity_for_nested_cart(): void
    {
        $cart = [
            [
                'product_id' => 101,
                'name' => 'Mouse',
                'price' => 50,
                'quantity' => 2,
                'type' => 'physical',
            ],
            [
                'name' => 'Security Bundle',
                'children' => [
                    [
                        'product_id' => 201,
                        'name' => 'Antivirus',
                        'price' => 40,
                        'quantity' => 1,
                        'type' => 'digital',
                    ],
                    [
                        'product_id' => 202,
                        'name' => 'VPN',
                        'price' => 30,
                        'quantity' => 3,
                        'type' => 'digital',
                    ],
                ],
            ],
        ];

        $root = CartBundleComposite::fromSessionCart($cart, 'Root cart');

        $this->assertSame(270.0, $root->getTotal());
        $this->assertSame(6, $root->getQuantity());
        $this->assertTrue($root->hasPhysicalProducts());
    }

    public function test_iterator_flattens_nested_components_in_stable_order(): void
    {
        $cart = [
            [
                'product_id' => 1,
                'name' => 'Keyboard',
                'price' => 100,
                'quantity' => 1,
                'type' => 'physical',
            ],
            [
                'name' => 'Software Pack',
                'children' => [
                    [
                        'product_id' => 2,
                        'name' => 'OS License',
                        'price' => 120,
                        'quantity' => 1,
                        'type' => 'digital',
                    ],
                    [
                        'product_id' => 3,
                        'name' => 'Office License',
                        'price' => 80,
                        'quantity' => 1,
                        'type' => 'digital',
                    ],
                ],
            ],
        ];

        $root = CartBundleComposite::fromSessionCart($cart, 'Root cart');

        $productIds = [];
        foreach ($root as $leaf) {
            $productIds[] = $leaf->getProductId();
        }

        $this->assertSame([1, 2, 3], $productIds);
        $this->assertCount(3, $root->toCartPayload());
    }
}
