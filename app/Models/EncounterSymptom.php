<?php

namespace App\Models;

use App\Enums\ObservationSource;
use App\Enums\SymptomSeverity;
use Database\Factories\EncounterSymptomFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EncounterSymptom extends Model
{
    /** @use HasFactory<EncounterSymptomFactory> */
    use HasFactory;

    protected $fillable = [
        'clinical_encounter_id',
        'name',
        'severity',
        'started_at',
        'duration',
        'notes',
        'source',
        'recorded_by_user_id',
        'verified_by_user_id',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'severity' => SymptomSeverity::class,
            'source' => ObservationSource::class,
            'started_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
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
}
