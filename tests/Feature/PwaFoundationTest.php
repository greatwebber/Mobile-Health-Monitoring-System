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
            ->assertSee('service-worker.js', escape: false)
            ->assertSee('data-pwa-update-notice', escape: false)
            ->assertSee('data-pwa-update-refresh', escape: false);
    }

    public function test_manifest_contains_installable_app_metadata_and_icons(): void
    {
        $manifestPath = public_path('manifest.webmanifest');

        $this->assertFileExists($manifestPath);

        $manifest = json_decode(file_get_contents($manifestPath), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('EdeHealth Sentinel', $manifest['name']);
        $this->assertSame('standalone', $manifest['display']);
        $this->assertSame('#075f5b', $manifest['theme_color']);
        $this->assertSame('en', $manifest['lang']);
        $this->assertSame('ltr', $manifest['dir']);
        $this->assertSame('./', $manifest['start_url']);
        $this->assertSame('./', $manifest['scope']);
        $this->assertCount(4, $manifest['icons']);

        foreach ($manifest['icons'] as $icon) {
            $path = public_path($icon['src']);
            $this->assertFileExists($path);
            [$width, $height] = getimagesize($path);
            [$declaredWidth, $declaredHeight] = array_map('intval', explode('x', $icon['sizes']));
            $this->assertSame($declaredWidth, $width);
            $this->assertSame($declaredHeight, $height);
        }

        $this->assertContains('any', array_column($manifest['icons'], 'purpose'));
        $this->assertContains('maskable', array_column($manifest['icons'], 'purpose'));
    }

    public function test_service_worker_has_an_offline_fallback_without_precaching_private_pages(): void
    {
        $serviceWorker = file_get_contents(public_path('service-worker.js'));

        $this->assertStringContainsString('offline.html', $serviceWorker);
        $this->assertStringContainsString("request.mode === 'navigate'", $serviceWorker);
        $this->assertStringContainsString('icon-maskable-192.png', $serviceWorker);
        $this->assertStringContainsString('icon-maskable-512.png', $serviceWorker);
        $this->assertStringContainsString("event.data?.type === 'SKIP_WAITING'", $serviceWorker);
        $this->assertStringNotContainsString("'/dashboard'", $serviceWorker);
        $this->assertStringNotContainsString("'/profile'", $serviceWorker);
    }

    public function test_app_exposes_a_deliberate_service_worker_update_flow(): void
    {
        $client = file_get_contents(resource_path('js/app.js'));

        $this->assertStringContainsString("registration.addEventListener('updatefound'", $client);
        $this->assertStringContainsString('registration.waiting', $client);
        $this->assertStringContainsString("navigator.serviceWorker.addEventListener('controllerchange'", $client);
        $this->assertStringContainsString('if (!appUpdateRequested || reloadForServiceWorkerUpdate) return;', $client);
        $this->assertStringContainsString("waitingWorker.postMessage({ type: 'SKIP_WAITING' })", $client);
        $this->assertStringContainsString('Unsaved form changes will not be preserved.', view('partials.pwa-update-notice')->render());
    }

    public function test_offline_page_is_self_contained_and_communicates_safe_recovery(): void
    {
        $offlinePage = file_get_contents(public_path('offline.html'));

        $this->assertStringContainsString('You’re currently offline', $offlinePage);
        $this->assertStringContainsString('Try again', $offlinePage);
        $this->assertStringContainsString('urgent medical help', $offlinePage);
        $this->assertStringNotContainsString('<script src=', $offlinePage);
        $this->assertStringNotContainsString('<link rel="stylesheet"', $offlinePage);
    }

    public function test_apache_serves_the_manifest_correctly_and_revalidates_service_worker_updates(): void
    {
        $apacheConfiguration = file_get_contents(public_path('.htaccess'));

        $this->assertStringContainsString('application/manifest+json .webmanifest', $apacheConfiguration);
        $this->assertStringContainsString('no-cache, no-store, must-revalidate', $apacheConfiguration);
    }
}
