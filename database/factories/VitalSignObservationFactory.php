<?php

namespace Database\Factories;

use App\Enums\ObservationSource;
use App\Models\ClinicalEncounter;
use App\Models\User;
use App\Models\VitalSignObservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<VitalSignObservation> */
class VitalSignObservationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clinical_encounter_id' => ClinicalEncounter::factory(),
            'measured_at' => now(),
            'temperature_c' => '36.8',
            'systolic_bp' => 120,
            'diastolic_bp' => 80,
            'heart_rate_bpm' => 72,
            'respiratory_rate_bpm' => 16,
            'oxygen_saturation_percent' => '98.00',
            'weight_kg' => null,
            'height_cm' => null,
            'blood_glucose_mg_dl' => null,
            'notes' => null,
            'source' => ObservationSource::Clinical,
            'recorded_by_user_id' => User::factory()->nurse(),
            'verified_by_user_id' => null,
            'verified_at' => null,
        ];
    }
}
