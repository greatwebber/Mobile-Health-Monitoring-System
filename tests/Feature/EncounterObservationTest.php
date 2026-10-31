<?php

namespace Tests\Feature;

use App\Enums\ObservationSource;
use App\Enums\SymptomSeverity;
use App\Models\ClinicalEncounter;
use App\Models\EncounterSymptom;
use App\Models\PatientProfile;
use App\Models\User;
use App\Models\VitalSignObservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EncounterObservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_nurse_can_append_a_verified_symptom_to_an_active_encounter(): void
    {
        $encounter = ClinicalEncounter::factory()->create();
        $nurse = User::factory()->nurse()->create();

        $this->actingAs($nurse)
            ->post(route('encounters.symptoms.store', $encounter), [
                'name' => 'Headache',
                'severity' => SymptomSeverity::Moderate->value,
                'started_at' => now()->subDays(2)->format('Y-m-d H:i:s'),
                'duration' => 'Two days',
                'notes' => 'Intermittent frontal discomfort.',
            ])
            ->assertRedirect(route('encounters.show', $encounter).'#observations')
            ->assertSessionHas('status', 'symptom-recorded');

        $symptom = EncounterSymptom::query()->sole();
        $this->assertSame(SymptomSeverity::Moderate, $symptom->severity);
        $this->assertSame(ObservationSource::Clinical, $symptom->source);
        $this->assertSame($nurse->id, $symptom->recorded_by_user_id);
        $this->assertSame($nurse->id, $symptom->verified_by_user_id);
        $this->assertNotNull($symptom->verified_at);
    }

    public function test_clinician_can_append_a_verified_vital_sign_snapshot(): void
    {
        $encounter = ClinicalEncounter::factory()->create();
        $clinician = User::factory()->clinician()->create();

        $this->actingAs($clinician)
            ->post(route('encounters.vital-signs.store', $encounter), [
                'measured_at' => now()->format('Y-m-d H:i:s'),
                'temperature_c' => 37.2,
                'systolic_bp' => 122,
                'diastolic_bp' => 79,
                'heart_rate_bpm' => 74,
                'respiratory_rate_bpm' => 17,
                'oxygen_saturation_percent' => 98,
                'weight_kg' => 68.4,
                'height_cm' => 171,
                'blood_glucose_mg_dl' => 92,
                'notes' => 'Measured while seated.',
            ])
            ->assertRedirect(route('encounters.show', $encounter).'#observations')
            ->assertSessionHas('status', 'vitals-recorded');

        $observation = VitalSignObservation::query()->sole();
        $this->assertSame('37.2', $observation->temperature_c);
        $this->assertSame(122, $observation->systolic_bp);
        $this->assertSame(79, $observation->diastolic_bp);
        $this->assertSame(ObservationSource::Clinical, $observation->source);
        $this->assertSame($clinician->id, $observation->verified_by_user_id);
        $this->assertArrayHasKey('Blood pressure', $observation->formattedReadings());
    }

    public function test_vital_sign_validation_requires_a_reading_and_a_complete_blood_pressure_pair(): void
    {
        $encounter = ClinicalEncounter::factory()->create();
        $nurse = User::factory()->nurse()->create();

        $this->actingAs($nurse)
            ->from(route('encounters.show', $encounter).'#observations')
            ->post(route('encounters.vital-signs.store', $encounter), [
                'measured_at' => now()->format('Y-m-d H:i:s'),
            ])
            ->assertSessionHasErrors('vitals');

        $this->post(route('encounters.vital-signs.store', $encounter), [
            'measured_at' => now()->format('Y-m-d H:i:s'),
            'systolic_bp' => 120,
        ])->assertSessionHasErrors('diastolic_bp');

        $this->post(route('encounters.vital-signs.store', $encounter), [
            'measured_at' => now()->addMinute()->format('Y-m-d H:i:s'),
            'temperature_c' => 80,
        ])->assertSessionHasErrors(['measured_at', 'temperature_c']);

        $this->assertDatabaseCount('vital_sign_observations', 0);
    }

    public function test_final_encounters_reject_new_symptoms_and_vital_signs(): void
    {
        $encounter = ClinicalEncounter::factory()->completed()->create();
        $nurse = User::factory()->nurse()->create();

        $this->actingAs($nurse)
            ->post(route('encounters.symptoms.store', $encounter), [
                'name' => 'Cough',
                'severity' => SymptomSeverity::Mild->value,
            ])
            ->assertForbidden();

        $this->post(route('encounters.vital-signs.store', $encounter), [
            'measured_at' => now()->format('Y-m-d H:i:s'),
            'heart_rate_bpm' => 70,
        ])->assertForbidden();
    }

    public function test_patient_can_view_own_observations_but_cannot_create_them(): void
    {
        $profile = PatientProfile::factory()->create();
        $encounter = ClinicalEncounter::factory()->for($profile)->create();
        EncounterSymptom::factory()->for($encounter, 'clinicalEncounter')->create(['name' => 'Visible symptom']);
        VitalSignObservation::factory()->for($encounter, 'clinicalEncounter')->create(['temperature_c' => 36.9]);

        $this->actingAs($profile->user)
            ->get(route('encounters.show', $encounter))
            ->assertOk()
            ->assertSee('Visible symptom')
            ->assertSee('36.9 °C')
            ->assertDontSee('Record vital signs');

        $this->post(route('encounters.symptoms.store', $encounter), [
            'name' => 'Unauthorized symptom',
            'severity' => SymptomSeverity::Mild->value,
        ])->assertForbidden();
    }

    public function test_observation_policies_prevent_mutation_and_cross_patient_access(): void
    {
        $symptom = EncounterSymptom::factory()->create();
        $vitals = VitalSignObservation::factory()->for($symptom->clinicalEncounter, 'clinicalEncounter')->create();
        $owner = $symptom->clinicalEncounter->patientProfile->user;
        $otherPatient = PatientProfile::factory()->create()->user;
        $clinician = User::factory()->clinician()->create();

        $this->assertTrue($owner->can('view', $symptom));
        $this->assertTrue($owner->can('view', $vitals));
        $this->assertTrue($clinician->can('view', $symptom));
        $this->assertFalse($otherPatient->can('view', $symptom));
        $this->assertFalse($clinician->can('update', $symptom));
        $this->assertFalse($clinician->can('delete', $vitals));
    }
}
