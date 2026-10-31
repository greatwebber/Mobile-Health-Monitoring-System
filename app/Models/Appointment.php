<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Database\Factories\AppointmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Appointment extends Model
{
    /** @use HasFactory<AppointmentFactory> */
    use HasFactory;

    protected $fillable = [
        'appointment_number',
        'patient_profile_id',
        'clinical_encounter_id',
        'assigned_clinician_id',
        'scheduled_at',
        'duration_minutes',
        'reason',
        'patient_instructions',
        'status',
        'outcome_notes',
        'scheduled_by_user_id',
        'status_changed_by_user_id',
        'status_changed_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'status' => AppointmentStatus::class,
            'status_changed_at' => 'datetime',
        ];
    }

    public static function generateNumber(): string
    {
        do {
            $number = 'FPE-MC-A-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (self::where('appointment_number', $number)->exists());

        return $number;
    }

    public function patientProfile(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class);
    }

    public function clinicalEncounter(): BelongsTo
    {
        return $this->belongsTo(ClinicalEncounter::class);
    }

    public function assignedClinician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_clinician_id');
    }

    public function scheduledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scheduled_by_user_id');
    }

    public function statusChangedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'status_changed_by_user_id');
    }
}
