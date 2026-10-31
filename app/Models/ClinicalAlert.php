<?php

namespace App\Models;

use App\Enums\AlertSeverity;
use App\Enums\AlertStatus;
use App\Enums\VitalMetric;
use Database\Factories\ClinicalAlertFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalAlert extends Model
{
    /** @use HasFactory<ClinicalAlertFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_profile_id',
        'clinical_encounter_id',
        'vital_sign_observation_id',
        'threshold_rule_id',
        'metric',
        'measured_value',
        'unit',
        'severity',
        'status',
        'rule_name',
        'rule_version',
        'message',
        'detected_at',
        'acknowledged_by_user_id',
        'acknowledged_at',
        'resolved_by_user_id',
        'resolved_at',
        'resolution_notes',
    ];

    protected function casts(): array
    {
        return [
            'metric' => VitalMetric::class,
            'measured_value' => 'decimal:4',
            'severity' => AlertSeverity::class,
            'status' => AlertStatus::class,
            'detected_at' => 'datetime',
            'acknowledged_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function displayValue(): string
    {
        return rtrim(rtrim($this->measured_value, '0'), '.').' '.$this->unit;
    }

    public function patientProfile(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class);
    }

    public function clinicalEncounter(): BelongsTo
    {
        return $this->belongsTo(ClinicalEncounter::class);
    }

    public function vitalSignObservation(): BelongsTo
    {
        return $this->belongsTo(VitalSignObservation::class);
    }

    public function thresholdRule(): BelongsTo
    {
        return $this->belongsTo(ThresholdRule::class);
    }

    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by_user_id');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by_user_id');
    }
}
