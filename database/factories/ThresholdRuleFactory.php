<?php

namespace Database\Factories;

use App\Enums\AlertSeverity;
use App\Enums\VitalMetric;
use App\Models\ThresholdRule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ThresholdRule> */
class ThresholdRuleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Test temperature review rule',
            'metric' => VitalMetric::Temperature,
            'minimum_value' => null,
            'maximum_value' => 37.5,
            'unit' => VitalMetric::Temperature->unit(),
            'severity' => AlertSeverity::Advisory,
            'guidance' => 'Review the measurement and assess the patient according to the approved clinical protocol.',
            'version' => 1,
            'is_active' => false,
            'created_by_user_id' => User::factory()->administrator(),
            'approved_by_user_id' => null,
            'approved_at' => null,
            'deactivated_by_user_id' => null,
            'deactivated_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'is_active' => true,
            'approved_by_user_id' => User::factory()->clinician(),
            'approved_at' => now(),
        ]);
    }
}
