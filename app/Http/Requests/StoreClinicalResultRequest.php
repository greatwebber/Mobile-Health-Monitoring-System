<?php

namespace App\Http\Requests;

use App\Enums\ClinicalResultCategory;
use App\Enums\ClinicalResultFlag;
use App\Models\ClinicalEncounter;
use App\Models\ClinicalResult;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreClinicalResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        $encounter = $this->route('encounter');

        return $encounter instanceof ClinicalEncounter
            && ($this->user()?->can('create', [ClinicalResult::class, $encounter]) ?? false);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'category' => ['required', Rule::enum(ClinicalResultCategory::class)],
            'test_name' => ['required', 'string', 'max:160'],
            'test_code' => ['nullable', 'string', 'max:40'],
            'value_numeric' => ['nullable', 'numeric', 'between:-9999999999,9999999999'],
            'value_text' => ['nullable', 'string', 'max:500'],
            'unit' => ['nullable', 'string', 'max:40'],
            'reference_range' => ['nullable', 'string', 'max:120'],
            'flag' => ['required', Rule::enum(ClinicalResultFlag::class)],
            'specimen' => ['nullable', 'string', 'max:120'],
            'collected_at' => ['nullable', 'date', 'before_or_equal:resulted_at'],
            'resulted_at' => ['required', 'date', 'before_or_equal:now'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /** @return array<callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if (blank($this->input('value_numeric')) && blank($this->input('value_text'))) {
                $validator->errors()->add('result_value', 'Enter a numeric or text result value.');
            }
        }];
    }
}
