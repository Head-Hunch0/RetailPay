<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        // Create users with roles
        $users = [
            [
                'name' => 'Administrator',
                'email' => 'admin@kkwholesalers.com',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ],
            [
                'name' => 'Nairobi Branch Manager',
                'email' => 'nairobimanager@kkwholesalers.com',
                'password' => bcrypt('password'),
                'role' => 'branchmanager'
            ],
            [
                'name' => 'Mombasa Branch Manager',
                'email' => 'mombasamanager@kkwholesalers.com',
                'password' => bcrypt('password'),
                'role' => 'branchmanager'
            ],
            [
                'name' => 'Nairobi Store Manager',
                'email' => 'nairobistore@kkwholesalers.com',
                'password' => bcrypt('password'),
                'role' => 'storemanager'
            ],
            [
                'name' => 'Mombasa Store 1 Manager',
                'email' => 'mombasastore1@kkwholesalers.com',
                'password' => bcrypt('password'),
                'role' => 'storemanager'
            ],
            [
                'name' => 'Mombasa Store 2 Manager',
                'email' => 'mombasastore2@kkwholesalers.com',
                'password' => bcrypt('password'),
                'role' => 'storemanager'
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }

    }
}
