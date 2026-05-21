<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerDashboardRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_login_defaults_to_customer_dashboard(): void
    {
        User::factory()->create([
            'email' => 'customer@example.com',
            'password' => 'password',
        ]);

        $response = $this->post(route('login.attempt'), [
            'email' => 'customer@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('account.dashboard'));
    }

    public function test_admin_login_defaults_to_admin_dashboard(): void
    {
        User::factory()->create([
            'email' => 'admin@demo.com',
            'password' => 'password',
        ]);

        $response = $this->post(route('login.attempt'), [
            'email' => 'admin@demo.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard.shortcut'));
    }

    public function test_customer_cannot_land_on_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'customer@example.com',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard.shortcut'));

        $response->assertRedirect(route('account.dashboard'));
    }
}
