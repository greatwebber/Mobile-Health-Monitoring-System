<?php

namespace Database\Factories;

use App\Enums\ConsentType;
use App\Models\ConsentRecord;
use App\Models\PatientProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ConsentRecord>
 */
class ConsentRecordFactory extends Factory
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
            'consent_type' => fake()->randomElement(ConsentType::cases()),
            'document_version' => 'test-1.0',
            'granted' => true,
            'decided_at' => now(),
            'withdrawn_at' => null,
            'captured_by_user_id' => null,
        ];
    }

    public function withdrawn(): static
    {
        return $this->state(fn (array $attributes) => [
            'granted' => false,
            'withdrawn_at' => now(),
        ]);
    }
}
