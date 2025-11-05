<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create categories
        $men = Category::create(['name' => 'Men', 'description' => 'A hight quality clothes for mens']);
        $women = Category::create(['name' => 'Women', 'description' => 'A hight quality clothes for womens']);
        $kids = Category::create(['name' => 'Kids', 'description' => 'A hight quality clothes for kids']);

        // Create products for each category
        $products = [
            [
                'name' => 'Classic White T-Shirt',
                'brand' => 'Nike',
                'price' => 29.99,
                'description' => 'A comfortable and durable cotton T-shirt for everyday wear.',
                'image' => 'tshirt_white.jpg',
                'category_id' => $men->id,
            ],
            [
                'name' => 'Slim Fit Jeans',
                'brand' => 'Levi’s',
                'price' => 59.99,
                'description' => 'Slim fit blue jeans with a stylish modern cut.',
                'image' => 'jeans_slimfit.jpg',
                'category_id' => $women->id,
            ],
            [
                'name' => 'Summer Floral Dress',
                'brand' => 'Zara',
                'price' => 79.99,
                'description' => 'Light floral dress perfect for summer outings.',
                'image' => 'floral_dress.jpg',
                'category_id' => $men->id,
            ],
            [
                'name' => 'Kids Hoodie',
                'brand' => 'Adidas',
                'price' => 39.99,
                'description' => 'Soft cotton hoodie with the Adidas logo for children.',
                'image' => 'kids_hoodie.jpg',
                'category_id' => $kids->id,
            ],
        ];

        foreach ($products as $data) {
            Product::create($data);
        }
    }
}

