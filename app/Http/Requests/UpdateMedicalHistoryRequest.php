<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicalHistoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $history = $this->user()?->patientProfile?->medicalHistory;

        return $history !== null && $this->user()->can('update', $history);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'allergies_text' => ['nullable', 'string', 'max:2000'],
            'chronic_conditions_text' => ['nullable', 'string', 'max:2000'],
            'current_medications_text' => ['nullable', 'string', 'max:2000'],
            'previous_surgeries_text' => ['nullable', 'string', 'max:2000'],
            'family_history_text' => ['nullable', 'string', 'max:2000'],
            'additional_notes' => ['nullable', 'string', 'max:3000'],
        ];
    }
}
