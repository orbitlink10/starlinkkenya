<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
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

    public function test_admin_can_upload_site_logo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $logoPath = tempnam(sys_get_temp_dir(), 'logo').'.png';

        file_put_contents(
            $logoPath,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAFgwJ/luzP9QAAAABJRU5ErkJggg==')
        );

        $logo = new UploadedFile($logoPath, 'logo.png', 'image/png', null, true);

        $response = $this->actingAs($user)->post(route('admin.settings.update'), [
            'whatsapp_phone' => '254712345678',
            'logo' => $logo,
        ]);

        $response->assertRedirect(route('admin.section', ['section' => 'settings']));

        $logoPath = SiteSetting::value('logo_path');

        $this->assertIsString($logoPath);
        $this->assertStringStartsWith('site/logo/', $logoPath);
        Storage::disk('public')->assertExists($logoPath);
    }

    public function test_settings_page_falls_back_when_site_settings_table_is_missing(): void
    {
        Schema::dropIfExists('site_settings');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.section', ['section' => 'settings']));

        $response->assertOk();
        $response->assertSee('name="whatsapp_phone"', false);
        $response->assertSee('value="254700123456"', false);
    }
}
