<?php

namespace App\Http\Requests;

use App\Enums\EncounterType;
use App\Enums\UserRole;
use App\Models\ClinicalEncounter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClinicalEncounterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', ClinicalEncounter::class) ?? false;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'patient_profile_id' => ['required', 'integer', 'exists:patient_profiles,id'],
            'type' => ['required', Rule::enum(EncounterType::class)],
            'occurred_at' => ['required', 'date', 'before_or_equal:now'],
            'chief_complaint' => ['required', 'string', 'max:1000'],
            'clinical_notes' => ['nullable', 'string', 'max:5000'],
            'disposition' => ['nullable', 'string', 'max:2000'],
            'assigned_clinician_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query
                    ->where('role', UserRole::Clinician->value)
                    ->where('is_active', true)),
            ],
        ];
    }
}
