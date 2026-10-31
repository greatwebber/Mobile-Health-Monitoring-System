<?php

namespace Tests\Feature;

use App\Enums\ConsentType;
use App\Models\ConsentRecord;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_review_all_consent_preferences(): void
    {
        $profile = PatientProfile::factory()->create();

        $response = $this->actingAs($profile->user)->get(route('consents.index'));

        $response->assertOk()
            ->assertSee('Consent')
            ->assertSee('Care data processing')
            ->assertSee('Longitudinal health monitoring')
            ->assertSee('De-identified research use')
            ->assertSee('AI-assisted risk support');
    }

    public function test_patient_can_record_and_then_withdraw_consent_without_overwriting_history(): void
    {
        config()->set('health.consent_document_version', 'test-2.0');
        $profile = PatientProfile::factory()->create();
        $user = $profile->user;

        $this->actingAs($user)
            ->post(route('consents.store', ConsentType::AiRiskSupport->value), ['granted' => true])
            ->assertRedirect(route('consents.index'))
            ->assertSessionHas('status', 'consent-updated');

        $granted = ConsentRecord::query()->sole();
        $this->assertTrue($granted->granted);
        $this->assertSame(ConsentType::AiRiskSupport, $granted->consent_type);
        $this->assertSame('test-2.0', $granted->document_version);
        $this->assertSame($user->id, $granted->captured_by_user_id);
        $this->assertNull($granted->withdrawn_at);

        $this->travel(1)->minute();

        $this->post(route('consents.store', ConsentType::AiRiskSupport->value), ['granted' => false])
            ->assertRedirect(route('consents.index'))
            ->assertSessionHas('status', 'consent-updated');

        $this->assertDatabaseCount('consent_records', 2);
        $this->assertTrue($granted->fresh()->granted);

        $withdrawn = $profile->latestConsent(ConsentType::AiRiskSupport);
        $this->assertFalse($withdrawn->granted);
        $this->assertNotNull($withdrawn->withdrawn_at);
    }

    public function test_repeating_the_current_consent_preference_does_not_create_a_duplicate(): void
    {
        $profile = PatientProfile::factory()->create();
        ConsentRecord::factory()->for($profile)->create([
            'consent_type' => ConsentType::HealthMonitoring,
            'granted' => true,
        ]);

        $this->actingAs($profile->user)
            ->post(route('consents.store', ConsentType::HealthMonitoring->value), ['granted' => true])
            ->assertSessionHas('status', 'consent-unchanged');

        $this->assertDatabaseCount('consent_records', 1);
    }

    public function test_only_patients_with_profiles_can_submit_supported_consent_types(): void
    {
        $clinician = User::factory()->clinician()->create();
        $patientWithoutProfile = User::factory()->patient()->create();
        $profile = PatientProfile::factory()->create();

        $this->actingAs($clinician)
            ->post(route('consents.store', ConsentType::ResearchUse->value), ['granted' => true])
            ->assertForbidden();

        $this->actingAs($patientWithoutProfile)
            ->post(route('consents.store', ConsentType::ResearchUse->value), ['granted' => true])
            ->assertForbidden();

        $this->actingAs($profile->user)
            ->post(route('consents.store', 'unsupported-consent'), ['granted' => true])
            ->assertNotFound();

        $this->assertDatabaseCount('consent_records', 0);
    }

    public function test_consent_policy_enforces_owner_and_clinical_least_privilege_access(): void
    {
        $record = ConsentRecord::factory()->create();
        $owner = $record->patientProfile->user;
        $otherPatient = PatientProfile::factory()->create()->user;
        $nurse = User::factory()->nurse()->create();
        $clinician = User::factory()->clinician()->create();
        $administrator = User::factory()->administrator()->create();

        $this->assertTrue($owner->can('view', $record));
        $this->assertTrue($nurse->can('view', $record));
        $this->assertTrue($clinician->can('view', $record));
        $this->assertFalse($otherPatient->can('view', $record));
        $this->assertFalse($administrator->can('view', $record));
        $this->assertFalse($owner->can('update', $record));
        $this->assertFalse($clinician->can('update', $record));
    }
}
