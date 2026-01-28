<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Cooking Oil 1L',
                'description' => 'Refined vegetable cooking oil, 1 liter bottle.',
                'price' => 320.00,
                'SKU' => 'COOKOIL-1L',
            ],
            [
                'name' => 'Maize Flour 2KG',
                'description' => 'Premium maize flour, 2kg pack.',
                'price' => 180.00,
                'SKU' => 'MAIZE-2KG',
            ],
            [
                'name' => 'Wheat Flour 2KG',
                'description' => 'All-purpose wheat flour, 2kg pack.',
                'price' => 210.00,
                'SKU' => 'WHEAT-2KG',
            ],
            [
                'name' => 'Rice 5KG',
                'description' => 'Long grain rice, 5kg bag.',
                'price' => 650.00,
                'SKU' => 'RICE-5KG',
            ],
            [
                'name' => 'Sugar 2KG',
                'description' => 'White granulated sugar, 2kg pack.',
                'price' => 240.00,
                'SKU' => 'SUGAR-2KG',
            ],
            [
                'name' => 'Salt 1KG',
                'description' => 'Iodized table salt, 1kg pack.',
                'price' => 70.00,
                'SKU' => 'SALT-1KG',
            ],
            [
                'name' => 'Tea Leaves 500G',
                'description' => 'Premium black tea leaves, 500g pack.',
                'price' => 220.00,
                'SKU' => 'TEA-500G',
            ],
            [
                'name' => 'Coffee 200G',
                'description' => 'Ground coffee, 200g pack.',
                'price' => 260.00,
                'SKU' => 'COFFEE-200G',
            ],
            [
                'name' => 'Milk Powder 400G',
                'description' => 'Instant milk powder, 400g pack.',
                'price' => 380.00,
                'SKU' => 'MILK-400G',
            ],
            [
                'name' => 'Cereal 500G',
                'description' => 'Breakfast cereal, 500g pack.',
                'price' => 300.00,
                'SKU' => 'CEREAL-500G',
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['SKU' => $product['SKU']],
                $product
            );
        }

        $this->command->info('Products seeded successfully.');
    }
}
