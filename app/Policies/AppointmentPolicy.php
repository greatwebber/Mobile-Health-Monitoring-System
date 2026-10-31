<?php

namespace App\Policies;

use App\Enums\AppointmentStatus;
use App\Enums\UserRole;
use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Nurse, UserRole::Clinician);
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return $user->is_active && (
            $appointment->patientProfile->user_id === $user->id
            || $this->viewAny($user)
        );
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $this->viewAny($user) && $appointment->status === AppointmentStatus::Scheduled;
    }

    public function transition(User $user, Appointment $appointment): bool
    {
        return $this->update($user, $appointment);
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return false;
    }
}
