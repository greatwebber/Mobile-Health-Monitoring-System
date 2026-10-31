<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\PatientProfile;
use App\Models\User;

class PatientProfilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_active && $user->hasRole(
            UserRole::Nurse,
            UserRole::Clinician,
            UserRole::Administrator,
        );
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PatientProfile $patientProfile): bool
    {
        return $user->is_active && (
            $patientProfile->user_id === $user->id
            || $this->viewAny($user)
        );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_active && (
            $user->hasRole(UserRole::Administrator)
            || ($user->hasRole(UserRole::Patient) && ! $user->patientProfile()->exists())
        );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PatientProfile $patientProfile): bool
    {
        return $user->is_active && (
            $patientProfile->user_id === $user->id
            || $user->hasRole(UserRole::Administrator)
        );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PatientProfile $patientProfile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PatientProfile $patientProfile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PatientProfile $patientProfile): bool
    {
        return false;
    }
}
