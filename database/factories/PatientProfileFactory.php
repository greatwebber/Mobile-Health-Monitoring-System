<?php

namespace Database\Factories;

use App\Enums\BloodGroup;
use App\Enums\Genotype;
use App\Enums\SexAtBirth;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PatientProfile>
 */
class PatientProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->patient(),
            'patient_number' => 'FPE-MC-TEST-'.fake()->unique()->numerify('######'),
            'date_of_birth' => fake()->dateTimeBetween('-60 years', '-16 years'),
            'sex_at_birth' => fake()->randomElement([SexAtBirth::Female, SexAtBirth::Male]),
            'phone' => fake()->numerify('080########'),
            'address' => fake()->address(),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => fake()->numerify('080########'),
            'blood_group' => fake()->randomElement(BloodGroup::cases()),
            'genotype' => fake()->randomElement(Genotype::cases()),
            'profile_completed_at' => now(),
        ];
    }

    public function incomplete(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_of_birth' => null,
            'sex_at_birth' => null,
            'phone' => null,
            'address' => null,
            'emergency_contact_name' => null,
            'emergency_contact_phone' => null,
            'blood_group' => null,
            'genotype' => null,
            'profile_completed_at' => null,
        ]);
    }
}
