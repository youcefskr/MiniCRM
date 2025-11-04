<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $tshirts = Category::where('name', 'T-Shirts')->first();

        Product::create([
            'name' => 'Classic White Tee',
            'brand' => 'Nike',
            'price' => 19.99,
            'description' => 'Soft cotton T-shirt with round neck.',
            'category_id' => $tshirts->id,
        ]);

        Product::create([
            'name' => 'Logo Print Tee',
            'brand' => 'Adidas',
            'price' => 24.99,
            'description' => 'Graphic print T-shirt with slim fit.',
            'category_id' => $tshirts->id,
        ]);
    }
}
