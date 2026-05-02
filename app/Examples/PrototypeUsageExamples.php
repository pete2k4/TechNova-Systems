<?php

declare(strict_types=1);

namespace App\Examples;

use App\Factories\ProductFactory;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

/**
 * Prototype Pattern Usage Examples
 * 
 * This file demonstrates real-world use cases for the Prototype pattern
 * in the TechNova Marketplace application.
 */
class PrototypeUsageExamples
{
    /**
     * Example 1: Clone a product to create a variant
     * 
     * Use case: Creating a "Premium" version of an existing product
     * without reloading the entire product from the database.
     */
    public static function exampleCloneProductVariant(Product $baseProduct): Product
    {
        // Clone the base product (gets all attributes without DB access)
        $premiumVariant = ProductFactory::cloneProduct($baseProduct);
        
        // Customize the variant
        $premiumVariant->name = $baseProduct->name . ' - Premium Edition';
        $premiumVariant->price = (float) $baseProduct->price * 1.5;
        $premiumVariant->sku = $baseProduct->sku . '-PREMIUM';
        
        // Save as a new product
        $premiumVariant->save();
        
        return $premiumVariant;
    }

    /**
     * Example 2: Register and clone product templates
     * 
     * Use case: E-commerce team registers base product templates
     * and quickly clones them to create new products for catalog expansion.
     */
    public static function exampleProductTemplate(): void
    {
        // Step 1: Create a base template for digital products
        $digitalTemplate = new Product([
            'name' => 'Digital Product Template',
            'type' => 'digital',
            'price' => 29.99,
            'sku' => 'DIGITAL-TEMPLATE',
            'is_active' => true,
        ]);
        
        // Step 2: Register it in the factory
        ProductFactory::registerPrototype('digital-template', $digitalTemplate);
        
        // Step 3: Later, quickly create new digital products by cloning
        $phpCourse = ProductFactory::cloneFromPrototype('digital-template');
        $phpCourse->name = 'Advanced PHP Course';
        $phpCourse->sku = 'PHP-COURSE-ADV';
        $phpCourse->save();
        
        $pythonCourse = ProductFactory::cloneFromPrototype('digital-template');
        $pythonCourse->name = 'Python Mastery Course';
        $pythonCourse->sku = 'PYTHON-MASTERY';
        $pythonCourse->save();
    }

    /**
     * Example 3: Duplicate an order for testing or bulk operations
     * 
     * Use case: Testing order processing without creating multiple
     * user actions, or creating order templates for bulk operations.
     */
    public static function exampleCloneOrder(Order $order): Order
    {
        // Clone the entire order (status resets to CHECKOUT_STARTED)
        $clonedOrder = $order->clone();
        
        // The cloned order is now ready to be modified and saved
        // without affecting the original order
        $clonedOrder->user_id = $order->user_id;
        $clonedOrder->save();
        
        // Clone all order items
        foreach ($order->items as $item) {
            $clonedItem = $item->clone();
            $clonedItem->order_id = $clonedOrder->id;
            $clonedItem->save();
        }
        
        return $clonedOrder;
    }

    /**
     * Example 4: Clone order items when building orders from templates
     * 
     * Use case: When a customer creates bulk orders with the same items,
     * clone the items instead of recreating them from scratch.
     */
    public static function exampleCloneOrderItem(OrderItem $templateItem, Order $newOrder): OrderItem
    {
        // Clone the template item
        $newItem = $templateItem->clone();
        
        // Associate with the new order
        $newItem->order_id = $newOrder->id;
        $newItem->save();
        
        return $newItem;
    }

    /**
     * Example 5: Batch product creation from templates
     * 
     * Use case: Marketing team wants to quickly create promotional
     * variants of 100+ products without manual entry.
     */
    public static function exampleBatchCloneProducts(int $count = 10): void
    {
        // Assume a base template is already registered
        if (!ProductFactory::hasPrototype('base-product')) {
            return; // Template not registered
        }
        
        for ($i = 1; $i <= $count; $i++) {
            // Clone the template
            $promotional = ProductFactory::cloneFromPrototype('base-product');
            
            // Customize for batch
            $promotional->name = $promotional->name . " - Batch #{$i}";
            $promotional->price = (float) $promotional->price * 0.9; // 10% discount
            $promotional->sku = $promotional->sku . "-BATCH-{$i}";
            
            // Save immediately
            $promotional->save();
        }
    }

    /**
     * Example 6: Advanced template management
     * 
     * Use case: Manage multiple product templates for different categories.
     */
    public static function exampleAdvancedTemplateManagement(): void
    {
        // Register templates for different categories
        $templates = [
            'software-base' => new Product(['type' => 'digital', 'price' => 49.99]),
            'hardware-base' => new Product(['type' => 'physical', 'price' => 99.99]),
            'subscription-base' => new Product(['type' => 'digital', 'price' => 9.99]),
        ];
        
        foreach ($templates as $key => $template) {
            ProductFactory::registerPrototype($key, $template);
        }
        
        // Check if a template exists before cloning
        if (ProductFactory::hasPrototype('software-base')) {
            $softwareProduct = ProductFactory::cloneFromPrototype('software-base');
            // ... customize and save
        }
        
        // List all registered templates
        // $registry = ProductFactory::getRegistryInstance();
        // $allKeys = $registry->keys(); // ['software-base', 'hardware-base', 'subscription-base']
    }
}
