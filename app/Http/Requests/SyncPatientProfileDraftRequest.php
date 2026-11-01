<?php

namespace App\Http\Requests;

use App\Enums\BloodGroup;
use App\Enums\Genotype;
use App\Enums\SexAtBirth;
use App\Enums\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncPatientProfileDraftRequest extends FormRequest
{
    public function authorize(): bool
    {
        $profile = $this->user()?->patientProfile;

        return $profile !== null
            && $this->user()->is_active
            && $this->user()->hasRole(UserRole::Patient)
            && $this->user()->can('update', $profile);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'draft_id' => ['required', 'uuid'],
            'base_updated_at' => ['required', 'date'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today', 'after:1900-01-01'],
            'sex_at_birth' => ['nullable', Rule::enum(SexAtBirth::class)],
            'phone' => ['nullable', 'string', 'max:32', 'regex:/^\+?[0-9][0-9\s\-()]{6,30}$/'],
            'address' => ['nullable', 'string', 'max:1000'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:32', 'regex:/^\+?[0-9][0-9\s\-()]{6,30}$/'],
            'blood_group' => ['nullable', Rule::enum(BloodGroup::class)],
            'genotype' => ['nullable', Rule::enum(Genotype::class)],
        ];
    }
}
