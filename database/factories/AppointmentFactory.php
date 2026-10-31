<?php

namespace Database\Factories;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Appointment> */
class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'appointment_number' => Appointment::generateNumber(),
            'patient_profile_id' => PatientProfile::factory(),
            'clinical_encounter_id' => null,
            'assigned_clinician_id' => User::factory()->clinician(),
            'scheduled_at' => now()->addDays(2)->setTime(10, 0),
            'duration_minutes' => 30,
            'reason' => 'Routine clinical follow-up',
            'patient_instructions' => 'Please arrive ten minutes before the scheduled time.',
            'status' => AppointmentStatus::Scheduled,
            'outcome_notes' => null,
            'scheduled_by_user_id' => User::factory()->nurse(),
            'status_changed_by_user_id' => null,
            'status_changed_at' => null,
        ];
    }
}
