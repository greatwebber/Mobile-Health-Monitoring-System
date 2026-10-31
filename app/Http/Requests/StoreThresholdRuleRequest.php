<?php

namespace App\Http\Requests;

use App\Enums\AlertSeverity;
use App\Enums\VitalMetric;
use App\Models\ThresholdRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreThresholdRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', ThresholdRule::class) ?? false;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:160'],
            'metric' => ['required', Rule::enum(VitalMetric::class)],
            'minimum_value' => ['nullable', 'numeric', 'between:-9999999999,9999999999'],
            'maximum_value' => ['nullable', 'numeric', 'between:-9999999999,9999999999'],
            'severity' => ['required', Rule::enum(AlertSeverity::class)],
            'guidance' => ['required', 'string', 'max:3000'],
            'version' => ['required', 'integer', 'min:1', 'max:9999'],
        ];
    }

    /** @return array<callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $minimum = $this->input('minimum_value');
            $maximum = $this->input('maximum_value');

            if (blank($minimum) && blank($maximum)) {
                $validator->errors()->add('threshold', 'Enter a minimum value, maximum value or both.');
            }

            if (filled($minimum) && filled($maximum) && (float) $minimum >= (float) $maximum) {
                $validator->errors()->add('maximum_value', 'The maximum value must be greater than the minimum value.');
            }
        }];
    }
}
