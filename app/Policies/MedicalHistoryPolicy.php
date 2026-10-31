<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\MedicalHistory;
use App\Models\User;

class MedicalHistoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Nurse, UserRole::Clinician);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MedicalHistory $medicalHistory): bool
    {
        return $user->is_active && (
            $medicalHistory->patientProfile->user_id === $user->id
            || $this->viewAny($user)
        );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_active
            && $user->hasRole(UserRole::Patient)
            && $user->patientProfile()->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicalHistory $medicalHistory): bool
    {
        return $user->is_active
            && $medicalHistory->patientProfile->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicalHistory $medicalHistory): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicalHistory $medicalHistory): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MedicalHistory $medicalHistory): bool
    {
        return false;
    }
}
