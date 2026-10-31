<?php

namespace App\Http\Requests;

use App\Enums\SymptomSeverity;
use App\Models\ClinicalEncounter;
use App\Models\EncounterSymptom;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEncounterSymptomRequest extends FormRequest
{
    public function authorize(): bool
    {
        $encounter = $this->route('encounter');

        return $encounter instanceof ClinicalEncounter
            && ($this->user()?->can('create', [EncounterSymptom::class, $encounter]) ?? false);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'severity' => ['required', Rule::enum(SymptomSeverity::class)],
            'started_at' => ['nullable', 'date', 'before_or_equal:now'],
            'duration' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
