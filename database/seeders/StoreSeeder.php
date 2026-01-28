<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = [
            [
                'name' => 'Nairobi Store',
                'location' => 'CBD, Nairobi',
                'branch_name' => 'Nairobi Branch',
                'manager_email' => 'nairobistore@kkwholesalers.com',
            ],
            [
                'name' => 'Mombasa Store 1',
                'location' => 'Nyali, Mombasa',
                'branch_name' => 'Mombasa Branch',
                'manager_email' => 'mombasastore1@kkwholesalers.com',
            ],
            [
                'name' => 'Mombasa Store 2',
                'location' => 'Likoni, Mombasa',
                'branch_name' => 'Mombasa Branch',
                'manager_email' => 'mombasastore2@kkwholesalers.com',
            ],
        ];

        foreach ($stores as $store) {
            $branch = Branch::where('name', $store['branch_name'])->first();
            $manager = User::where('email', $store['manager_email'])->first();


            Store::create([
                'name' => $store['name'],
                'location' => $store['location'],
                'branchID' => $branch->id,
                'managerID' => $manager->id,
            ]);
        }

        $this->command->info('Stores seeded successfully.');
    }
}
