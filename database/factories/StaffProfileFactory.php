<?php

namespace Database\Factories;

use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StaffProfile>
 */
class StaffProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->clinician(),
            'staff_number' => 'FPE-MC-STAFF-'.fake()->unique()->numerify('####'),
            'professional_title' => 'Medical Officer',
            'department' => 'Clinical Services',
            'license_number' => 'TEST-LIC-'.fake()->unique()->numerify('######'),
            'phone' => fake()->numerify('080########'),
        ];
    }
}
