<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_whatsapp_phone_number(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.settings.update'), [
            'whatsapp_phone' => '+254 712 345 678',
        ]);

        $response->assertRedirect(route('admin.section', ['section' => 'settings']));
        $response->assertSessionHas('success', 'Settings updated successfully.');

        $this->assertSame('254712345678', SiteSetting::value('whatsapp_phone'));
    }

    public function test_settings_page_shows_whatsapp_phone_field(): void
    {
        SiteSetting::setValue('whatsapp_phone', '254733444555');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.section', ['section' => 'settings']));

        $response->assertOk();
        $response->assertSee('name="whatsapp_phone"', false);
        $response->assertSee('value="254733444555"', false);
        $response->assertSee('https://wa.me/254733444555', false);
    }
}
