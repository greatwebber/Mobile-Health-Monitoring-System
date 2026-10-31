<?php

namespace App\Http\Requests;

use App\Enums\BloodGroup;
use App\Enums\Genotype;
use App\Enums\SexAtBirth;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $profile = $this->user()?->patientProfile;

        return $profile !== null && $this->user()->can('update', $profile);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
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
