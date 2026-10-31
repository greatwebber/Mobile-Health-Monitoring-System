<?php

namespace App\Enums;

use App\Models\VitalSignObservation;

enum VitalMetric: string
{
    case Temperature = 'temperature_c';
    case SystolicBloodPressure = 'systolic_bp';
    case DiastolicBloodPressure = 'diastolic_bp';
    case HeartRate = 'heart_rate_bpm';
    case RespiratoryRate = 'respiratory_rate_bpm';
    case OxygenSaturation = 'oxygen_saturation_percent';
    case BloodGlucose = 'blood_glucose_mg_dl';

    public function label(): string
    {
        return match ($this) {
            self::Temperature => 'Temperature',
            self::SystolicBloodPressure => 'Systolic blood pressure',
            self::DiastolicBloodPressure => 'Diastolic blood pressure',
            self::HeartRate => 'Heart rate',
            self::RespiratoryRate => 'Respiratory rate',
            self::OxygenSaturation => 'Oxygen saturation',
            self::BloodGlucose => 'Blood glucose',
        };
    }

    public function unit(): string
    {
        return match ($this) {
            self::Temperature => '°C',
            self::SystolicBloodPressure, self::DiastolicBloodPressure => 'mmHg',
            self::HeartRate => 'bpm',
            self::RespiratoryRate => 'breaths/min',
            self::OxygenSaturation => '%',
            self::BloodGlucose => 'mg/dL',
        };
    }

    public function valueFrom(VitalSignObservation $observation): ?float
    {
        $value = $observation->{$this->value};

        return $value === null ? null : (float) $value;
    }
}
