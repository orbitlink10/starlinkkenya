<?php

namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use App\Models\Enquiry;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Product;
use App\Models\SitePage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AnalyticsDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_public_visits_searches_and_cart_actions_are_recorded(): void
    {
        Carbon::setTestNow('2026-05-01 09:30:00');

        $product = Product::query()->create([
            'name' => 'Starlink Standard Kit',
            'slug' => 'starlink-standard-kit',
            'price' => 49999,
            'stock' => 12,
            'is_active' => true,
        ]);

        SitePage::query()->create([
            'page_title' => 'Satellite Internet Guide',
            'slug' => 'satellite-internet-guide',
            'type' => 'Post',
        ]);

        $this->withHeader('referer', 'https://www.google.com/search?q=starlink')
            ->get('/?q=router')
            ->assertOk();

        $this->get('/product/starlink-standard-kit')->assertOk();
        $this->get('/cart')->assertOk();
        $this->get('/satellite-internet-guide')->assertOk();

        $this->post(route('shop.cart.add', ['product' => $product]), ['quantity' => 2])
            ->assertRedirect(route('shop.cart.index'));

        $productWhatsappResponse = $this->get(route('shop.product.whatsapp', ['product' => $product]));
        $productWhatsappResponse->assertStatus(302);
        $this->assertTrue(str_starts_with((string) $productWhatsappResponse->headers->get('Location'), 'https://wa.me/'));

        $cartWhatsappResponse = $this->get(route('shop.cart.whatsapp'));
        $cartWhatsappResponse->assertStatus(302);
        $this->assertTrue(str_starts_with((string) $cartWhatsappResponse->headers->get('Location'), 'https://wa.me/'));

        $this->assertSame(4, AnalyticsEvent::query()->where('event_type', 'page_view')->count());
        $this->assertSame(1, AnalyticsEvent::query()->where('event_type', 'search')->count());
        $this->assertSame(1, AnalyticsEvent::query()->where('event_type', 'add_to_cart')->count());
        $this->assertSame(1, AnalyticsEvent::query()->where('event_type', 'whatsapp_product_click')->count());
        $this->assertSame(1, AnalyticsEvent::query()->where('event_type', 'whatsapp_cart_click')->count());
        $this->assertSame(1, AnalyticsEvent::query()->distinct()->count('visitor_id'));

        $this->assertDatabaseHas('analytics_events', [
            'event_type' => 'page_view',
            'path' => '/',
            'label' => 'Homepage',
            'page_type' => 'home',
            'referrer_host' => 'www.google.com',
        ]);

        $this->assertDatabaseHas('analytics_events', [
            'event_type' => 'search',
            'path' => '/',
            'label' => 'router',
            'page_type' => 'home',
        ]);

        $this->assertDatabaseHas('analytics_events', [
            'event_type' => 'page_view',
            'path' => '/product/starlink-standard-kit',
            'label' => 'Starlink Standard Kit',
            'page_type' => 'product',
        ]);

        $this->assertDatabaseHas('analytics_events', [
            'event_type' => 'add_to_cart',
            'path' => '/product/starlink-standard-kit',
            'label' => 'Starlink Standard Kit',
            'page_type' => 'product',
        ]);

        $this->assertDatabaseHas('analytics_events', [
            'event_type' => 'whatsapp_product_click',
            'path' => '/product/starlink-standard-kit',
            'label' => 'Starlink Standard Kit',
            'page_type' => 'product',
        ]);

        $this->assertDatabaseHas('analytics_events', [
            'event_type' => 'whatsapp_cart_click',
            'path' => '/cart',
            'label' => 'Cart Checkout',
            'page_type' => 'cart',
        ]);
    }

    public function test_admin_can_view_analytics_dashboard(): void
    {
        Carbon::setTestNow('2026-05-01 15:00:00');

        $user = User::factory()->create();

        AnalyticsEvent::query()->create([
            'event_type' => 'page_view',
            'visitor_id' => 'visitor-a',
            'path' => '/',
            'label' => 'Homepage',
            'page_type' => 'home',
            'referrer_host' => 'www.google.com',
            'occurred_at' => now()->subDays(1),
        ]);

        AnalyticsEvent::query()->create([
            'event_type' => 'page_view',
            'visitor_id' => 'visitor-a',
            'path' => '/product/starlink-mini-kit',
            'label' => 'Starlink Mini Kit',
            'page_type' => 'product',
            'referrer_host' => 'www.google.com',
            'occurred_at' => now()->subHours(6),
        ]);

        AnalyticsEvent::query()->create([
            'event_type' => 'page_view',
            'visitor_id' => 'visitor-b',
            'path' => '/product/starlink-mini-kit',
            'label' => 'Starlink Mini Kit',
            'page_type' => 'product',
            'referrer_host' => 'chatgpt.com',
            'occurred_at' => now()->subDays(2),
        ]);

        AnalyticsEvent::query()->create([
            'event_type' => 'page_view',
            'visitor_id' => 'visitor-c',
            'path' => '/installation-guide',
            'label' => 'Installation Guide',
            'page_type' => 'page',
            'occurred_at' => now()->subDays(3),
        ]);

        AnalyticsEvent::query()->create([
            'event_type' => 'page_view',
            'visitor_id' => 'visitor-legacy',
            'path' => '/legacy-page',
            'label' => 'Old Landing Page',
            'page_type' => 'page',
            'occurred_at' => now()->subDays(45),
        ]);

        AnalyticsEvent::query()->create([
            'event_type' => 'search',
            'visitor_id' => 'visitor-a',
            'path' => '/',
            'label' => 'router',
            'page_type' => 'home',
            'occurred_at' => now()->subDay(),
        ]);

        AnalyticsEvent::query()->create([
            'event_type' => 'add_to_cart',
            'visitor_id' => 'visitor-b',
            'path' => '/product/starlink-mini-kit',
            'label' => 'Starlink Mini Kit',
            'page_type' => 'product',
            'occurred_at' => now()->subHours(3),
        ]);

        AnalyticsEvent::query()->create([
            'event_type' => 'whatsapp_product_click',
            'visitor_id' => 'visitor-b',
            'path' => '/product/starlink-mini-kit',
            'label' => 'Starlink Mini Kit',
            'page_type' => 'product',
            'occurred_at' => now()->subHours(2),
        ]);

        AnalyticsEvent::query()->create([
            'event_type' => 'whatsapp_cart_click',
            'visitor_id' => 'visitor-b',
            'path' => '/cart',
            'label' => 'Cart Checkout',
            'page_type' => 'cart',
            'occurred_at' => now()->subHour(),
        ]);

        $order = Order::query()->create([
            'order_number' => 'ORD-1001',
            'customer_name' => 'Jane Doe',
            'customer_email' => 'jane@example.com',
            'amount' => 49999,
            'status' => 'completed',
            'paid_at' => now()->subHours(2),
        ]);
        $order->forceFill([
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ])->save();

        Invoice::query()->create([
            'invoice_number' => 'INV-1001',
            'order_id' => $order->id,
            'amount' => 49999,
            'status' => 'paid',
            'issued_at' => now()->toDateString(),
            'due_at' => now()->addDays(7)->toDateString(),
            'paid_at' => now()->subHour(),
        ]);

        $enquiry = Enquiry::query()->create([
            'name' => 'Alex Client',
            'email' => 'alex@example.com',
            'phone' => '0700123456',
            'message' => 'Need Starlink installation support.',
            'status' => 'new',
        ]);
        $enquiry->forceFill([
            'created_at' => now()->subHours(8),
            'updated_at' => now()->subHours(8),
        ])->save();

        $response = $this->actingAs($user)->get(route('analytics.index', ['range' => 30]));

        $response->assertOk();
        $response->assertSeeText('Website Analytics');
        $response->assertSeeText('Starlink Mini Kit');
        $response->assertSeeText('Installation Guide');
        $response->assertSeeText('www.google.com');
        $response->assertSeeText('chatgpt.com');
        $response->assertSeeText('router');
        $response->assertSeeText('KSh 49,999.00');
        $response->assertSeeText('1 product order clicks / 1 cart checkout clicks.');
        $response->assertSeeText('1 cart adds / 2 WhatsApp clicks / 1 enquiries.');
        $response->assertSeeText('Product WhatsApp');
        $response->assertSeeText('Cart WhatsApp');
        $response->assertDontSeeText('Old Landing Page');
    }

    public function test_analytics_pages_fail_safe_when_tracking_table_is_missing(): void
    {
        $user = User::factory()->create();

        Schema::drop('analytics_events');

        $this->get('/')->assertOk();

        $response = $this->actingAs($user)->get(route('analytics.index'));

        $response->assertOk();
        $response->assertSeeText('Website Analytics');
        $response->assertSeeText('Waiting for first visit');
    }
}
