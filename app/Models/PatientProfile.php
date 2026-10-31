<?php

namespace App\Models;

use App\Enums\BloodGroup;
use App\Enums\ConsentType;
use App\Enums\Genotype;
use App\Enums\SexAtBirth;
use Database\Factories\PatientProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PatientProfile extends Model
{
    /** @use HasFactory<PatientProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_number',
        'date_of_birth',
        'sex_at_birth',
        'phone',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'blood_group',
        'genotype',
        'profile_completed_at',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'sex_at_birth' => SexAtBirth::class,
            'blood_group' => BloodGroup::class,
            'genotype' => Genotype::class,
            'profile_completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medicalHistory(): HasOne
    {
        return $this->hasOne(MedicalHistory::class);
    }

    public function consentRecords(): HasMany
    {
        return $this->hasMany(ConsentRecord::class);
    }

    public function clinicalEncounters(): HasMany
    {
        return $this->hasMany(ClinicalEncounter::class);
    }

    public function clinicalAlerts(): HasMany
    {
        return $this->hasMany(ClinicalAlert::class);
    }

    public function latestConsent(ConsentType $type): ?ConsentRecord
    {
        return $this->consentRecords()
            ->where('consent_type', $type->value)
            ->latest('decided_at')
            ->latest('id')
            ->first();
    }

    public static function numberFor(User $user): string
    {
        return sprintf('FPE-MC-P-%06d', $user->getKey());
    }

    public function completionPercentage(): int
    {
        $fields = [
            'date_of_birth',
            'sex_at_birth',
            'phone',
            'address',
            'emergency_contact_name',
            'emergency_contact_phone',
            'blood_group',
            'genotype',
        ];

        $completed = collect($fields)->filter(fn (string $field) => filled($this->{$field}))->count();

        return (int) round(($completed / count($fields)) * 100);
    }

    public function refreshCompletionStatus(): void
    {
        $required = [
            'date_of_birth',
            'sex_at_birth',
            'phone',
            'emergency_contact_name',
            'emergency_contact_phone',
        ];

        $isComplete = collect($required)->every(fn (string $field) => filled($this->{$field}));

        $this->forceFill([
            'profile_completed_at' => $isComplete ? ($this->profile_completed_at ?? now()) : null,
        ])->save();
    }
}
