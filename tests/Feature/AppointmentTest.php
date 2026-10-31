<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\ClinicalEncounter;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_nurse_can_schedule_and_reschedule_a_linked_follow_up(): void
    {
        $patient = PatientProfile::factory()->create();
        $encounter = ClinicalEncounter::factory()->for($patient)->create();
        $nurse = User::factory()->nurse()->create();
        $clinician = User::factory()->clinician()->create();

        $this->actingAs($nurse)
            ->post(route('appointments.store'), $this->validPayload($patient, $clinician, $encounter))
            ->assertRedirect();

        $appointment = Appointment::query()->sole();
        $this->assertSame(AppointmentStatus::Scheduled, $appointment->status);
        $this->assertSame($patient->id, $appointment->patient_profile_id);
        $this->assertSame($encounter->id, $appointment->clinical_encounter_id);
        $this->assertSame($nurse->id, $appointment->scheduled_by_user_id);

        $newTime = now()->addDays(5)->setTime(11, 30);
        $this->put(route('appointments.update', $appointment), [
            ...$this->validPayload($patient, $clinician, $encounter),
            'scheduled_at' => $newTime->format('Y-m-d H:i:s'),
            'reason' => 'Updated follow-up review',
        ])->assertRedirect(route('appointments.show', $appointment));

        $this->assertSame('Updated follow-up review', $appointment->refresh()->reason);
        $this->assertTrue($appointment->scheduled_at->equalTo($newTime));
    }

    public function test_appointment_validation_requires_future_time_and_matching_encounter_patient(): void
    {
        $patient = PatientProfile::factory()->create();
        $otherPatient = PatientProfile::factory()->create();
        $otherEncounter = ClinicalEncounter::factory()->for($otherPatient)->create();
        $nurse = User::factory()->nurse()->create();
        $clinician = User::factory()->clinician()->create();

        $this->actingAs($nurse)
            ->post(route('appointments.store'), [
                ...$this->validPayload($patient, $clinician, $otherEncounter),
                'scheduled_at' => now()->subHour()->format('Y-m-d H:i:s'),
            ])
            ->assertSessionHasErrors(['scheduled_at', 'clinical_encounter_id']);

        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_staff_can_record_a_documented_final_outcome_and_it_becomes_immutable(): void
    {
        $appointment = Appointment::factory()->create(['scheduled_at' => now()->subHour()]);
        $clinician = User::factory()->clinician()->create();

        $this->actingAs($clinician)
            ->post(route('appointments.transition', $appointment), [
                'status' => AppointmentStatus::Completed->value,
                'outcome_notes' => 'Patient attended and the planned follow-up review was completed.',
            ])
            ->assertRedirect(route('appointments.show', $appointment));

        $appointment->refresh();
        $this->assertSame(AppointmentStatus::Completed, $appointment->status);
        $this->assertSame($clinician->id, $appointment->status_changed_by_user_id);
        $this->assertNotNull($appointment->status_changed_at);

        $this->get(route('appointments.edit', $appointment))->assertForbidden();
        $this->post(route('appointments.transition', $appointment), [
            'status' => AppointmentStatus::Cancelled->value,
            'outcome_notes' => 'Attempt to replace an already final outcome.',
        ])->assertForbidden();
    }

    public function test_future_appointment_cannot_be_completed_or_marked_no_show_but_can_be_cancelled(): void
    {
        $appointment = Appointment::factory()->create(['scheduled_at' => now()->addDay()]);
        $nurse = User::factory()->nurse()->create();

        $this->actingAs($nurse)
            ->post(route('appointments.transition', $appointment), [
                'status' => AppointmentStatus::Completed->value,
                'outcome_notes' => 'This appointment has not happened yet.',
            ])
            ->assertSessionHasErrors('status');

        $this->post(route('appointments.transition', $appointment), [
            'status' => AppointmentStatus::Cancelled->value,
            'outcome_notes' => 'Cancelled after the patient requested a different care plan.',
        ])->assertRedirect(route('appointments.show', $appointment));

        $this->assertSame(AppointmentStatus::Cancelled, $appointment->refresh()->status);
    }

    public function test_patient_sees_only_their_appointments_and_cannot_manage_them(): void
    {
        $owned = Appointment::factory()->create();
        $other = Appointment::factory()->create();
        $owner = $owned->patientProfile->user;
        $administrator = User::factory()->administrator()->create();

        $this->actingAs($owner)
            ->get(route('appointments.index'))
            ->assertOk()
            ->assertSee($owned->appointment_number)
            ->assertDontSee($other->appointment_number);

        $this->get(route('appointments.show', $owned))->assertOk();
        $this->get(route('appointments.show', $other))->assertForbidden();
        $this->get(route('appointments.create'))->assertForbidden();

        $this->actingAs($administrator)->get(route('appointments.index'))->assertForbidden();
    }

    public function test_dashboard_shows_only_the_patients_own_upcoming_appointment_count(): void
    {
        $profile = PatientProfile::factory()->create();
        Appointment::factory()->for($profile)->create(['scheduled_at' => now()->addDays(2)]);
        Appointment::factory()->for($profile)->create([
            'scheduled_at' => now()->subDay(),
            'status' => AppointmentStatus::Completed,
        ]);
        Appointment::factory()->create(['scheduled_at' => now()->addDay()]);

        $this->actingAs($profile->user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Next: '.now()->addDays(2)->format('j M'))
            ->assertSee('>1<', escape: false);
    }

    /** @return array<string, mixed> */
    private function validPayload(
        PatientProfile $patient,
        User $clinician,
        ?ClinicalEncounter $encounter = null,
    ): array {
        return [
            'patient_profile_id' => $patient->id,
            'clinical_encounter_id' => $encounter?->id,
            'assigned_clinician_id' => $clinician->id,
            'scheduled_at' => now()->addDays(2)->setTime(10, 0)->format('Y-m-d H:i:s'),
            'duration_minutes' => 30,
            'reason' => 'Post-consultation follow-up review',
            'patient_instructions' => 'Please arrive ten minutes before the scheduled time.',
        ];
    }
}
