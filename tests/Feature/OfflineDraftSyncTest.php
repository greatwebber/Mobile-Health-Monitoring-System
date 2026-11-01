<?php

namespace Tests\Feature;

use App\Enums\SexAtBirth;
use App\Models\OfflineSyncReceipt;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OfflineDraftSyncTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_synchronize_a_versioned_profile_draft(): void
    {
        $profile = PatientProfile::factory()->incomplete()->create();
        $draftId = (string) Str::uuid();

        $this->actingAs($profile->user)
            ->postJson(route('offline-sync.patient-profile'), $this->payload($profile, $draftId))
            ->assertOk()
            ->assertJsonPath('status', 'synchronized')
            ->assertJsonPath('resource_id', $profile->id);

        $profile->refresh();
        $this->assertSame('Federal Polytechnic Ede campus', $profile->address);
        $this->assertSame(SexAtBirth::Female, $profile->sex_at_birth);
        $this->assertNotNull($profile->profile_completed_at);
        $this->assertDatabaseHas('offline_sync_receipts', [
            'user_id' => $profile->user_id,
            'draft_id' => $draftId,
            'resource_type' => 'patient_profile',
            'resource_id' => $profile->id,
        ]);
    }

    public function test_retry_with_same_draft_id_is_idempotent(): void
    {
        $profile = PatientProfile::factory()->incomplete()->create();
        $draftId = (string) Str::uuid();
        $payload = $this->payload($profile, $draftId);

        $this->actingAs($profile->user)
            ->postJson(route('offline-sync.patient-profile'), $payload)
            ->assertOk();

        $profile->forceFill(['address' => 'Newer server value'])->save();

        $this->postJson(route('offline-sync.patient-profile'), [
            ...$payload,
            'address' => 'Duplicate retry must not overwrite',
        ])->assertOk()->assertJsonPath('status', 'already-synchronized');

        $this->assertSame('Newer server value', $profile->fresh()->address);
        $this->assertSame(1, OfflineSyncReceipt::query()->count());
    }

    public function test_stale_draft_returns_conflict_without_overwriting_server(): void
    {
        $profile = PatientProfile::factory()->incomplete()->create();
        $baseVersion = $profile->updated_at->toISOString();
        $this->travel(1)->second();
        $profile->forceFill(['address' => 'Newer server address'])->save();

        $this->actingAs($profile->user)
            ->postJson(route('offline-sync.patient-profile'), [
                ...$this->payload($profile, (string) Str::uuid()),
                'base_updated_at' => $baseVersion,
                'address' => 'Stale offline address',
            ])
            ->assertConflict()
            ->assertJsonPath('status', 'conflict');

        $this->assertSame('Newer server address', $profile->fresh()->address);
        $this->assertDatabaseCount('offline_sync_receipts', 0);
    }

    public function test_non_patient_and_invalid_drafts_are_rejected(): void
    {
        $nurse = User::factory()->nurse()->create();

        $this->actingAs($nurse)
            ->postJson(route('offline-sync.patient-profile'), [])
            ->assertForbidden();

        $profile = PatientProfile::factory()->incomplete()->create();

        $this->actingAs($profile->user)
            ->postJson(route('offline-sync.patient-profile'), [
                ...$this->payload($profile, 'not-a-uuid'),
                'phone' => 'invalid phone',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['draft_id', 'phone']);
    }

    public function test_profile_screen_exposes_encrypted_draft_controls_and_logout_cleanup_hook(): void
    {
        $profile = PatientProfile::factory()->create();

        $this->actingAs($profile->user)
            ->get(route('patient-profile.edit'))
            ->assertOk()
            ->assertSee('data-offline-draft="patient-profile"', escape: false)
            ->assertSee('data-offline-sync-url=', escape: false)
            ->assertSee('data-offline-base-version=', escape: false)
            ->assertSee('data-offline-draft-save', escape: false)
            ->assertSee('data-clear-private-drafts', escape: false);
    }

    public function test_client_uses_indexed_db_web_crypto_and_keeps_drafts_out_of_service_worker_cache(): void
    {
        $client = file_get_contents(resource_path('js/offline-drafts.js'));
        $serviceWorker = file_get_contents(public_path('service-worker.js'));

        $this->assertStringContainsString('indexedDB.open(DATABASE_NAME', $client);
        $this->assertStringContainsString("name: 'AES-GCM'", $client);
        $this->assertStringContainsString('crypto.subtle.encrypt', $client);
        $this->assertStringContainsString('response.status === 409', $client);
        $this->assertStringContainsString('store.delete(record.id)', $client);
        $this->assertStringNotContainsString('offline-sync', $serviceWorker);
        $this->assertStringNotContainsString('indexedDB', $serviceWorker);
    }

    /** @return array<string, mixed> */
    private function payload(PatientProfile $profile, string $draftId): array
    {
        return [
            'draft_id' => $draftId,
            'base_updated_at' => $profile->updated_at->toISOString(),
            'date_of_birth' => '2001-05-12',
            'sex_at_birth' => SexAtBirth::Female->value,
            'phone' => '08012345678',
            'address' => 'Federal Polytechnic Ede campus',
            'emergency_contact_name' => 'Demo Contact',
            'emergency_contact_phone' => '08087654321',
            'blood_group' => null,
            'genotype' => null,
        ];
    }
}
