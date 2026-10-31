<?php

namespace App\Http\Requests;

use App\Models\ClinicalEncounter;
use App\Models\VitalSignObservation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreVitalSignObservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $encounter = $this->route('encounter');

        return $encounter instanceof ClinicalEncounter
            && ($this->user()?->can('create', [VitalSignObservation::class, $encounter]) ?? false);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'measured_at' => ['required', 'date', 'before_or_equal:now'],
            'temperature_c' => ['nullable', 'numeric', 'between:25,50'],
            'systolic_bp' => ['nullable', 'integer', 'between:40,300', 'required_with:diastolic_bp'],
            'diastolic_bp' => ['nullable', 'integer', 'between:20,200', 'required_with:systolic_bp'],
            'heart_rate_bpm' => ['nullable', 'integer', 'between:20,300'],
            'respiratory_rate_bpm' => ['nullable', 'integer', 'between:5,80'],
            'oxygen_saturation_percent' => ['nullable', 'numeric', 'between:30,100'],
            'weight_kg' => ['nullable', 'numeric', 'between:1,500'],
            'height_cm' => ['nullable', 'numeric', 'between:30,250'],
            'blood_glucose_mg_dl' => ['nullable', 'numeric', 'between:10,1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /** @return array<callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $hasReading = collect(VitalSignObservation::READING_FIELDS)
                ->contains(fn (string $field) => filled($this->input($field)));

            if (! $hasReading) {
                $validator->errors()->add('vitals', 'Enter at least one vital-sign measurement.');
            }
        }];
    }
}
