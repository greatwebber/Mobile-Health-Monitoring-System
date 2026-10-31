<?php

namespace App\Policies;

use App\Models\ClinicalEncounter;
use App\Models\User;
use App\Models\VitalSignObservation;

class VitalSignObservationPolicy
{
    public function view(User $user, VitalSignObservation $vitalSignObservation): bool
    {
        return $user->can('view', $vitalSignObservation->clinicalEncounter);
    }

    public function create(User $user, ClinicalEncounter $clinicalEncounter): bool
    {
        return $user->can('update', $clinicalEncounter);
    }

    public function update(User $user, VitalSignObservation $vitalSignObservation): bool
    {
        return false;
    }

    public function delete(User $user, VitalSignObservation $vitalSignObservation): bool
    {
        return false;
    }
}
