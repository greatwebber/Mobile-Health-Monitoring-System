<?php

namespace Tests\Feature;

use Tests\TestCase;

class PwaFoundationTest extends TestCase
{
    public function test_public_home_exposes_the_pwa_foundation(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('EdeHealth Sentinel')
            ->assertSee('manifest.webmanifest', escape: false)
            ->assertSee('service-worker.js', escape: false);
    }

    public function test_manifest_contains_installable_app_metadata_and_icons(): void
    {
        $manifestPath = public_path('manifest.webmanifest');

        $this->assertFileExists($manifestPath);

        $manifest = json_decode(file_get_contents($manifestPath), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('EdeHealth Sentinel', $manifest['name']);
        $this->assertSame('standalone', $manifest['display']);
        $this->assertSame('#075f5b', $manifest['theme_color']);
        $this->assertCount(4, $manifest['icons']);

        foreach ($manifest['icons'] as $icon) {
            $this->assertFileExists(public_path($icon['src']));
        }
    }

    public function test_service_worker_has_an_offline_fallback_without_precaching_private_pages(): void
    {
        $serviceWorker = file_get_contents(public_path('service-worker.js'));

        $this->assertStringContainsString('offline.html', $serviceWorker);
        $this->assertStringContainsString("request.mode === 'navigate'", $serviceWorker);
        $this->assertStringNotContainsString("'/dashboard'", $serviceWorker);
        $this->assertStringNotContainsString("'/profile'", $serviceWorker);
    }

    public function test_apache_serves_the_manifest_correctly_and_revalidates_service_worker_updates(): void
    {
        $apacheConfiguration = file_get_contents(public_path('.htaccess'));

        $this->assertStringContainsString('application/manifest+json .webmanifest', $apacheConfiguration);
        $this->assertStringContainsString('no-cache, no-store, must-revalidate', $apacheConfiguration);
    }
}
