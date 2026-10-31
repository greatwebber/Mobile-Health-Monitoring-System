<?php

namespace App\Policies;

use App\Enums\AlertStatus;
use App\Enums\UserRole;
use App\Models\ClinicalAlert;
use App\Models\User;

class ClinicalAlertPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Nurse, UserRole::Clinician);
    }

    public function view(User $user, ClinicalAlert $clinicalAlert): bool
    {
        return $user->is_active && (
            $clinicalAlert->patientProfile->user_id === $user->id
            || $this->viewAny($user)
        );
    }

    public function acknowledge(User $user, ClinicalAlert $clinicalAlert): bool
    {
        return $this->viewAny($user) && $clinicalAlert->status === AlertStatus::Open;
    }

    public function resolve(User $user, ClinicalAlert $clinicalAlert): bool
    {
        return $user->is_active
            && $user->hasRole(UserRole::Clinician)
            && $clinicalAlert->status !== AlertStatus::Resolved;
    }

    public function update(User $user, ClinicalAlert $clinicalAlert): bool
    {
        return false;
    }

    public function delete(User $user, ClinicalAlert $clinicalAlert): bool
    {
        return false;
    }
}
