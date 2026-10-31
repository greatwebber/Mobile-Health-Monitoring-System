<?php

namespace Tests\Feature;

use App\Models\PatientProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationLoadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_shell_exposes_an_accessible_delayed_navigation_loader(): void
    {
        $profile = PatientProfile::factory()->create();

        $this->actingAs($profile->user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('id="navigation-loader"', escape: false)
            ->assertSee('role="status"', escape: false)
            ->assertSee('aria-live="polite"', escape: false)
            ->assertSee('Loading securely')
            ->assertSee('id="secure-session-indicator"', escape: false);

        $script = file_get_contents(resource_path('js/app.js'));

        $this->assertStringContainsString('window.setTimeout(() => {', $script);
        $this->assertStringContainsString('}, 180);', $script);
        $this->assertStringContainsString("document.addEventListener('click'", $script);
        $this->assertStringContainsString("document.addEventListener('submit'", $script);
        $this->assertStringContainsString("window.addEventListener('pageshow'", $script);
        $this->assertStringContainsString("document.body.setAttribute('aria-busy', 'true')", $script);
    }

    public function test_turbo_drive_is_configured_for_reload_free_private_navigation(): void
    {
        $profile = PatientProfile::factory()->create();

        $this->actingAs($profile->user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('<meta name="turbo-cache-control" content="no-cache">', escape: false)
            ->assertSee('<meta name="turbo-prefetch" content="false">', escape: false)
            ->assertSee('data-turbo-track="reload"', escape: false);

        $script = file_get_contents(resource_path('js/app.js'));
        $package = json_decode(file_get_contents(base_path('package.json')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertStringContainsString("import '@hotwired/turbo';", $script);
        $this->assertStringContainsString("document.addEventListener('turbo:before-visit'", $script);
        $this->assertStringContainsString("document.addEventListener('turbo:submit-start'", $script);
        $this->assertStringContainsString("document.addEventListener('turbo:load'", $script);
        $this->assertSame('^8.0.23', $package['dependencies']['@hotwired/turbo']);
    }
}
