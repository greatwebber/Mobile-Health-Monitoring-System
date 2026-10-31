<?php

namespace App\Http\Requests;

use App\Models\Appointment;

class UpdateAppointmentRequest extends StoreAppointmentRequest
{
    public function authorize(): bool
    {
        $appointment = $this->route('appointment');

        return $appointment instanceof Appointment
            && ($this->user()?->can('update', $appointment) ?? false);
    }
}
