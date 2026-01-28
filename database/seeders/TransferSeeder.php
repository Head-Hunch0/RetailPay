<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Store;
use App\Models\Transfer;
use Illuminate\Database\Seeder;

class TransferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = Store::all(['id', 'managerID']);
        $products = Product::all(['id']);

        if ($stores->count() < 2 || $products->isEmpty()) {
            return;
        }

        foreach ($stores as $fromStore) {
            $transfersCount = random_int(1, 4);

            for ($i = 0; $i < $transfersCount; $i++) {
                $toStore = $stores->where('id', '!=', $fromStore->id)->random();
                $product = $products->random();

                $fromStock = Stock::where('productID', $product->id)
                    ->where('storeID', $fromStore->id)
                    ->first();

                if (!$fromStock || $fromStock->quantity <= 0) {
                    continue;
                }

                $quantity = random_int(1, min(10, $fromStock->quantity));

                Transfer::create([
                    'productID' => $product->id,
                    'fromStoreID' => $fromStore->id,
                    'toStoreID' => $toStore->id,
                    'quantity' => $quantity,
                    'requestedBy' => $fromStore->managerID,
                    'approvedBy' => $toStore->managerID,
                    'status' => 'approved',
                ]);

                $fromStock->decrement('quantity', $quantity);

                $toStock = Stock::where('productID', $product->id)
                    ->where('storeID', $toStore->id)
                    ->first();

                if ($toStock) {
                    $toStock->increment('quantity', $quantity);
                } else {
                    Stock::create([
                        'productID' => $product->id,
                        'storeID' => $toStore->id,
                        'quantity' => $quantity,
                        'minimum' => 10,
                    ]);
                }
            }
        }

        $this->command->info('Transfers seeded successfully.');
    }
}
