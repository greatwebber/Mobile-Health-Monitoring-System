<?php

namespace App\Http\Requests;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class TransitionAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $appointment = $this->route('appointment');

        return $appointment instanceof Appointment
            && ($this->user()?->can('transition', $appointment) ?? false);
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in([
                AppointmentStatus::Completed->value,
                AppointmentStatus::Cancelled->value,
                AppointmentStatus::NoShow->value,
            ])],
            'outcome_notes' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    /** @return array<callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $appointment = $this->route('appointment');
            $status = AppointmentStatus::tryFrom((string) $this->input('status'));

            if ($appointment instanceof Appointment
                && in_array($status, [AppointmentStatus::Completed, AppointmentStatus::NoShow], true)
                && $appointment->scheduled_at->isFuture()) {
                $validator->errors()->add('status', 'A future appointment cannot be completed or marked as not attended.');
            }
        }];
    }
}
