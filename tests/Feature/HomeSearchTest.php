<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_search_filters_products_by_query(): void
    {
        Product::query()->create([
            'name' => 'Starlink Standard Router Kit',
            'slug' => 'starlink-standard-router-kit',
            'price' => 49999,
            'stock' => 10,
            'is_active' => true,
        ]);

        Product::query()->create([
            'name' => 'Starlink Ethernet Adapter',
            'slug' => 'starlink-ethernet-adapter',
            'price' => 6999,
            'stock' => 10,
            'is_active' => true,
        ]);

        Product::query()->create([
            'name' => 'Hidden Router Accessory',
            'slug' => 'hidden-router-accessory',
            'price' => 9999,
            'stock' => 10,
            'is_active' => false,
        ]);

        $response = $this->get('/?q=router');

        $response->assertOk();
        $response->assertSeeText('Starlink Standard Router Kit');
        $response->assertDontSeeText('Starlink Ethernet Adapter');
        $response->assertDontSeeText('Hidden Router Accessory');
        $response->assertSee('Search results for "router".', false);
    }
}
