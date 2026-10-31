<?php

namespace App\Models;

use App\Enums\ObservationSource;
use Database\Factories\VitalSignObservationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VitalSignObservation extends Model
{
    /** @use HasFactory<VitalSignObservationFactory> */
    use HasFactory;

    public const READING_FIELDS = [
        'temperature_c',
        'systolic_bp',
        'diastolic_bp',
        'heart_rate_bpm',
        'respiratory_rate_bpm',
        'oxygen_saturation_percent',
        'weight_kg',
        'height_cm',
        'blood_glucose_mg_dl',
    ];

    protected $fillable = [
        'clinical_encounter_id',
        'measured_at',
        ...self::READING_FIELDS,
        'notes',
        'source',
        'recorded_by_user_id',
        'verified_by_user_id',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'measured_at' => 'datetime',
            'temperature_c' => 'decimal:1',
            'oxygen_saturation_percent' => 'decimal:2',
            'weight_kg' => 'decimal:2',
            'height_cm' => 'decimal:2',
            'blood_glucose_mg_dl' => 'decimal:2',
            'source' => ObservationSource::class,
            'verified_at' => 'datetime',
        ];
    }

    /** @return array<string, string> */
    public function formattedReadings(): array
    {
        $readings = [];

        if ($this->temperature_c !== null) {
            $readings['Temperature'] = $this->temperature_c.' °C';
        }
        if ($this->systolic_bp !== null && $this->diastolic_bp !== null) {
            $readings['Blood pressure'] = $this->systolic_bp.'/'.$this->diastolic_bp.' mmHg';
        }
        if ($this->heart_rate_bpm !== null) {
            $readings['Heart rate'] = $this->heart_rate_bpm.' bpm';
        }
        if ($this->respiratory_rate_bpm !== null) {
            $readings['Respiratory rate'] = $this->respiratory_rate_bpm.' breaths/min';
        }
        if ($this->oxygen_saturation_percent !== null) {
            $readings['Oxygen saturation'] = $this->oxygen_saturation_percent.'%';
        }
        if ($this->weight_kg !== null) {
            $readings['Weight'] = $this->weight_kg.' kg';
        }
        if ($this->height_cm !== null) {
            $readings['Height'] = $this->height_cm.' cm';
        }
        if ($this->blood_glucose_mg_dl !== null) {
            $readings['Blood glucose'] = $this->blood_glucose_mg_dl.' mg/dL';
        }

        return $readings;
    }

    public function clinicalEncounter(): BelongsTo
    {
        return $this->belongsTo(ClinicalEncounter::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by_user_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_user_id');
    }

    public function clinicalAlerts(): HasMany
    {
        return $this->hasMany(ClinicalAlert::class);
    }
}
