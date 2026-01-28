<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_all_protected_routes(): void
    {
        $loginUrl = route('login');
        $routes = [
            route('dashboard'),
            route('branches.index'),
            route('stores.index'),
            route('products.index'),
            route('stock.index'),
            route('sales.index'),
            route('transfers.index'),
        ];

        foreach ($routes as $url) {
            $this->get($url)->assertRedirect($loginUrl);
        }

        $this->post(route('products.store'))->assertRedirect($loginUrl);
        $this->put(route('products.update', 1))->assertRedirect($loginUrl);
        $this->delete(route('products.destroy', 1))->assertRedirect($loginUrl);
        $this->post(route('transfers.store'))->assertRedirect($loginUrl);
        $this->patch(route('transfers.update', 1))->assertRedirect($loginUrl);
    }

    public function test_authenticated_user_can_access_all_get_routes(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $routes = [
            route('dashboard'),
            route('branches.index'),
            route('stores.index'),
            route('products.index'),
            route('stock.index'),
            route('sales.index'),
            route('transfers.index'),
        ];

        foreach ($routes as $url) {
            $this->actingAs($user)->get($url)->assertStatus(200);
        }
    }
}
