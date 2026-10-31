<?php

namespace App\Http\Requests;

use App\Models\ClinicalAlert;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ResolveClinicalAlertRequest extends FormRequest
{
    public function authorize(): bool
    {
        $alert = $this->route('alert');

        return $alert instanceof ClinicalAlert
            && ($this->user()?->can('resolve', $alert) ?? false);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'resolution_notes' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }
}
