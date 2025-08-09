<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Fresh Orange Juice',
                'description' => 'Pure and natural orange juice extracted from fresh oranges',
                'price' => 12.99,
                'stock_quantity' => 50,
                'category' => 'Juice',
                'status' => true,
                'success_mail' => 'orders@fruitstore.com',
                'attachment' => 'documents/orange-juice-recipe.pdf'
            ],
            [
                'name' => 'Organic Bananas',
                'description' => 'Fresh organic bananas rich in potassium and vitamins',
                'price' => 5.49,
                'stock_quantity' => 100,
                'category' => 'Fruit',
                'status' => true,
                'success_mail' => 'organic@fruitstore.com',
                'attachment' => 'documents/banana-health-benefits.pdf'
            ],
            [
                'name' => 'Apple Cider Vinegar',
                'description' => 'Natural apple cider vinegar with mother culture',
                'price' => 8.99,
                'stock_quantity' => 75,
                'category' => 'Drink',
                'status' => true
            ],
            [
                'name' => 'Fresh Cucumber',
                'description' => 'Crisp and fresh cucumbers perfect for salads',
                'price' => 3.99,
                'stock_quantity' => 80,
                'category' => 'Vegetable',
                'status' => true
            ],
            [
                'name' => 'Tomato Ketchup',
                'description' => 'Classic tomato ketchup made from ripe tomatoes',
                'price' => 4.50,
                'stock_quantity' => 120,
                'category' => 'Condiment',
                'status' => true
            ],
            [
                'name' => 'Grape Juice',
                'description' => 'Sweet and refreshing grape juice',
                'price' => 9.99,
                'stock_quantity' => 60,
                'category' => 'Juice',
                'status' => true,
                'success_mail' => 'grape@fruitstore.com'
            ],
            [
                'name' => 'Fresh Raspberries',
                'description' => 'Sweet and tangy fresh raspberries',
                'price' => 7.99,
                'stock_quantity' => 40,
                'category' => 'Fruit',
                'status' => true
            ],
            [
                'name' => 'Organic Milk',
                'description' => 'Pure organic milk from grass-fed cows',
                'price' => 6.99,
                'stock_quantity' => 90,
                'category' => 'Dairy',
                'status' => true
            ],
            [
                'name' => 'Fresh Tomatoes',
                'description' => 'Ripe and juicy fresh tomatoes',
                'price' => 4.99,
                'stock_quantity' => 70,
                'category' => 'Vegetable',
                'status' => true
            ],
            [
                'name' => 'Mango Smoothie',
                'description' => 'Creamy mango smoothie made with fresh mangoes',
                'price' => 11.99,
                'stock_quantity' => 35,
                'category' => 'Drink',
                'status' => true,
                'success_mail' => 'smoothies@fruitstore.com',
                'attachment' => 'documents/mango-smoothie-guide.pdf'
            ]
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}
