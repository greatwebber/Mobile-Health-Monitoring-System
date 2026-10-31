<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ClinicalEncounter;
use App\Models\User;

class ClinicalEncounterPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Nurse, UserRole::Clinician);
    }

    public function view(User $user, ClinicalEncounter $clinicalEncounter): bool
    {
        return $user->is_active && (
            $clinicalEncounter->patientProfile->user_id === $user->id
            || $this->viewAny($user)
        );
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, ClinicalEncounter $clinicalEncounter): bool
    {
        return $this->viewAny($user) && ! $clinicalEncounter->status->isFinal();
    }

    public function delete(User $user, ClinicalEncounter $clinicalEncounter): bool
    {
        return false;
    }

    public function restore(User $user, ClinicalEncounter $clinicalEncounter): bool
    {
        return false;
    }

    public function forceDelete(User $user, ClinicalEncounter $clinicalEncounter): bool
    {
        return false;
    }
}
