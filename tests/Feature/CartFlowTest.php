<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_add_to_cart_redirects_to_cart_page(): void
    {
        $product = Product::query()->create([
            'name' => 'Starlink Gen 3 Kit',
            'slug' => 'starlink-gen-3-kit',
            'price' => 50000,
            'stock' => 10,
            'quantity' => 10,
            'is_active' => true,
        ]);

        $response = $this->post(route('shop.cart.add', ['product' => $product]), [
            'quantity' => 2,
        ]);

        $response->assertRedirect(route('shop.cart.index'));
        $this->assertSame(2, session('cart')[(string) $product->id]['quantity']);
    }

    public function test_cart_page_renders_order_summary_and_checkout_modal(): void
    {
        $product = Product::query()->create([
            'name' => 'Starlink High Performance Kit',
            'slug' => 'starlink-high-performance-kit',
            'price' => 230000,
            'stock' => 10,
            'quantity' => 10,
            'is_active' => true,
        ]);

        $this->withSession([
            'cart' => [
                (string) $product->id => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => 230000,
                    'quantity' => 1,
                    'image_path' => null,
                ],
            ],
        ]);

        $response = $this->get(route('shop.cart.index'));

        $response->assertOk();
        $response->assertSeeText('Shopping Cart');
        $response->assertSeeText('Order Summary');
        $response->assertSeeText('Proceed to Checkout');
        $response->assertSeeText('Create an Account');
        $response->assertSeeText('Sign In Here');
        $response->assertSee('name="quantity" value="2"', false);
    }

    public function test_cart_quantity_can_be_updated(): void
    {
        $product = Product::query()->create([
            'name' => 'Starlink Gen 3 Kit',
            'slug' => 'starlink-gen-3-kit',
            'price' => 50000,
            'stock' => 10,
            'quantity' => 10,
            'is_active' => true,
        ]);

        $this->withSession([
            'cart' => [
                (string) $product->id => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => 50000,
                    'quantity' => 1,
                    'image_path' => null,
                ],
            ],
        ]);

        $response = $this->patch(route('shop.cart.update', ['product' => $product]), [
            'quantity' => 3,
        ]);

        $response->assertRedirect(route('shop.cart.index'));
        $this->assertSame(3, session('cart')[(string) $product->id]['quantity']);
    }
}
