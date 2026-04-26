<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $softwareCategory = Category::where('slug', 'software-licenses')->first();
        $hardwareCategory = Category::where('slug', 'computer-hardware')->first();
        $ebookCategory = Category::where('slug', 'ebooks-courses')->first();
        $networkingCategory = Category::where('slug', 'networking-equipment')->first();

        $products = [
            // Digital Products
            [
                'category_id' => $softwareCategory->id,
                'name' => 'Windows 11 Pro License',
                'slug' => 'windows-11-pro',
                'description' => 'Official Windows 11 Pro single-user license with activation support and 1 year technical support',
                'price' => 199.99,
                'type' => 'digital',
                'sku' => 'WIN11PRO-001',
                'stock' => null,
            ],
            [
                'category_id' => $softwareCategory->id,
                'name' => 'Microsoft Office 365 Annual',
                'slug' => 'office-365-annual',
                'description' => 'Annual subscription to Microsoft Office 365 with cloud storage and updates',
                'price' => 99.99,
                'type' => 'digital',
                'sku' => 'OFFICE365-001',
                'stock' => null,
            ],
            [
                'category_id' => $ebookCategory->id,
                'name' => 'Advanced PHP Development Course',
                'slug' => 'php-dev-course',
                'description' => 'Comprehensive online course covering advanced PHP, design patterns, and best practices',
                'price' => 79.99,
                'type' => 'digital',
                'sku' => 'COURSE-PHP-001',
                'stock' => null,
            ],
            [
                'category_id' => $ebookCategory->id,
                'name' => 'Clean Code E-Book Bundle',
                'slug' => 'clean-code-bundle',
                'description' => '5 essential e-books on code quality, design patterns, and software architecture',
                'price' => 49.99,
                'type' => 'digital',
                'sku' => 'EBOOK-BUNDLE-001',
                'stock' => null,
            ],

            // Physical Products
            [
                'category_id' => $hardwareCategory->id,
                'name' => 'NVIDIA RTX 4090 Graphics Card',
                'slug' => 'nvidia-rtx-4090',
                'description' => '24GB GDDR6X memory, PCIe 4.0, suitable for gaming and professional workloads',
                'price' => 1599.99,
                'type' => 'physical',
                'sku' => 'GPU-RTX4090-001',
                'stock' => 5,
            ],
            [
                'category_id' => $hardwareCategory->id,
                'name' => 'Intel Core i9-13900K Processor',
                'slug' => 'intel-core-i9-13900k',
                'description' => '24-core processor with 8P+16E cores, 32 threads, 5.8GHz boost clock',
                'price' => 589.99,
                'type' => 'physical',
                'sku' => 'CPU-I9-13900K-001',
                'stock' => 8,
            ],
            [
                'category_id' => $hardwareCategory->id,
                'name' => 'Samsung 990 Pro NVMe SSD 4TB',
                'slug' => 'samsung-990-pro-4tb',
                'description' => 'PCIe 4.0 NVMe SSD with up to 7,100 MB/s read speed, includes heatsink',
                'price' => 449.99,
                'type' => 'physical',
                'sku' => 'SSD-990PRO-4TB-001',
                'stock' => 12,
            ],
            [
                'category_id' => $networkingCategory->id,
                'name' => 'Netgear Nighthawk Pro WiFi 6 Router',
                'slug' => 'netgear-nighthawk-wifi6',
                'description' => 'WiFi 6 (802.11ax) router with 32 stream technology and 12-stream AXE7800',
                'price' => 299.99,
                'type' => 'physical',
                'sku' => 'ROUTER-NIGHTHAWK-001',
                'stock' => 6,
            ],
            [
                'category_id' => $networkingCategory->id,
                'name' => 'Cat8 Ethernet Cable 100ft',
                'slug' => 'cat8-ethernet-100ft',
                'description' => 'Heavy-duty Cat8 ethernet cable certified for 40Gbps speeds, with shielding',
                'price' => 29.99,
                'type' => 'physical',
                'sku' => 'CABLE-CAT8-100FT-001',
                'stock' => 25,
            ],
            [
                'category_id' => $hardwareCategory->id,
                'name' => 'Corsair Dominator DDR5 32GB (2x16GB)',
                'slug' => 'corsair-dominator-ddr5-32gb',
                'description' => 'DDR5 high-performance RAM with XMP 3.0 profiles, 6000MHz',
                'price' => 179.99,
                'type' => 'physical',
                'sku' => 'RAM-DDR5-32GB-001',
                'stock' => 15,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['sku' => $product['sku']], $product);
        }
    }
}
