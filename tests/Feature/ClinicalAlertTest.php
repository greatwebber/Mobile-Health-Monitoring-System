<?php

namespace Tests\Feature;

use App\Enums\AlertStatus;
use App\Models\ClinicalAlert;
use App\Models\ClinicalEncounter;
use App\Models\PatientProfile;
use App\Models\ThresholdRule;
use App\Models\User;
use App\Models\VitalSignObservation;
use App\Services\ThresholdAlertEvaluator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicalAlertTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_active_clinician_approved_rules_generate_unique_alerts(): void
    {
        $observation = VitalSignObservation::factory()->create(['temperature_c' => 38.2]);
        $draft = ThresholdRule::factory()->create(['maximum_value' => 37.5]);
        $evaluator = app(ThresholdAlertEvaluator::class);

        $this->assertCount(0, $evaluator->evaluate($observation));
        $this->assertDatabaseCount('clinical_alerts', 0);

        $approved = ThresholdRule::factory()->approved()->create(['maximum_value' => 37.5]);

        $this->assertCount(1, $evaluator->evaluate($observation));
        $this->assertCount(0, $evaluator->evaluate($observation));
        $this->assertDatabaseCount('clinical_alerts', 1);

        $alert = ClinicalAlert::query()->sole();
        $this->assertSame($approved->id, $alert->threshold_rule_id);
        $this->assertNotSame($draft->id, $alert->threshold_rule_id);
        $this->assertSame(AlertStatus::Open, $alert->status);
        $this->assertSame('38.2000', $alert->measured_value);

        $inRangeObservation = VitalSignObservation::factory()->create(['temperature_c' => 37.2]);
        $this->assertCount(0, $evaluator->evaluate($inRangeObservation));
        $this->assertDatabaseCount('clinical_alerts', 1);
    }

    public function test_recording_vitals_evaluates_approved_rules_automatically(): void
    {
        $encounter = ClinicalEncounter::factory()->create();
        $nurse = User::factory()->nurse()->create();
        ThresholdRule::factory()->approved()->create(['maximum_value' => 37.5]);

        $this->actingAs($nurse)
            ->post(route('encounters.vital-signs.store', $encounter), [
                'measured_at' => now()->format('Y-m-d H:i:s'),
                'temperature_c' => 38.4,
            ])
            ->assertRedirect(route('encounters.show', $encounter).'#observations');

        $this->assertDatabaseCount('clinical_alerts', 1);
        $this->assertDatabaseHas('clinical_alerts', [
            'patient_profile_id' => $encounter->patient_profile_id,
            'clinical_encounter_id' => $encounter->id,
            'status' => AlertStatus::Open->value,
        ]);
    }

    public function test_nurse_can_acknowledge_and_only_clinician_can_resolve_with_notes(): void
    {
        $alert = $this->makeAlert();
        $nurse = User::factory()->nurse()->create();
        $clinician = User::factory()->clinician()->create();

        $this->actingAs($nurse)
            ->post(route('alerts.acknowledge', $alert))
            ->assertRedirect(route('alerts.show', $alert));

        $alert->refresh();
        $this->assertSame(AlertStatus::Acknowledged, $alert->status);
        $this->assertSame($nurse->id, $alert->acknowledged_by_user_id);

        $this->post(route('alerts.resolve', $alert), [
            'resolution_notes' => 'Reviewed and documented after clinical assessment.',
        ])->assertForbidden();

        $this->actingAs($clinician)
            ->post(route('alerts.resolve', $alert), ['resolution_notes' => 'Too short'])
            ->assertSessionHasErrors('resolution_notes');

        $this->post(route('alerts.resolve', $alert), [
            'resolution_notes' => 'Reviewed and documented after clinical assessment.',
        ])->assertRedirect(route('alerts.show', $alert));

        $alert->refresh();
        $this->assertSame(AlertStatus::Resolved, $alert->status);
        $this->assertSame($clinician->id, $alert->resolved_by_user_id);
        $this->assertNotNull($alert->resolved_at);
    }

    public function test_patient_access_is_limited_to_their_own_alerts_and_admin_has_no_clinical_access(): void
    {
        $alert = $this->makeAlert();
        $owner = $alert->patientProfile->user;
        $otherPatient = PatientProfile::factory()->create()->user;
        $administrator = User::factory()->administrator()->create();

        $this->actingAs($owner)
            ->get(route('alerts.index'))
            ->assertOk()
            ->assertSee($alert->rule_name);

        $this->get(route('alerts.show', $alert))->assertOk();
        $this->actingAs($otherPatient)->get(route('alerts.show', $alert))->assertForbidden();
        $this->actingAs($administrator)->get(route('alerts.index'))->assertForbidden();
        $this->get(route('alerts.show', $alert))->assertForbidden();
    }

    private function makeAlert(): ClinicalAlert
    {
        $observation = VitalSignObservation::factory()->create(['temperature_c' => 38.2]);
        ThresholdRule::factory()->approved()->create(['maximum_value' => 37.5]);

        app(ThresholdAlertEvaluator::class)->evaluate($observation);

        return ClinicalAlert::query()->sole();
    }
}
