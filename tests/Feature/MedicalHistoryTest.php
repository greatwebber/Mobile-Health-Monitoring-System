<?php

namespace Tests\Feature;

use App\Models\MedicalHistory;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_an_empty_medical_history_for_the_patient(): void
    {
        $this->post('/register', [
            'name' => 'New Patient',
            'email' => 'new.patient@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'new.patient@example.com')->firstOrFail();
        $history = $user->patientProfile->medicalHistory;

        $this->assertNotNull($history);
        $this->assertSame($user->id, $history->updated_by_user_id);
        $this->assertNull($history->allergies);
        $this->assertNull($history->additional_notes);
    }

    public function test_patient_can_view_and_update_structured_medical_history(): void
    {
        $profile = PatientProfile::factory()->create();
        $history = MedicalHistory::factory()->for($profile)->create();

        $this->actingAs($profile->user)
            ->get(route('medical-history.edit'))
            ->assertOk()
            ->assertSee('Medical history')
            ->assertSee('Penicillin');

        $this->patch(route('medical-history.update'), [
            'allergies_text' => " Penicillin \n\npenicillin\nPeanuts ",
            'chronic_conditions_text' => "Asthma\r\nHypertension",
            'current_medications_text' => 'Salbutamol inhaler',
            'previous_surgeries_text' => 'Appendectomy — 2019',
            'family_history_text' => 'Mother — diabetes',
            'additional_notes' => '  Carry emergency inhaler.  ',
        ])->assertRedirect(route('medical-history.edit'))
            ->assertSessionHas('status', 'medical-history-updated');

        $history->refresh();
        $this->assertSame(['Penicillin', 'Peanuts'], $history->allergies);
        $this->assertSame(['Asthma', 'Hypertension'], $history->chronic_conditions);
        $this->assertSame(['Salbutamol inhaler'], $history->current_medications);
        $this->assertSame(['Appendectomy — 2019'], $history->previous_surgeries);
        $this->assertSame(['Mother — diabetes'], $history->family_history);
        $this->assertSame('Carry emergency inhaler.', $history->additional_notes);
        $this->assertSame($profile->user_id, $history->updated_by_user_id);
    }

    public function test_medical_history_update_validates_field_lengths(): void
    {
        $profile = PatientProfile::factory()->create();
        MedicalHistory::factory()->for($profile)->create();

        $this->actingAs($profile->user)
            ->from(route('medical-history.edit'))
            ->patch(route('medical-history.update'), [
                'allergies_text' => str_repeat('a', 2001),
                'additional_notes' => str_repeat('n', 3001),
            ])
            ->assertRedirect(route('medical-history.edit'))
            ->assertSessionHasErrors(['allergies_text', 'additional_notes']);
    }

    public function test_non_patient_cannot_open_or_update_the_patient_medical_history_screen(): void
    {
        $clinician = User::factory()->clinician()->create();

        $this->actingAs($clinician)
            ->get(route('medical-history.edit'))
            ->assertForbidden();

        $this->patch(route('medical-history.update'), ['allergies_text' => 'Latex'])
            ->assertForbidden();
    }

    public function test_medical_history_policy_enforces_owner_and_clinical_least_privilege_access(): void
    {
        $history = MedicalHistory::factory()->create();
        $owner = $history->patientProfile->user;
        $otherPatient = PatientProfile::factory()->create()->user;
        $nurse = User::factory()->nurse()->create();
        $clinician = User::factory()->clinician()->create();
        $administrator = User::factory()->administrator()->create();

        $this->assertTrue($owner->can('view', $history));
        $this->assertTrue($owner->can('update', $history));
        $this->assertTrue($nurse->can('view', $history));
        $this->assertTrue($clinician->can('view', $history));
        $this->assertFalse($nurse->can('update', $history));
        $this->assertFalse($clinician->can('update', $history));
        $this->assertFalse($otherPatient->can('view', $history));
        $this->assertFalse($administrator->can('view', $history));
    }
}
