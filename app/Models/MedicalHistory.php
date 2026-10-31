<?php

namespace App\Models;

use Database\Factories\MedicalHistoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalHistory extends Model
{
    /** @use HasFactory<MedicalHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'allergies',
        'chronic_conditions',
        'current_medications',
        'previous_surgeries',
        'family_history',
        'additional_notes',
        'updated_by_user_id',
        'reviewed_at',
        'reviewed_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'allergies' => 'array',
            'chronic_conditions' => 'array',
            'current_medications' => 'array',
            'previous_surgeries' => 'array',
            'family_history' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }

    public function patientProfile(): BelongsTo
    {
        return $this->belongsTo(PatientProfile::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
