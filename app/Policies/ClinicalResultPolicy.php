<?php

namespace App\Policies;

use App\Models\ClinicalEncounter;
use App\Models\ClinicalResult;
use App\Models\User;

class ClinicalResultPolicy
{
    public function view(User $user, ClinicalResult $clinicalResult): bool
    {
        return $user->can('view', $clinicalResult->clinicalEncounter);
    }

    public function create(User $user, ClinicalEncounter $clinicalEncounter): bool
    {
        return $user->can('update', $clinicalEncounter);
    }

    public function update(User $user, ClinicalResult $clinicalResult): bool
    {
        return false;
    }

    public function delete(User $user, ClinicalResult $clinicalResult): bool
    {
        return false;
    }
}
