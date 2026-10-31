<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\Appointment;
use App\Models\ClinicalEncounter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Appointment::class) ?? false;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'patient_profile_id' => ['required', 'integer', 'exists:patient_profiles,id'],
            'clinical_encounter_id' => ['nullable', 'integer', 'exists:clinical_encounters,id'],
            'assigned_clinician_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where(fn ($query) => $query
                    ->where('role', UserRole::Clinician->value)
                    ->where('is_active', true)),
            ],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'duration_minutes' => ['required', 'integer', 'min:10', 'max:240'],
            'reason' => ['required', 'string', 'max:500'],
            'patient_instructions' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /** @return array<callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if (! $this->filled('clinical_encounter_id') || ! $this->filled('patient_profile_id')) {
                return;
            }

            $matchesPatient = ClinicalEncounter::query()
                ->whereKey($this->integer('clinical_encounter_id'))
                ->where('patient_profile_id', $this->integer('patient_profile_id'))
                ->exists();

            if (! $matchesPatient) {
                $validator->errors()->add('clinical_encounter_id', 'The linked encounter must belong to the selected patient.');
            }
        }];
    }
}
