<?php

namespace Tests\Feature;

use App\Enums\EncounterStatus;
use App\Enums\EncounterType;
use App\Models\ClinicalEncounter;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicalEncounterTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_view_only_their_own_encounter_history(): void
    {
        $profile = PatientProfile::factory()->create();
        $ownEncounter = ClinicalEncounter::factory()->for($profile)->create([
            'chief_complaint' => 'Own encounter marker',
        ]);
        ClinicalEncounter::factory()->create(['chief_complaint' => 'Other patient marker']);

        $this->actingAs($profile->user)
            ->get(route('encounters.index'))
            ->assertOk()
            ->assertSee('Own encounter marker')
            ->assertDontSee('Other patient marker');

        $this->get(route('encounters.show', $ownEncounter))
            ->assertOk()
            ->assertSee($ownEncounter->encounter_number);
    }

    public function test_nurse_can_open_an_encounter_for_a_registered_patient(): void
    {
        $patient = PatientProfile::factory()->create();
        $nurse = User::factory()->nurse()->create();
        $clinician = User::factory()->clinician()->create();

        $this->actingAs($nurse)
            ->get(route('encounters.create'))
            ->assertOk()
            ->assertSee('Open a clinical encounter');

        $response = $this->post(route('encounters.store'), [
            'patient_profile_id' => $patient->id,
            'type' => EncounterType::Triage->value,
            'occurred_at' => now()->format('Y-m-d H:i:s'),
            'chief_complaint' => 'Persistent cough for three days.',
            'assigned_clinician_id' => $clinician->id,
        ]);

        $encounter = ClinicalEncounter::query()->sole();
        $response->assertRedirect(route('encounters.show', $encounter))
            ->assertSessionHas('status', 'encounter-created');

        $this->assertStringStartsWith('FPE-MC-E-', $encounter->encounter_number);
        $this->assertSame(EncounterStatus::Open, $encounter->status);
        $this->assertSame($nurse->id, $encounter->created_by_user_id);
        $this->assertSame($clinician->id, $encounter->assigned_clinician_id);
    }

    public function test_clinician_can_document_and_complete_an_active_encounter(): void
    {
        $encounter = ClinicalEncounter::factory()->create();
        $clinician = User::factory()->clinician()->create();

        $this->actingAs($clinician)
            ->get(route('encounters.edit', $encounter))
            ->assertOk()
            ->assertSee('Update encounter');

        $this->patch(route('encounters.update', $encounter), [
            'type' => EncounterType::Consultation->value,
            'status' => EncounterStatus::Completed->value,
            'occurred_at' => $encounter->occurred_at->format('Y-m-d H:i:s'),
            'chief_complaint' => 'Persistent cough for three days.',
            'clinical_notes' => 'Patient assessed and stable.',
            'disposition' => 'Review in one week if symptoms persist.',
            'assigned_clinician_id' => $clinician->id,
        ])->assertRedirect(route('encounters.show', $encounter))
            ->assertSessionHas('status', 'encounter-updated');

        $encounter->refresh();
        $this->assertSame(EncounterStatus::Completed, $encounter->status);
        $this->assertSame('Patient assessed and stable.', $encounter->clinical_notes);
        $this->assertNotNull($encounter->completed_at);
    }

    public function test_completed_encounter_is_immutable(): void
    {
        $encounter = ClinicalEncounter::factory()->completed()->create();
        $clinician = User::factory()->clinician()->create();

        $this->actingAs($clinician)
            ->get(route('encounters.edit', $encounter))
            ->assertForbidden();

        $this->patch(route('encounters.update', $encounter), [
            'type' => EncounterType::Consultation->value,
            'status' => EncounterStatus::Open->value,
            'occurred_at' => now()->format('Y-m-d H:i:s'),
            'chief_complaint' => 'Attempted change',
        ])->assertForbidden();

        $this->assertNotSame('Attempted change', $encounter->fresh()->chief_complaint);
    }

    public function test_encounter_validation_rejects_future_visits_and_non_clinician_assignment(): void
    {
        $patient = PatientProfile::factory()->create();
        $nurse = User::factory()->nurse()->create();
        $invalidAssignee = User::factory()->nurse()->create();

        $this->actingAs($nurse)
            ->from(route('encounters.create'))
            ->post(route('encounters.store'), [
                'patient_profile_id' => $patient->id,
                'type' => EncounterType::Triage->value,
                'occurred_at' => now()->addDay()->format('Y-m-d H:i:s'),
                'chief_complaint' => '',
                'assigned_clinician_id' => $invalidAssignee->id,
            ])
            ->assertRedirect(route('encounters.create'))
            ->assertSessionHasErrors(['occurred_at', 'chief_complaint', 'assigned_clinician_id']);

        $this->assertDatabaseCount('clinical_encounters', 0);
    }

    public function test_encounter_policy_enforces_patient_ownership_and_clinical_access(): void
    {
        $encounter = ClinicalEncounter::factory()->create();
        $owner = $encounter->patientProfile->user;
        $otherPatient = PatientProfile::factory()->create()->user;
        $nurse = User::factory()->nurse()->create();
        $clinician = User::factory()->clinician()->create();
        $administrator = User::factory()->administrator()->create();

        $this->assertTrue($owner->can('view', $encounter));
        $this->assertFalse($owner->can('update', $encounter));
        $this->assertTrue($nurse->can('view', $encounter));
        $this->assertTrue($nurse->can('update', $encounter));
        $this->assertTrue($clinician->can('view', $encounter));
        $this->assertTrue($clinician->can('update', $encounter));
        $this->assertFalse($otherPatient->can('view', $encounter));
        $this->assertFalse($administrator->can('view', $encounter));
        $this->assertFalse($administrator->can('create', ClinicalEncounter::class));
    }

    public function test_patient_and_administrator_cannot_access_staff_encounter_management(): void
    {
        $patient = PatientProfile::factory()->create()->user;
        $administrator = User::factory()->administrator()->create();

        $this->actingAs($patient)
            ->get(route('encounters.create'))
            ->assertForbidden();

        $this->post(route('encounters.store'), [])
            ->assertForbidden();

        $this->actingAs($administrator)
            ->get(route('encounters.index'))
            ->assertForbidden();
    }

    public function test_clinical_staff_can_search_the_encounter_register(): void
    {
        $nurse = User::factory()->nurse()->create();
        $matchingPatient = PatientProfile::factory()->create();
        $matchingPatient->user->update(['name' => 'Unique Search Patient']);
        ClinicalEncounter::factory()->for($matchingPatient)->create(['chief_complaint' => 'Visible result']);
        ClinicalEncounter::factory()->create(['chief_complaint' => 'Hidden result']);

        $this->actingAs($nurse)
            ->get(route('encounters.index', ['search' => 'Unique Search']))
            ->assertOk()
            ->assertSee('Visible result')
            ->assertDontSee('Hidden result');
    }
}
