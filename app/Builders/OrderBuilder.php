<?php

declare(strict_types=1);

namespace App\Builders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;

/**
 * OrderBuilder - Builder Pattern Implementation
 * 
 * Constructs complex Order objects step by step with a fluent interface.
 * Handles order item addition, price calculations, and validation.
 * 
 */
class OrderBuilder
{
    private ?int $userId = null;
    private array $items = [];
    private float $subtotal = 0.0;
    private float $discount = 0.0;
    private float $total = 0.0;
    private string $status = 'pending';
    private ?string $paymentMethod = null;
    private ?string $paymentCredential = null;
    private ?string $orderNumber = null;

    /**
     * Set the user for this order.
     *
     * @param User|int $user User model or user ID
     * @return self
     */
    public function forUser(User|int $user): self
    {
        $this->userId = $user instanceof User ? $user->id : $user;
        return $this;
    }

    /**
     * Add an item to the order.
     *
     * @param int $productId
     * @param int $quantity
     * @param float $price Price per unit
     * @return self
     */
    public function addItem(int $productId, int $quantity, float $price): self
    {
        $this->items[] = [
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $price,
        ];

        $this->subtotal += ($price * $quantity);
        $this->recalculateTotal();

        return $this;
    }

    /**
     * Add multiple items at once.
     *
     * @param array $items Array of items with product_id, quantity, and price
     * @return self
     */
    public function addItems(array $items): self
    {
        foreach ($items as $item) {
            $this->addItem(
                $item['product_id'],
                $item['quantity'],
                $item['price']
            );
        }

        return $this;
    }

    /**
     * Set the discount amount.
     *
     * @param float $discount
     * @return self
     */
    public function withDiscount(float $discount): self
    {
        $this->discount = max(0, $discount);
        $this->recalculateTotal();
        return $this;
    }

    /**
     * Set the payment method and credential.
     *
     * @param string $method
     * @param string|null $credential
     * @return self
     */
    public function withPaymentMethod(string $method, ?string $credential = null): self
    {
        $this->paymentMethod = $method;
        $this->paymentCredential = $credential;
        return $this;
    }

    /**
     * Set the order status.
     *
     * @param string $status
     * @return self
     */
    public function withStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Set a custom order number.
     *
     * @param string $orderNumber
     * @return self
     */
    public function withOrderNumber(string $orderNumber): self
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    /**
     * Recalculate the total based on subtotal and discount.
     *
     * @return void
     */
    private function recalculateTotal(): void
    {
        $this->total = max(0, $this->subtotal - $this->discount);
    }

    /**
     * Generate a unique order number.
     *
     * @return string
     */
    private function generateOrderNumber(): string
    {
        return 'ORD-' . strtoupper(uniqid());
    }

    /**
     * Validate the order before building.
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    private function validate(): void
    {
        if ($this->userId === null) {
            throw new \InvalidArgumentException('Order must have a user. Call forUser() before building.');
        }

        if (empty($this->items)) {
            throw new \InvalidArgumentException('Order must have at least one item. Call addItem() before building.');
        }

        if ($this->paymentMethod === null) {
            throw new \InvalidArgumentException('Order must have a payment method. Call withPaymentMethod() before building.');
        }
    }

    /**
     * Build and return the Order model.
     * Note: Items are not attached. Use buildAndSave() to persist with items.
     *
     * @return Order
     * @throws \InvalidArgumentException
     */
    public function build(): Order
    {
        $this->validate();

        if ($this->orderNumber === null) {
            $this->orderNumber = $this->generateOrderNumber();
        }

        $order = new Order([
            'user_id' => $this->userId,
            'order_number' => $this->orderNumber,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'total' => $this->total,
            'status' => $this->status,
            'payment_method' => $this->paymentMethod,
            'payment_credential' => $this->paymentCredential,
        ]);

        return $order;
    }

    /**
     * Get the pending items that will be attached to the order.
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Build and save the order with its items to the database.
     *
     * @return Order
     * @throws \InvalidArgumentException
     */
    public function buildAndSave(): Order
    {
        $order = $this->build();
        $order->save();

        // Create order items
        foreach ($this->items as $itemData) {
            $orderItem = new OrderItem([
                'order_id' => $order->id,
                'product_id' => $itemData['product_id'],
                'quantity' => $itemData['quantity'],
                'price' => $itemData['price'],
            ]);
            $orderItem->save();
        }

        // Refresh the order to load the items relationship
        $order->load('items');

        return $order;
    }

    /**
     * Reset the builder to build a new order.
     *
     * @return self
     */
    public function reset(): self
    {
        $this->userId = null;
        $this->items = [];
        $this->subtotal = 0.0;
        $this->discount = 0.0;
        $this->total = 0.0;
        $this->status = 'pending';
        $this->paymentMethod = null;
        $this->paymentCredential = null;
        $this->orderNumber = null;

        return $this;
    }

    /**
     * Get a summary of the current order being built.
     *
     * @return array
     */
    public function getSummary(): array
    {
        return [
            'user_id' => $this->userId,
            'items_count' => count($this->items),
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'total' => $this->total,
            'status' => $this->status,
            'payment_method' => $this->paymentMethod,
        ];
    }
}
