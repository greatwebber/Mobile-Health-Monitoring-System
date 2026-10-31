<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateManagedAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        $managedUser = $this->route('managedUser');

        return $managedUser instanceof User
            && ($this->user()?->can('update', $managedUser) ?? false);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        $managedUser = $this->route('managedUser');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($managedUser)],
            'is_active' => ['required', 'boolean'],
            'professional_title' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'license_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('staff_profiles', 'license_number')->ignore($managedUser?->staffProfile?->id),
            ],
            'phone' => ['nullable', 'string', 'max:32', 'regex:/^\+?[0-9][0-9\s\-()]{6,30}$/'],
        ];
    }

    /** @return array<callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $managedUser = $this->route('managedUser');

            if ($managedUser instanceof User
                && $managedUser->is($this->user())
                && ! $this->boolean('is_active')) {
                $validator->errors()->add('is_active', 'You cannot deactivate your own administrator account.');
            }
        }];
    }
}
