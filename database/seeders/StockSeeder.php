<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Store;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = Store::all(['id']);
        $products = Product::all(['id']);

        foreach ($stores as $store) {
            foreach ($products as $product) {
                $minimum = random_int(5, 20);
                $quantity = random_int($minimum + 5, $minimum + 100);

                Stock::updateOrCreate(
                    [
                        'productID' => $product->id,
                        'storeID' => $store->id,
                        'quantity' => $quantity,
                        'minimum' => $minimum,
                    ]
                );
            }
        }

        $this->command->info('Stock seeded successfully.');
    }
}
