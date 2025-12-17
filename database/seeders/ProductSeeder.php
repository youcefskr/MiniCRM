<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Créer des catégories
        $categories = [
            [
                'name' => 'Électronique',
                'description' => 'Appareils et équipements électroniques'
            ],
            [
                'name' => 'Informatique',
                'description' => 'Ordinateurs, accessoires et logiciels'
            ],
            [
                'name' => 'Services',
                'description' => 'Services professionnels et consulting'
            ],
            [
                'name' => 'Abonnements',
                'description' => 'Licences et abonnements logiciels'
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = Category::firstOrCreate(
                ['name' => $categoryData['name']],
                ['description' => $categoryData['description']]
            );

            // Créer des produits pour chaque catégorie
            $this->createProductsForCategory($category);
        }
    }

    private function createProductsForCategory(Category $category)
    {
        $products = [];

        switch ($category->name) {
            case 'Électronique':
                $products = [
                    [
                        'name' => 'iPhone 15 Pro',
                        'code' => 'IPHONE-15-PRO',
                        'brand' => 'Apple',
                        'price' => 1229.00,
                        'stock_quantity' => 25,
                        'type' => 'product',
                        'description' => 'Smartphone haut de gamme avec puce A17 Pro',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Samsung Galaxy S24',
                        'code' => 'GALAXY-S24',
                        'brand' => 'Samsung',
                        'price' => 899.00,
                        'stock_quantity' => 30,
                        'type' => 'product',
                        'description' => 'Flagship Android avec IA intégrée',
                        'is_active' => true,
                    ],
                ];
                break;

            case 'Informatique':
                $products = [
                    [
                        'name' => 'MacBook Pro 16"',
                        'code' => 'MBP-16-M3',
                        'brand' => 'Apple',
                        'price' => 2799.00,
                        'stock_quantity' => 15,
                        'type' => 'product',
                        'description' => 'Ordinateur portable professionnel avec puce M3 Max',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Dell XPS 15',
                        'code' => 'DELL-XPS-15',
                        'brand' => 'Dell',
                        'price' => 1899.00,
                        'stock_quantity' => 20,
                        'type' => 'product',
                        'description' => 'Ultrabook performant pour professionnels',
                        'is_active' => true,
                    ],
                ];
                break;

            case 'Services':
                $products = [
                    [
                        'name' => 'Consulting IT - Heure',
                        'code' => 'CONSULT-IT-H',
                        'brand' => 'Notre Entreprise',
                        'price' => 150.00,
                        'stock_quantity' => 0,
                        'type' => 'service',
                        'description' => 'Prestation de consulting informatique par heure',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Formation Laravel',
                        'code' => 'FORM-LARAVEL',
                        'brand' => 'Notre Entreprise',
                        'price' => 1500.00,
                        'stock_quantity' => 0,
                        'type' => 'service',
                        'description' => 'Formation complète Laravel (3 jours)',
                        'is_active' => true,
                    ],
                ];
                break;

            case 'Abonnements':
                $products = [
                    [
                        'name' => 'Microsoft 365 Business',
                        'code' => 'M365-BUS',
                        'brand' => 'Microsoft',
                        'price' => 12.50,
                        'stock_quantity' => 0,
                        'type' => 'subscription',
                        'description' => 'Licence mensuelle Microsoft 365 par utilisateur',
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Adobe Creative Cloud',
                        'code' => 'ADOBE-CC',
                        'brand' => 'Adobe',
                        'price' => 59.99,
                        'stock_quantity' => 0,
                        'type' => 'subscription',
                        'description' => 'Abonnement mensuel Creative Cloud complet',
                        'is_active' => true,
                    ],
                ];
                break;
        }

        foreach ($products as $productData) {
            $productData['category_id'] = $category->id;
            Product::firstOrCreate(
                ['code' => $productData['code']],
                $productData
            );
        }
    }
}
