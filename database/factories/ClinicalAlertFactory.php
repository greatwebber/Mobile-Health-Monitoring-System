<?php

namespace Database\Factories;

use App\Enums\AlertSeverity;
use App\Enums\AlertStatus;
use App\Enums\VitalMetric;
use App\Models\ClinicalAlert;
use App\Models\ThresholdRule;
use App\Models\VitalSignObservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ClinicalAlert> */
class ClinicalAlertFactory extends Factory
{
    public function definition(): array
    {
        $observation = VitalSignObservation::factory()->create();
        $rule = ThresholdRule::factory()->approved()->create();

        return [
            'patient_profile_id' => $observation->clinicalEncounter->patient_profile_id,
            'clinical_encounter_id' => $observation->clinical_encounter_id,
            'vital_sign_observation_id' => $observation->id,
            'threshold_rule_id' => $rule->id,
            'metric' => VitalMetric::Temperature,
            'measured_value' => 38.2,
            'unit' => VitalMetric::Temperature->unit(),
            'severity' => AlertSeverity::Advisory,
            'status' => AlertStatus::Open,
            'rule_name' => $rule->name,
            'rule_version' => $rule->version,
            'message' => 'Temperature is outside the approved review range.',
            'detected_at' => now(),
            'acknowledged_by_user_id' => null,
            'acknowledged_at' => null,
            'resolved_by_user_id' => null,
            'resolved_at' => null,
            'resolution_notes' => null,
        ];
    }
}
