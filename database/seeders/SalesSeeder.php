<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\Store;
use Illuminate\Database\Seeder;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = Store::all(['id']);
        $products = Product::all(['id', 'price']);

        foreach ($stores as $store) {
            $salesCount = random_int(5, 15);

            for ($i = 0; $i < $salesCount; $i++) {
                $product = $products->random();
                $stock = Stock::where('productID', $product->id)
                    ->where('storeID', $store->id)
                    ->first();

                if (!$stock || $stock->quantity <= 0) {
                    continue;
                }

                $quantitySold = random_int(1, 10);
                $quantitySold = min($quantitySold, $stock->quantity);
                $totalPrice = $product->price * $quantitySold;

                Sale::create([
                    'productID' => $product->id,
                    'storeID' => $store->id,
                    'quantitySold' => $quantitySold,
                    'totalPrice' => $totalPrice,
                ]);

                $stock->decrement('quantity', $quantitySold);
            }
        }

        $this->command->info('Sales seeded successfully.');
    }
}
