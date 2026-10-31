<?php

namespace App\Policies;

use App\Models\ClinicalEncounter;
use App\Models\EncounterSymptom;
use App\Models\User;

class EncounterSymptomPolicy
{
    public function view(User $user, EncounterSymptom $encounterSymptom): bool
    {
        return $user->can('view', $encounterSymptom->clinicalEncounter);
    }

    public function create(User $user, ClinicalEncounter $clinicalEncounter): bool
    {
        return $user->can('update', $clinicalEncounter);
    }

    public function update(User $user, EncounterSymptom $encounterSymptom): bool
    {
        return false;
    }

    public function delete(User $user, EncounterSymptom $encounterSymptom): bool
    {
        return false;
    }
}
