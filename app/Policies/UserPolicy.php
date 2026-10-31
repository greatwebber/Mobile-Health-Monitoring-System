<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active && $user->hasRole(UserRole::Administrator);
    }

    public function view(User $user, User $managedUser): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function update(User $user, User $managedUser): bool
    {
        return $this->viewAny($user);
    }

    public function delete(User $user, User $managedUser): bool
    {
        return false;
    }
}
