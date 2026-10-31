<?php

namespace App\Models;

use App\Enums\EncounterStatus;
use App\Enums\EncounterType;
use Database\Factories\ClinicalEncounterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ClinicalEncounter extends Model
{
    /** @use HasFactory<ClinicalEncounterFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_profile_id',
        'encounter_number',
        'type',
        'status',
        'occurred_at',
        'chief_complaint',
        'clinical_notes',
        'disposition',
        'created_by_user_id',
        'assigned_clinician_id',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => EncounterType::class,
            'status' => EncounterStatus::class,
            'occurred_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public static function generateNumber(): string
    {
        do {
            $number = 'FPE-MC-E-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (self::where('encounter_number', $number)->exists());

        return $number;
    }

    public function patientProfile(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function assignedClinician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_clinician_id');
    }

    public function symptoms(): HasMany
    {
        return $this->hasMany(EncounterSymptom::class);
    }

    public function vitalSignObservations(): HasMany
    {
        return $this->hasMany(VitalSignObservation::class);
    }

    public function clinicalResults(): HasMany
    {
        return $this->hasMany(ClinicalResult::class);
    }

    public function clinicalAlerts(): HasMany
    {
        return $this->hasMany(ClinicalAlert::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
