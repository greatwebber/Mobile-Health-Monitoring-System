<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreManagedAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', User::class) ?? false;
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in([
                UserRole::Nurse->value,
                UserRole::Clinician->value,
                UserRole::Administrator->value,
            ])],
            'password' => ['required', 'confirmed', Password::defaults()],
            'professional_title' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:255', 'unique:staff_profiles,license_number'],
            'phone' => ['nullable', 'string', 'max:32', 'regex:/^\+?[0-9][0-9\s\-()]{6,30}$/'],
        ];
    }
}
