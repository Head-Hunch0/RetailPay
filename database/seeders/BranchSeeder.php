<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Nairobi Branch',
                'location' => 'Moi Avenue, Nairobi',
                'manager_email' => 'nairobimanager@kkwholesalers.com'
            ],
            [
                'name' => 'Mombasa Branch',
                'location' => 'Mombasa Road, Mombasa',
                'manager_email' => 'mombasamanager@kkwholesalers.com'
            ],
        ];

        foreach ($branches as $branch) {
            $manager = User::where('email', $branch['manager_email'])->first();

            Branch::create([
                'name' => $branch['name'],
                'location' => $branch['location'],
                'managerID' => $manager->id
            ]);
        }

        $this->command->info('Branches seeded successfully.');
    }
}
