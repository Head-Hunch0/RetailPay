<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\Store;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeedersTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_core_records(): void
    {
        $this->seed();

        $this->assertGreaterThan(0, User::count(), 'Users were not seeded.');
        $this->assertGreaterThan(0, Branch::count(), 'Branches were not seeded.');
        $this->assertGreaterThan(0, Store::count(), 'Stores were not seeded.');
        $this->assertGreaterThan(0, Product::count(), 'Products were not seeded.');
        $this->assertGreaterThan(0, Stock::count(), 'Stock was not seeded.');
        $this->assertGreaterThan(0, Sale::count(), 'Sales were not seeded.');
        $this->assertGreaterThan(0, Transfer::count(), 'Transfers were not seeded.');
    }

    public function test_seeded_users_have_expected_roles_and_logins(): void
    {
        $this->seed();

        $this->assertDatabaseHas('users', ['email' => 'admin@kkwholesalers.com', 'role' => 'admin']);
        $this->assertDatabaseHas('users', ['email' => 'nairobimanager@kkwholesalers.com', 'role' => 'branchmanager']);
        $this->assertDatabaseHas('users', ['email' => 'mombasamanager@kkwholesalers.com', 'role' => 'branchmanager']);
        $this->assertDatabaseHas('users', ['email' => 'nairobistore@kkwholesalers.com', 'role' => 'storemanager']);
        $this->assertDatabaseHas('users', ['email' => 'mombasastore1@kkwholesalers.com', 'role' => 'storemanager']);
        $this->assertDatabaseHas('users', ['email' => 'mombasastore2@kkwholesalers.com', 'role' => 'storemanager']);
    }

    public function test_seeded_transfers_reference_valid_entities(): void
    {
        $this->seed();

        $transfer = Transfer::with(['product', 'fromStore', 'toStore', 'requester'])->first();
        $this->assertNotNull($transfer, 'No transfer records found.');
        $this->assertNotNull($transfer->product, 'Transfer product not linked.');
        $this->assertNotNull($transfer->fromStore, 'Transfer fromStore not linked.');
        $this->assertNotNull($transfer->toStore, 'Transfer toStore not linked.');
        $this->assertNotNull($transfer->requester, 'Transfer requester not linked.');
    }
}
