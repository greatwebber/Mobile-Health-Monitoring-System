<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\ThresholdRule;
use App\Models\User;

class ThresholdRulePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Administrator, UserRole::Clinician);
    }

    public function view(User $user, ThresholdRule $thresholdRule): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Administrator);
    }

    public function approve(User $user, ThresholdRule $thresholdRule): bool
    {
        return $user->is_active
            && $user->hasRole(UserRole::Clinician)
            && $thresholdRule->approved_at === null
            && $thresholdRule->deactivated_at === null;
    }

    public function deactivate(User $user, ThresholdRule $thresholdRule): bool
    {
        return $user->is_active
            && $user->hasRole(UserRole::Clinician)
            && $thresholdRule->is_active;
    }

    public function update(User $user, ThresholdRule $thresholdRule): bool
    {
        return false;
    }

    public function delete(User $user, ThresholdRule $thresholdRule): bool
    {
        return false;
    }
}
