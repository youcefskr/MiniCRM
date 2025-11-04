<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create(['name' => 'T-Shirts', 'description' => 'Casual and printed T-shirts']);
        Category::create(['name' => 'Jeans', 'description' => 'Various jeans styles']);
        Category::create(['name' => 'Shoes', 'description' => 'All types of shoes']);
    }
}
