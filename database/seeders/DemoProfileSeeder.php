<?php

namespace Database\Seeders;

use App\Enums\AlertSeverity;
use App\Enums\BloodGroup;
use App\Enums\ClinicalResultCategory;
use App\Enums\ClinicalResultFlag;
use App\Enums\ConsentType;
use App\Enums\EncounterStatus;
use App\Enums\EncounterType;
use App\Enums\Genotype;
use App\Enums\ObservationSource;
use App\Enums\SexAtBirth;
use App\Enums\SymptomSeverity;
use App\Enums\VitalMetric;
use App\Models\ClinicalEncounter;
use App\Models\ClinicalResult;
use App\Models\EncounterSymptom;
use App\Models\PatientProfile;
use App\Models\StaffProfile;
use App\Models\ThresholdRule;
use App\Models\User;
use App\Models\VitalSignObservation;
use Illuminate\Database\Seeder;
use LogicException;

class DemoProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new LogicException('Demo profiles may only be seeded in local or testing environments.');
        }

        $patient = User::where('email', 'patient@edehealth.test')->firstOrFail();

        $patientProfile = PatientProfile::query()->updateOrCreate(
            ['user_id' => $patient->id],
            [
                'patient_number' => PatientProfile::numberFor($patient),
                'date_of_birth' => '2002-06-15',
                'sex_at_birth' => SexAtBirth::Female,
                'phone' => '08000000001',
                'address' => 'Demo residence, Federal Polytechnic Ede campus',
                'emergency_contact_name' => 'Demo Contact',
                'emergency_contact_phone' => '08000000002',
                'blood_group' => BloodGroup::OPositive,
                'genotype' => Genotype::AA,
                'profile_completed_at' => now(),
            ],
        );

        $patientProfile->medicalHistory()->updateOrCreate(
            [],
            [
                'allergies' => ['Penicillin'],
                'chronic_conditions' => [],
                'current_medications' => [],
                'previous_surgeries' => [],
                'family_history' => ['Hypertension'],
                'additional_notes' => 'Fictional demonstration record only.',
                'updated_by_user_id' => $patient->id,
            ],
        );

        foreach (ConsentType::cases() as $consentType) {
            $patientProfile->consentRecords()->firstOrCreate(
                [
                    'consent_type' => $consentType,
                    'document_version' => config('health.consent_document_version'),
                ],
                [
                    'granted' => true,
                    'decided_at' => now(),
                    'captured_by_user_id' => $patient->id,
                ],
            );
        }

        $staffProfiles = [
            'admin@edehealth.test' => [
                'staff_number' => 'FPE-MC-STAFF-0001',
                'professional_title' => 'System Administrator',
                'department' => 'Health Information Systems',
                'license_number' => null,
                'phone' => '08000000003',
            ],
            'clinician@edehealth.test' => [
                'staff_number' => 'FPE-MC-STAFF-0002',
                'professional_title' => 'Medical Officer',
                'department' => 'Clinical Services',
                'license_number' => 'DEMO-MDCN-0001',
                'phone' => '08000000004',
            ],
            'nurse@edehealth.test' => [
                'staff_number' => 'FPE-MC-STAFF-0003',
                'professional_title' => 'Nursing Officer',
                'department' => 'Nursing Services',
                'license_number' => 'DEMO-NMCN-0001',
                'phone' => '08000000005',
            ],
        ];

        foreach ($staffProfiles as $email => $attributes) {
            $staff = User::where('email', $email)->firstOrFail();

            StaffProfile::query()->updateOrCreate(
                ['user_id' => $staff->id],
                $attributes,
            );
        }

        $clinician = User::where('email', 'clinician@edehealth.test')->firstOrFail();
        $nurse = User::where('email', 'nurse@edehealth.test')->firstOrFail();

        $demoEncounter = ClinicalEncounter::query()->firstOrCreate(
            ['encounter_number' => 'FPE-MC-E-DEMO-0001'],
            [
                'patient_profile_id' => $patientProfile->id,
                'type' => EncounterType::Consultation,
                'status' => EncounterStatus::Completed,
                'occurred_at' => now()->subDays(7)->setTime(10, 30),
                'chief_complaint' => 'Fictional demonstration visit for intermittent headache.',
                'clinical_notes' => 'Demo assessment only. Patient was clinically stable during this fictional encounter.',
                'disposition' => 'Routine follow-up advised if symptoms persist.',
                'created_by_user_id' => $nurse->id,
                'assigned_clinician_id' => $clinician->id,
                'completed_at' => now()->subDays(7)->setTime(11, 0),
            ],
        );

        EncounterSymptom::query()->firstOrCreate(
            [
                'clinical_encounter_id' => $demoEncounter->id,
                'name' => 'Intermittent headache',
            ],
            [
                'severity' => SymptomSeverity::Mild,
                'started_at' => $demoEncounter->occurred_at->copy()->subDays(2),
                'duration' => 'Two days',
                'notes' => 'Fictional demonstration symptom.',
                'source' => ObservationSource::Clinical,
                'recorded_by_user_id' => $nurse->id,
                'verified_by_user_id' => $nurse->id,
                'verified_at' => $demoEncounter->occurred_at,
            ],
        );

        VitalSignObservation::query()->firstOrCreate(
            [
                'clinical_encounter_id' => $demoEncounter->id,
                'measured_at' => $demoEncounter->occurred_at,
            ],
            [
                'temperature_c' => 36.7,
                'systolic_bp' => 118,
                'diastolic_bp' => 76,
                'heart_rate_bpm' => 72,
                'respiratory_rate_bpm' => 16,
                'oxygen_saturation_percent' => 98,
                'weight_kg' => 62.5,
                'notes' => 'Fictional demonstration measurements.',
                'source' => ObservationSource::Clinical,
                'recorded_by_user_id' => $nurse->id,
                'verified_by_user_id' => $nurse->id,
                'verified_at' => $demoEncounter->occurred_at,
            ],
        );

        foreach ([
            [10, 36.8, 116, 75, 70, 98],
            [20, 36.7, 115, 74, 69, 99],
        ] as [$minutes, $temperature, $systolic, $diastolic, $heartRate, $oxygenSaturation]) {
            $measuredAt = $demoEncounter->occurred_at->copy()->addMinutes($minutes);

            VitalSignObservation::query()->firstOrCreate(
                [
                    'clinical_encounter_id' => $demoEncounter->id,
                    'measured_at' => $measuredAt,
                ],
                [
                    'temperature_c' => $temperature,
                    'systolic_bp' => $systolic,
                    'diastolic_bp' => $diastolic,
                    'heart_rate_bpm' => $heartRate,
                    'respiratory_rate_bpm' => 16,
                    'oxygen_saturation_percent' => $oxygenSaturation,
                    'notes' => 'Fictional trend demonstration measurement.',
                    'source' => ObservationSource::Clinical,
                    'recorded_by_user_id' => $nurse->id,
                    'verified_by_user_id' => $nurse->id,
                    'verified_at' => $measuredAt,
                ],
            );
        }

        ClinicalResult::query()->firstOrCreate(
            [
                'clinical_encounter_id' => $demoEncounter->id,
                'test_code' => 'HGB-DEMO',
            ],
            [
                'category' => ClinicalResultCategory::Haematology,
                'test_name' => 'Haemoglobin',
                'value_numeric' => 13.2,
                'unit' => 'g/dL',
                'reference_range' => '12.0–16.0 g/dL',
                'flag' => ClinicalResultFlag::NotFlagged,
                'specimen' => 'Whole blood',
                'collected_at' => $demoEncounter->occurred_at,
                'resulted_at' => $demoEncounter->occurred_at->copy()->addMinutes(30),
                'notes' => 'Fictional demonstration result.',
                'source' => ObservationSource::Clinical,
                'recorded_by_user_id' => $clinician->id,
                'verified_by_user_id' => $clinician->id,
                'verified_at' => $demoEncounter->occurred_at->copy()->addMinutes(30),
            ],
        );

        $administrator = User::where('email', 'admin@edehealth.test')->firstOrFail();

        ThresholdRule::query()->firstOrCreate(
            [
                'name' => '[DEMO DRAFT] Temperature review placeholder',
                'version' => 1,
            ],
            [
                'metric' => VitalMetric::Temperature,
                'minimum_value' => null,
                'maximum_value' => 38,
                'unit' => VitalMetric::Temperature->unit(),
                'severity' => AlertSeverity::Advisory,
                'guidance' => 'Fictional local demonstration rule only. Replace with institutionally approved clinical guidance before activation.',
                'is_active' => false,
                'created_by_user_id' => $administrator->id,
            ],
        );
    }
}
