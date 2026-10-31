<?php

namespace Database\Factories;

use App\Enums\ObservationSource;
use App\Enums\SymptomSeverity;
use App\Models\ClinicalEncounter;
use App\Models\EncounterSymptom;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<EncounterSymptom> */
class EncounterSymptomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clinical_encounter_id' => ClinicalEncounter::factory(),
            'name' => fake()->randomElement(['Headache', 'Cough', 'Fatigue']),
            'severity' => fake()->randomElement(SymptomSeverity::cases()),
            'started_at' => now()->subDay(),
            'duration' => 'One day',
            'notes' => null,
            'source' => ObservationSource::Clinical,
            'recorded_by_user_id' => User::factory()->nurse(),
            'verified_by_user_id' => null,
            'verified_at' => null,
        ];
    }
}
