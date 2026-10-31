<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\StaffProfile;
use App\Models\User;

class StaffProfilePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Administrator);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StaffProfile $staffProfile): bool
    {
        return $user->is_active && (
            $staffProfile->user_id === $user->id
            || $user->hasRole(UserRole::Administrator)
        );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StaffProfile $staffProfile): bool
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StaffProfile $staffProfile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StaffProfile $staffProfile): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StaffProfile $staffProfile): bool
    {
        return false;
    }
}
