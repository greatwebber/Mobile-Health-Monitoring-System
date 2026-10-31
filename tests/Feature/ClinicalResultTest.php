<?php

namespace Tests\Feature;

use App\Enums\ClinicalResultCategory;
use App\Enums\ClinicalResultFlag;
use App\Enums\ObservationSource;
use App\Models\ClinicalEncounter;
use App\Models\ClinicalResult;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClinicalResultTest extends TestCase
{
    use RefreshDatabase;

    public function test_clinician_can_append_a_verified_numeric_result(): void
    {
        $encounter = ClinicalEncounter::factory()->create();
        $clinician = User::factory()->clinician()->create();

        $this->actingAs($clinician)
            ->post(route('encounters.clinical-results.store', $encounter), [
                'category' => ClinicalResultCategory::Haematology->value,
                'test_name' => 'Haemoglobin',
                'test_code' => 'HGB',
                'value_numeric' => 13.4,
                'unit' => 'g/dL',
                'reference_range' => '12.0–16.0 g/dL',
                'flag' => ClinicalResultFlag::NotFlagged->value,
                'specimen' => 'Whole blood',
                'collected_at' => now()->subHour()->format('Y-m-d H:i:s'),
                'resulted_at' => now()->format('Y-m-d H:i:s'),
            ])
            ->assertRedirect(route('encounters.show', $encounter).'#observations')
            ->assertSessionHas('status', 'clinical-result-recorded');

        $result = ClinicalResult::query()->sole();
        $this->assertSame(ClinicalResultCategory::Haematology, $result->category);
        $this->assertSame(ClinicalResultFlag::NotFlagged, $result->flag);
        $this->assertSame(ObservationSource::Clinical, $result->source);
        $this->assertSame('13.4 g/dL', $result->displayValue());
        $this->assertSame($clinician->id, $result->recorded_by_user_id);
        $this->assertSame($clinician->id, $result->verified_by_user_id);
        $this->assertNotNull($result->verified_at);
    }

    public function test_text_results_are_supported_without_a_numeric_value(): void
    {
        $encounter = ClinicalEncounter::factory()->create();
        $nurse = User::factory()->nurse()->create();

        $this->actingAs($nurse)
            ->post(route('encounters.clinical-results.store', $encounter), [
                'category' => ClinicalResultCategory::Microbiology->value,
                'test_name' => 'Malaria parasite screen',
                'value_text' => 'Negative',
                'flag' => ClinicalResultFlag::NotFlagged->value,
                'resulted_at' => now()->format('Y-m-d H:i:s'),
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('Negative', ClinicalResult::query()->sole()->displayValue());
    }

    public function test_result_validation_requires_a_value_and_valid_timestamps(): void
    {
        $encounter = ClinicalEncounter::factory()->create();
        $clinician = User::factory()->clinician()->create();

        $this->actingAs($clinician)
            ->post(route('encounters.clinical-results.store', $encounter), [
                'category' => ClinicalResultCategory::Chemistry->value,
                'test_name' => 'Blood glucose',
                'flag' => ClinicalResultFlag::NotFlagged->value,
                'collected_at' => now()->addHours(2)->format('Y-m-d H:i:s'),
                'resulted_at' => now()->addHour()->format('Y-m-d H:i:s'),
            ])
            ->assertSessionHasErrors(['result_value', 'collected_at', 'resulted_at']);

        $this->assertDatabaseCount('clinical_results', 0);
    }

    public function test_final_encounter_and_patient_reject_new_results(): void
    {
        $completed = ClinicalEncounter::factory()->completed()->create();
        $nurse = User::factory()->nurse()->create();
        $payload = [
            'category' => ClinicalResultCategory::Other->value,
            'test_name' => 'Test',
            'value_text' => 'Result',
            'flag' => ClinicalResultFlag::NotFlagged->value,
            'resulted_at' => now()->format('Y-m-d H:i:s'),
        ];

        $this->actingAs($nurse)
            ->post(route('encounters.clinical-results.store', $completed), $payload)
            ->assertForbidden();

        $active = ClinicalEncounter::factory()->create();
        $this->actingAs($active->patientProfile->user)
            ->post(route('encounters.clinical-results.store', $active), $payload)
            ->assertForbidden();
    }

    public function test_patient_can_view_own_result_but_not_another_patients_result(): void
    {
        $profile = PatientProfile::factory()->create();
        $encounter = ClinicalEncounter::factory()->for($profile)->create();
        $result = ClinicalResult::factory()->for($encounter, 'clinicalEncounter')->create([
            'test_name' => 'Visible haemoglobin',
        ]);
        $otherPatient = PatientProfile::factory()->create()->user;

        $this->actingAs($profile->user)
            ->get(route('encounters.show', $encounter))
            ->assertOk()
            ->assertSee('Visible haemoglobin')
            ->assertSee('13.4 g/dL')
            ->assertDontSee('Record laboratory or clinical result');

        $this->assertTrue($profile->user->can('view', $result));
        $this->assertFalse($otherPatient->can('view', $result));
    }

    public function test_results_are_append_only_for_clinical_staff(): void
    {
        $result = ClinicalResult::factory()->create();
        $clinician = User::factory()->clinician()->create();

        $this->assertTrue($clinician->can('view', $result));
        $this->assertFalse($clinician->can('update', $result));
        $this->assertFalse($clinician->can('delete', $result));
    }
}
