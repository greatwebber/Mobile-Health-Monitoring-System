<?php

namespace Database\Factories;

use App\Models\MedicalHistory;
use App\Models\PatientProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalHistory>
 */
class MedicalHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_profile_id' => PatientProfile::factory(),
            'allergies' => ['Penicillin'],
            'chronic_conditions' => [],
            'current_medications' => [],
            'previous_surgeries' => [],
            'family_history' => ['Hypertension'],
            'additional_notes' => null,
            'updated_by_user_id' => null,
            'reviewed_at' => null,
            'reviewed_by_user_id' => null,
        ];
    }
}
