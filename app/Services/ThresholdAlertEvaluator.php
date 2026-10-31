<?php

namespace App\Services;

use App\Enums\AlertStatus;
use App\Enums\VitalMetric;
use App\Models\ClinicalAlert;
use App\Models\ThresholdRule;
use App\Models\VitalSignObservation;
use Illuminate\Support\Collection;

class ThresholdAlertEvaluator
{
    /** @return Collection<int, ClinicalAlert> */
    public function evaluate(VitalSignObservation $observation): Collection
    {
        $observation->loadMissing('clinicalEncounter');
        $created = collect();

        foreach (VitalMetric::cases() as $metric) {
            $value = $metric->valueFrom($observation);

            if ($value === null) {
                continue;
            }

            $rules = ThresholdRule::query()
                ->where('metric', $metric->value)
                ->where('is_active', true)
                ->whereNotNull('approved_by_user_id')
                ->whereNotNull('approved_at')
                ->get();

            foreach ($rules as $rule) {
                if (! $rule->isBreached($value)) {
                    continue;
                }

                $alert = ClinicalAlert::query()->firstOrCreate(
                    [
                        'vital_sign_observation_id' => $observation->id,
                        'threshold_rule_id' => $rule->id,
                    ],
                    [
                        'patient_profile_id' => $observation->clinicalEncounter->patient_profile_id,
                        'clinical_encounter_id' => $observation->clinical_encounter_id,
                        'metric' => $metric,
                        'measured_value' => $value,
                        'unit' => $metric->unit(),
                        'severity' => $rule->severity,
                        'status' => AlertStatus::Open,
                        'rule_name' => $rule->name,
                        'rule_version' => $rule->version,
                        'message' => $metric->label().' measurement requires review under the approved rule “'.$rule->name.'”.',
                        'detected_at' => now(),
                    ],
                );

                if ($alert->wasRecentlyCreated) {
                    $created->push($alert);
                }
            }
        }

        return $created;
    }
}
