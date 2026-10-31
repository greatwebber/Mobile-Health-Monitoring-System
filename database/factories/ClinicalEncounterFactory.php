<?php

namespace Database\Factories;

use App\Enums\EncounterStatus;
use App\Enums\EncounterType;
use App\Models\ClinicalEncounter;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ClinicalEncounter> */
class ClinicalEncounterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'patient_profile_id' => PatientProfile::factory(),
            'encounter_number' => ClinicalEncounter::generateNumber(),
            'type' => fake()->randomElement(EncounterType::cases()),
            'status' => EncounterStatus::Open,
            'occurred_at' => now(),
            'chief_complaint' => fake()->sentence(),
            'clinical_notes' => null,
            'disposition' => null,
            'created_by_user_id' => User::factory()->nurse(),
            'assigned_clinician_id' => null,
            'completed_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => EncounterStatus::Completed,
            'clinical_notes' => 'Patient was assessed and clinically stable.',
            'disposition' => 'Routine follow-up advised.',
            'completed_at' => now(),
        ]);
    }
}
