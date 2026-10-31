<?php

namespace Database\Factories;

use App\Enums\ClinicalResultCategory;
use App\Enums\ClinicalResultFlag;
use App\Enums\ObservationSource;
use App\Models\ClinicalEncounter;
use App\Models\ClinicalResult;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ClinicalResult> */
class ClinicalResultFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clinical_encounter_id' => ClinicalEncounter::factory(),
            'category' => ClinicalResultCategory::Haematology,
            'test_name' => 'Haemoglobin',
            'test_code' => 'HGB',
            'value_numeric' => 13.4,
            'value_text' => null,
            'unit' => 'g/dL',
            'reference_range' => '12.0–16.0 g/dL',
            'flag' => ClinicalResultFlag::NotFlagged,
            'specimen' => 'Whole blood',
            'collected_at' => now()->subHour(),
            'resulted_at' => now(),
            'notes' => null,
            'source' => ObservationSource::Clinical,
            'recorded_by_user_id' => User::factory()->clinician(),
            'verified_by_user_id' => null,
            'verified_at' => null,
        ];
    }
}
