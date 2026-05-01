<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        if (! $user) {
            $user = User::factory()->create();
        }

        $skus = [
            'GPU-RTX4090-001',
            'OFFICE365-001',
            'COURSE-PHP-001',
        ];

        $products = Product::whereIn('sku', $skus)->get();

        if ($products->isEmpty()) {
            return;
        }

        $subtotal = $products->sum(fn ($p) => (float) $p->price);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'subtotal' => $subtotal,
            'discount' => 0.00,
            'total' => $subtotal,
            // leave `status` unset so DB default enum applies
            'payment_method' => 'credit_card',
            'payment_credential' => null,
        ]);

        foreach ($products as $product) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => $product->price,
            ]);
        }
    }
}
