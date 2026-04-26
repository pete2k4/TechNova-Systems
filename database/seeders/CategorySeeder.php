<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Software & Licenses',
                'slug' => 'software-licenses',
                'description' => 'Digital software licenses and operating systems',
                'type' => 'digital',
            ],
            [
                'name' => 'E-Books & Courses',
                'slug' => 'ebooks-courses',
                'description' => 'Digital educational content and e-books',
                'type' => 'digital',
            ],
            [
                'name' => 'Computer Hardware',
                'slug' => 'computer-hardware',
                'description' => 'Physical computer components and peripherals',
                'type' => 'physical',
            ],
            [
                'name' => 'Networking Equipment',
                'slug' => 'networking-equipment',
                'description' => 'Physical networking devices and cables',
                'type' => 'physical',
            ],
            [
                'name' => 'Mixed Tech Products',
                'slug' => 'mixed-tech',
                'description' => 'Both digital and physical technology products',
                'type' => 'mixed',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
